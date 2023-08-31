<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public $user;

    public function __construct(User $user=null)
    {
        $this->user = $user;
    }

    public function createUser($data)
    {

        $password = Str::random(8);

        $user = User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => Hash::make($password),
        ]);

        if ($user) {

            if (isset($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            if (isset($data['department_id'])) {
                $user->department()->associate($data['department_id']);
                $user->save();
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
