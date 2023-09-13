<?php
namespace App\Transformers\Admin;

use App\Models\DocumentAccess;

class UserAccessTransformer
{
    public function transform(DocumentAccess $documentAccess)
    {
        return [
            'id' => $documentAccess->id,
            'document' => [
                'id' => $documentAccess->document_id,
                'name' => $documentAccess->document->name,
            ],
            'user' => $documentAccess->user
                                ? (new UserTransformer())->transform($documentAccess->user)
                                : null,
            'update' => (bool)$documentAccess->update,
            'view' => (bool)$documentAccess->view,
            'delete' => (bool)$documentAccess->delete,
            'download' => (bool)$documentAccess->download,
            'revoked' => (bool)$documentAccess->revoked,
            'granted_by' => $documentAccess->granted_by,
            'revoked_by' => $documentAccess->revoked_by,
            'upload_proof' => $documentAccess->upload_proof,
            'access_request_id' => $documentAccess->access_request_id,
        ];
    }
}
