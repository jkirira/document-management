<?php
namespace App\Transformers\Client;

use App\Models\User;

class UserTransformer
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department' => isset($user->department)
                            ? [
                                'id' => $user->department->id,
                                'name' => $user->department->name,
                            ]
                            : null,
        ];
    }
}
