<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'folders';

    protected $guarded = [
        'id',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function parentFolder()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function childFolders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function scopeAccessibleToEveryone($query, $ability=null)
    {
        return $query->whereHas('documents', function ($documents) use ($ability) {
                        $documents->accessibleToEveryone($ability);
                    });
    }

    public function scopeAccessibleToDepartment($query, $department, $ability=null)
    {
        return $query->whereHas('documents', function ($documents) use ($department, $ability) {
                        $documents->accessibleToDepartment($department, $ability);
                    });
    }

    public function scopeAccessibleToRole($query, $role, $ability=null)
    {
        return $query->whereHas('documents', function ($documents) use ($role, $ability) {
                        $documents->accessibleToRole($role, $ability);
                    });
    }

    public function scopeAccessibleToRoles($query, $roles, $ability=null)
    {
        return $query->whereHas('documents', function ($documents) use ($roles, $ability) {
                        $documents->accessibleToRoles($roles, $ability);
                    });
    }

    public function scopeAccessibleToUser($query, $user, $ability=null)
    {
        return $query->whereHas('documents', function ($documents) use ($user, $ability) {
                        $documents->accessibleToUser($user, $ability);
                    });
    }

}
