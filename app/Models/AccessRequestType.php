<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessRequestType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'access_request_types';

    protected $guarded = [
        'id',
    ];

}
