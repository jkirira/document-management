<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, SearchableTrait;

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 10,
            'email' => 10,
        ],
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function categories()
    {
        return $this->hasMany(UserCategory::class, 'user_id');
    }

    public function isAdmin()
    {
        $adminRole = Role::where('slug', Role::ADMIN_ROLE_SLUG)->first();
        return $this->roles()->where('role_id', $adminRole->id)->exists();
    }

    public function isDocumentOwner(Document $document)
    {
        return $document->added_by === $this->id;
    }

    public function isFolderOwner(Folder $folder)
    {
        return $folder->added_by === $this->id;
    }

    public function isUserCategoryOwner(UserCategory $userCategory)
    {
        return $userCategory->user_id === $this->id;
    }

    public function hasRole(Role $role)
    {
        return $this->roles()->where('role_id', $role->id)->exists();
    }

    public function isDocumentAccessManager(Document $document)
    {
        return $document->accessManagers()->where('user_id', $this->id)->exists();
    }

}
