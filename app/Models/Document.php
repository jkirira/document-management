<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected $guarded = [
        'id',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_documents');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

}
