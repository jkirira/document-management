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
            'email' => $data['email'],
            'name' => $data['name'],
            'department_id' => $data['department_id'],
        ]);

        if ($user) {

            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

        }

        return $user->fresh();

    }

    public function updateUser(User $user, $data)
    {
        $userInfo = Arr::except($data, ['roles']);

        $user->update($userInfo);

        if(isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user->fresh();

    }

}
