<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_categories';

    protected $guarded = [
        'id',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'category_documents');
    }

    public function scopeUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

}
