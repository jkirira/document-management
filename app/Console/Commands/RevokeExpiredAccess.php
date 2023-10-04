<?php

namespace App\Console\Commands;

use App\Models\DocumentAccess;
use App\Notifications\DocumentAccessExpired;
use App\Services\DocumentAccessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RevokeExpiredAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access:revoke-expired
                            {--after= : Optional expiry date formatted as Y-m-d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke Access that have expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting command RevokeExpiredAccess');

        $after = $this->option('after') ? Carbon::parse($this->option('after')) : Carbon::now();

        $expiredAccesses = DocumentAccess::with(['user', 'document'])
                                        ->active()
                                        ->where('expires_at', '<=', $after)
                                        ->get();

//        dd($expiredAccesses);

        $documentAccessService = new DocumentAccessService();

        foreach ($expiredAccesses as $expiredAccess) {
            $this->info('Access id - ' . $expiredAccess->id);

            try {

                $expiredAccess = DB::transaction(function() use ($documentAccessService, $expiredAccess) {
                    $expiredAccess->update(['expired' => true]);
                    return $documentAccessService->revokeAccess($expiredAccess);
                });

                $expiredAccess->refresh();

                $user = $expiredAccess->user;
                if (isset($user)) {
                    $user->notify(new DocumentAccessExpired($expiredAccess));
                }

            } catch (\Exception $exception) {
                if (!$expiredAccess->fresh()->expired) {
                    $this->error('Revoke Access id - ' . $expiredAccess->id . 'failed');
                } else {
                    $this->error('Revoke Access id - ' . $expiredAccess->id . 'something went wrong');
                }
            }

        }

        $this->info('Stopped command RevokeExpiredAccess');

    }
}
