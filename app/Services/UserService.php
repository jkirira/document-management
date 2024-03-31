<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Arr;

class UserService
{
    public $user;

    public function __construct(User $user=null)
    {
        $this->user = $user;
    }

    public function createUser($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'department_id' => $data['department_id'],
        ]);

        if ($user) {

            if (isset($data['role_ids'])) {
                $user->roles()->sync($data['role_ids']);
            }

        }

        return $user->fresh();

    }

    public function updateUser(User $user, $data)
    {
        $user->update([
            'name' => isset($data['name']) ? $data['name'] : $user->name,
            'email' => isset($data['email']) ? $data['email'] : $user->email,
            'department_id' => isset($data['department_id']) ? $data['department_id'] : $user->department_id,
        ]);

        if(isset($data['role_ids'])) {
            $user->roles()->sync($data['role_ids']);
        }

        return $user->fresh();

    }

}
