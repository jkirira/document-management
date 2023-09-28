<?php
namespace App\Transformers\Admin;

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
            'department' => isset($documentAccess->department)
                                ?   [
                                        'id' => $documentAccess->department_id,
                                        'name' => $documentAccess->department->name,
                                    ]
                                : null,
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
            'all_departments' => (bool)$documentAccess->all_departments,
            'all_roles' => (bool)$documentAccess->all_roles,
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
