<?php
namespace App\Transformers\Admin;

use App\Models\AccessRequest;

class AccessRequestTransformer
{
    public function transform(AccessRequest $accessRequest)
    {
        return [
            'id' => $accessRequest->id,
            'document' => [
                'id' => $accessRequest->document_id,
                'name' => $accessRequest->document->name,
            ],
            'requested_by' => [
                'id' => $accessRequest->requested_by,
                'name' => $accessRequest->requestedBy->name,
            ],
            'description' => $accessRequest->description,
            'granted' => (bool)$accessRequest->granted,
            'granted_by' => $accessRequest->grantedBy,
            'rejected_by' => $accessRequest->rejectedBy,
            'rejected_reason' => $accessRequest->rejected_reason,
        ];

    }

}
