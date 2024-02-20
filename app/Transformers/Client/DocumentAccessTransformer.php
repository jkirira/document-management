<?php
namespace App\Transformers\Client;

use App\Models\DocumentAccess;

class DocumentAccessTransformer
{
    public function transform(DocumentAccess $documentAccess)
    {
        return [
            'id' => $documentAccess->id,
            'document' => [
                'id' => $documentAccess->document_id,
                'name' => $documentAccess->document->name,
            ],
            'all_departments' => (bool)$documentAccess->all_departments,
            'department' => isset($documentAccess->department)
                                ?   [
                                        'id' => $documentAccess->department_id,
                                        'name' => $documentAccess->department->name,
                                    ]
                                : null,
            'all_roles' => (bool)$documentAccess->all_roles,
            'role' => isset($documentAccess->role)
                        ?   [
                                'id' => $documentAccess->role_id,
                                'name' => $documentAccess->role->name,
                            ]
                        : null,
            'user' => isset($documentAccess->user)
                        ?   [
                                'id' => $documentAccess->user_id,
                                'name' => $documentAccess->user->name,
                            ]
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
