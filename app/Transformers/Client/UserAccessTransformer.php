<?php
namespace App\Transformers\Client;

use App\Models\DocumentAccess;
use App\Transformers\Admin\UserTransformer;

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
            'user' => isset($documentAccess->user)
                                ? (new UserTransformer())->transform($documentAccess->user)
                                : null,
            'update' => (bool)$documentAccess->update,
            'view' => (bool)$documentAccess->view,
            'delete' => (bool)$documentAccess->delete,
            'download' => (bool)$documentAccess->download,
            'revoked' => (bool)$documentAccess->revoked,
            'granted_by' => isset($documentAccess->grantedBy)
                                ?   [
                                    'id' => $documentAccess->grantedBy->id,
                                    'name' => $documentAccess->grantedBy->name,
                                ]
                                : null,
            'revoked_by' => isset($documentAccess->revokedBy)
                                ?   [
                                    'id' => $documentAccess->revokedBy->id,
                                    'name' => $documentAccess->revokedBy->name,
                                ]
                                : null,
            'upload_proof' => $documentAccess->upload_proof,
            'access_request_id' => $documentAccess->access_request_id,
        ];
    }
}
