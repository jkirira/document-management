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

    const ACCESS_TYPES = [
        'update',
        'view',
        'delete',
        'download',
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

}
