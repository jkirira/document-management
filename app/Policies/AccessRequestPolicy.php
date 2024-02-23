<?php

namespace App\Policies;

use App\Models\AccessRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccessRequestPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccessRequest  $accessRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AccessRequest $accessRequest)
    {
        $document = $accessRequest->document;
        return  ($accessRequest->requested_by === $user->id) ||
                $user->isAdmin() ||
                $user->isDocumentAccessManager($document);

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccessRequest  $accessRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AccessRequest $accessRequest)
    {
        $document = $accessRequest->document;
        return  ($accessRequest->requested_by === $user->id) ||
            $user->isAdmin() ||
            $user->isDocumentAccessManager($document);

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccessRequest  $accessRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AccessRequest $accessRequest)
    {
        $document = $accessRequest->document;
        return  ($accessRequest->requested_by === $user->id) ||
            $user->isAdmin() ||
            $user->isDocumentAccessManager($document);

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccessRequest  $accessRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AccessRequest $accessRequest)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AccessRequest  $accessRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AccessRequest $accessRequest)
    {
        return $user->isAdmin();
    }
}
