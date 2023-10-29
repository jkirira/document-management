<?php
namespace App\Transformers\Admin;

use App\Models\User;

class UserTransformer
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => (bool)count($user->roles)
                        ? $user->roles->map(function($role) {
                                            return [
                                                'id' => $role->id,
                                                'name' => $role->name,
                                            ];
                                        })
                        : [],
            'department' => isset($user->department)
                            ? [
                                'id' => $user->department->id,
                                'name' => $user->department->name,
                            ]
                            : [],
        ];
    }
}
