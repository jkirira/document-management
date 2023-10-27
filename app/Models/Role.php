<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Role extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $table = 'roles';

    protected $guarded = [
        'id',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 1,
        ],
    ];

    const ADMIN_ROLE_SLUG = 'admin';


    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
    }

    /*
    * CUSTOM SEARCH
    */
    // public function scopeSearch($query, string $terms = null)
    // {
    //     $columns = array_filter($this->searchColumns);
    //     $terms = array_filter(explode(' ', $terms));

    //     $query->where(function ($query) use ($terms, $columns) {
    //         foreach ($columns as $column) {
    //             $query->orWhere(function ($query) use ($terms, $column) {
    //                 foreach ($terms as $term) {
    //                     $query->orWhere($column, 'like', '%'.$term.'%');
    //                 };
    //             });
    //         };
    //     });
    // }

}
