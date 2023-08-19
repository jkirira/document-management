<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    use HasFactory;

    protected $table = 'document_role_access';

    protected $guarded = [
        'id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
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
