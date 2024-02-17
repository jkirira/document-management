<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Department extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $table = 'departments';

    protected $guarded = [
        'id',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 1,
        ],
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'department_users');
    }

}
