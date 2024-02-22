<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected $guarded = [
        'id',
    ];

    public function categories()
    {
        return $this->belongsToMany(UserCategory::class, 'category_documents');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function accessManagers()
    {
        return $this->belongsToMany(User::class, 'document_access_managers', 'document_id', 'user_id');
    }

    public function access()
    {
        return $this->hasMany(DocumentAccess::class, 'document_id');
    }

    //    Documents::accessibleToEveryone()->get();
    public function scopeAccessibleToEveryone($query, $ability=null)
    {
        return $query->whereHas('access', function ($access) use ($ability) {
                        $access->where('all_departments', true)
                                ->where('all_roles', true)
                                ->active()
                                ->notExpired()
                                ->when($ability, function ($query, $ability) {
                                    return $query->withAbilityTo($ability);
                                });
                });
    }

    //    Documents::accessibleToUser()->get();
    public function scopeAccessibleToUser($query, $user, $ability=null)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $department = $user->department;
        $roleIds = $user->roles->pluck('id');

        return $query->where('added_by', $user->id)
                    ->orWhereHas('access', function ($access) use ($department, $roleIds, $user, $ability) {
                        $access->active()
                                ->notExpired()
                                ->when($ability, function ($query, $ability) {
                                    return $query->withAbilityTo($ability);
                                })
                                ->where(function ($query) use ($department, $roleIds, $user) {
                                    $query->where(function ($query) {
                                                $query->where('all_departments', true)->where('all_roles', true);
                                            })
                                            ->orWhere(function ($query) use ($department, $roleIds) {
                                                $query->where('all_departments', true)->whereIn('role_id', $roleIds);
                                            })
                                            ->orWhere(function ($query) use ($user) {
                                                $query->where('user_id', $user->id);
                                            })
                                            ->when($department, function ($query, $department) {
                                                return $query->orWhere(function ($query) use ($department) {
                                                                $query->where('department_id', $department->id)->where('all_roles', true);
                                                            });
                                            })
                                            ->when((isset($department) && count($roleIds)), function ($query) use ($department, $roleIds) {
                                                return $query->orWhere(function ($query) use ($department, $roleIds) {
                                                                $query->where('department_id', $department->id)->whereIn('role_id', $roleIds);
                                                            });
                                            });
                                });
                    });
    }

}
