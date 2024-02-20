<?php
namespace App\Transformers\Client;

use App\Models\Document;

class DocumentTransformer
{
    public function transformForHistory(Document $document)
    {
        return [
            'id' => $document->id,
            'name' => $document->name,
            'path' => $document->path,
            'folder' => isset($document->folder)
                        ?   [
                            'id' => $document->folder_id,
                            'name' => $document->folder->name,
                        ]
                        : null,
            'owner' => isset($document->owner)
                        ?   [
                            'id' => $document->owner->id,
                            'name' => $document->owner->name,
                        ]
                        : null,
            'created_at' => $document->created_at,
            'deleted' => isset($document->deleted_at),
            'deleted_at' => $document->deleted_at,
            'deleted_by' => isset($document->deletedBy)
                            ?   [
                                'id' => $document->deleted_by,
                                'name' => $document->deletedBy->name,
                            ]
                            : null,
        ];
    }
}
