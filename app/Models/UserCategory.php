<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class UserCategory extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $table = 'user_categories';

    protected $guarded = [
        'id',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 1,
        ],
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'category_documents');
    }

    public function scopeForUser($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

}
