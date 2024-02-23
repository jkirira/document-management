<?php

namespace App\Providers;

use App\Models\AccessRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCategory;
use App\Policies\DepartmentPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserCategoryPolicy;
use App\Policies\UserPolicy;
use App\Policies\AccessRequestPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\FolderPolicy;
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
         Folder::class => FolderPolicy::class,
         AccessRequest::class => AccessRequestPolicy::class,
         UserCategory::class => UserCategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-document-access-managers', function (User $user, Document $document) {
            return $user->isAdmin() || $user->isDocumentAccessManager($document);
        });

        Gate::define('manage-document-access', function (User $user, Document $document) {
            return  $user->isAdmin() || $user->isDocumentAccessManager($document);
        });

        Gate::define('approve-access-request', function (User $user, AccessRequest $accessRequest) {
            $document = $accessRequest->document;
            $userIsNotRequestingUser = $user->id !== $accessRequest->requested_by;
            $userCanApproveRequest = ($user->isAdmin() || $user->isDocumentAccessManager($document));

            return (bool)(
                !$accessRequest->granted &&
                !$accessRequest->rejected &&
                $userIsNotRequestingUser &&
                $userCanApproveRequest
            );
        });

    }
}
