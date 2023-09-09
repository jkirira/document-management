<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use App\Policies\Admin\DepartmentPolicy;
use App\Policies\Admin\DocumentPolicy;
use App\Policies\Admin\RolePolicy;
use App\Policies\Admin\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
         User::class => UserPolicy::class,
         Role::class => RolePolicy::class,
         Department::class => DepartmentPolicy::class,
         Document::class => DocumentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-access-managers', function (User $user, Document $document) {
            return $user->isAdmin() || $user->canManageDocumentAccess($document);
        });

        Gate::define('grant-document-access', function (User $user, Document $document) {
            return  $user->isAdmin() ||
                    $user->isDocumentOwner($document) ||
                    $user->canManageDocumentAccess($document);
        });

    }
}
