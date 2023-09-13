<?php


namespace App\Services;


use App\Models\AccessRequest;
use App\Models\DocumentAccess;
use Illuminate\Foundation\Auth\User;

class AccessRequestService
{

    public function addNewAccessRequest ($input, User $requestedBy=null)
    {
//                'id' => '',
//                'document_id' => '',
//                'requested_by' => '',
//                'description' => '',
//                'type_id' => ''
//                'granted' => ''
//                'granted_by' => ''
//                'rejected_by' => ''
//                'rejected_reason' => ''

        $document = DocumentAccess::findOrFail($input['document_id']);

        if (!isset($requestedBy)) {
            $requestedBy = auth()->user();
        }

        return AccessRequest::create([
            "document_id" => $document->id,
            "requested_by" => $requestedBy ? $requestedBy->id : auth()->id(),
            'description' => isset($input['description']) ? $input['description'] : null,
        ]);

    }

}
