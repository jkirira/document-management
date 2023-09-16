<?php


namespace App\Services;


use App\Models\AccessRequest;
use App\Models\DocumentAccess;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;

class AccessRequestService
{

    public function addNewAccessRequest ($input, User $requestedBy=null)
    {
        $document = DocumentAccess::findOrFail($input['document_id']);

        $expiry_time = null;
        if (isset($input['expiry_date']) && isset($input['expiry_time'])) {
//            Carbon::createFromFormat('Y-m-d H', '1975-05-21 22')->toDateTimeString(); // 1975-05-21 22:00:00
            $expiry_time = Carbon::parse($input['expiry_date'])
                                ->setTimeFromTimeString($input['expiry_time'])
                                ->toDateTimeString();
        }

        if (!isset($requestedBy)) {
            $requestedBy = auth()->user();
        }

        return AccessRequest::create([
                    "document_id" => $document->id,
                    "requested_by" => $requestedBy ? $requestedBy->id : auth()->id(),
                    'description' => isset($input['description']) ? $input['description'] : null,
                    'expiry_time' => $expiry_time,
                ]);

    }

    public function updateAccess(AccessRequest $accessRequest, $values)
    {
        $expiry_time = null;
        if (isset($input['expiry_date']) && isset($input['expiry_time'])) {
            $expiry_time = Carbon::parse($input['expiry_date'])
                                ->setTimeFromTimeString($input['expiry_time'])
                                ->toDateTimeString();
        }

        $accessRequest->update([
            'description' => isset($values['description']) ? $values['description'] : $accessRequest->description,
            'expiry_time' => isset($expiry_time) ? $expiry_time : $accessRequest->expiry_time,
        ]);

        return $accessRequest;

    }

}
