<?php

namespace App\Console\Commands;

use App\Models\DocumentAccess;
use App\Notifications\AccessExpiring;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAccessExpiringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access:send-access-expiring-reminders {--six-hour-reminders} {--twelve-hour-reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to user and access granter about expiring access';

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
        $this->info('Starting command SendAccessExpiringReminders');

        if ($this->option('six-hour-reminders')) {
            $this->sendSixHourReminders();
        }

        if ($this->option('twelve-hour-reminders')) {
            $this->sendTwelveHourReminders();
        }

        if (!$this->option('six-hour-reminders') && !$this->option('twelve-hour-reminders')) {
            $this->sendReminders();
        }

        $this->info('Stopped command SendAccessExpiringReminders');

    }

    public function sendReminders()
    {

        $accesses = DocumentAccess::with(['user', 'document', 'grantedBy'])
                                    ->active()
                                    ->notExpired()
                                    ->whereNotNull('expires_at')
                                    ->get();

        foreach ($accesses as $access) {
            $this->info('Access id - ' . $access->id);

            $notified = false;
            try {
                $user = $access->user;
                if (isset($user)) {
                    $user->notify(new AccessExpiring($access));
                }

                $granter = $access->grantedBy;
                if (isset($granter)) {
                    $granter->notify(new AccessExpiring($access));
                }

                $notified = true;

            } catch (\Exception $exception) {
                $this->error('Access id - ' . $access->id . ' notifications failed');
            }

            if ($notified) {
                $access->update(['expiry_notified_at' => Carbon::now()]);
            }

        }

    }

    public function sendSixHourReminders()
    {

        $accessesWith6HoursLeft = DocumentAccess::with(['user', 'document', 'grantedBy'])
                                                ->active()
                                                ->notExpired()
                                                ->where('expires_at', '<=', Carbon::now()->addHours(6))
                                                ->where(function($query) {
                                                    /*
                                                    *   only notify if hasn't been notified yet
                                                    *   or
                                                    *   if last notified more than 6 hours ago
                                                    */
                                                    $query->whereNull('expiry_notified_at')
                                                            ->orWhere('expiry_notified_at', '<', Carbon::now()->subHours(6));
                                                })
                                                ->get();

        foreach ($accessesWith6HoursLeft as $access) {
            $this->info('Access id - ' . $access->id);

            $notified = false;
            try {
                $user = $access->user;
                if (isset($user)) {
                    $user->notify(new AccessExpiring($access));
                }

                $granter = $access->grantedBy;
                if (isset($granter)) {
                    $granter->notify(new AccessExpiring($access));
                }

                $notified = true;

            } catch (\Exception $exception) {
                $this->error('Access id - ' . $access->id . ' notifications failed');
            }

            if ($notified) {
                $access->update(['expiry_notified_at' => Carbon::now()]);
            }

        }

    }

    public function sendTwelveHourReminders()
    {

        $accessesWith12HoursLeft = DocumentAccess::with(['user', 'document', 'grantedBy'])
                                                ->active()
                                                ->notExpired()
                                                ->where('expires_at', '<=', Carbon::now()->addHours(12))
                                                ->where(function($query) {
                                                    /*
                                                    *   only notify if hasn't been notified yet
                                                    *   or
                                                    *   if last notified more than 12 hours ago
                                                    */
                                                    $query->whereNull('expiry_notified_at')
                                                          ->orWhere('expiry_notified_at', '<', Carbon::now()->subHours(12));
                                                })
                                                ->get();

        foreach ($accessesWith12HoursLeft as $access) {
            $this->info('Access id - ' . $access->id);

            $notified = false;
            try {
                $user = $access->user;
                if (isset($user)) {
                    $user->notify(new AccessExpiring($access));
                }

                $granter = $access->grantedBy;
                if (isset($granter)) {
                    $granter->notify(new AccessExpiring($access));
                }

                $notified = true;

            } catch (\Exception $exception) {
                $this->error('Access id - ' . $access->id . ' notifications failed');
            }

            if ($notified) {
                $access->update(['expiry_notified_at' => Carbon::now()]);
            }

        }

    }

}
