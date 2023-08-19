<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'access_requests';

    protected $guarded = [
        'id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function type()
    {
        return $this->belongsTo(AccessRequestType::class, 'type_id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

}
