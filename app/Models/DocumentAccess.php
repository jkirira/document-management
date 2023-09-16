<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_access';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'all_departments' => 'boolean',
        'all_roles' => 'boolean',
        'update' => 'boolean',
        'view' => 'boolean',
        'delete' => 'boolean',
        'download' => 'boolean',
        'expired' => 'boolean',
        'revoked' => 'boolean',
    ];

    const ACCESS_ABILITIES = [
        'update' => 'update',
        'view' => 'view',
        'delete' => 'delete',
        'download' => 'download',
    ];


    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class, 'access_request_id');
    }

    public function scopeWithAbilityTo($query, $ability)
    {
        if (!in_array($ability, $this::ACCESS_ABILITIES)) {
            return $query;
        }
        return $query->where($ability, true);
    }

    public function scopeNormalAccess($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeSpecialUserAccess($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('revoked')->orWhere('revoked', 0);
        });
    }

}
