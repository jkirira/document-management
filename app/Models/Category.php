<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $guarded = [
        'id',
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'category_documents');
    }

}
