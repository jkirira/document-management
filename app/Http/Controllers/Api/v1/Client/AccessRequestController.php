<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AccessRequestRequest;
use App\Models\AccessRequest;
use App\Notifications\AccessRequestGranted;
use App\Notifications\AccessRequestMade;
use App\Notifications\AccessRequestRejected;
use App\Services\AccessRequestService;
use App\Services\DocumentAccessService;
use App\Transformers\Client\AccessRequestTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AccessRequestController extends Controller
{
    public function index()
    {
        // add maybe only admin authorization
        $accessRequests = AccessRequest::with(['document', 'requestedBy', 'rejectedBy', 'grantedBy'])
                                    ->get()
                                    ->map(function($accessRequest) {
                                        return (new AccessRequestTransformer())->transform($accessRequest);
                                    });

        return response()->json($accessRequests, Response::HTTP_OK);
    }

    public function store(AccessRequestRequest $request)
    {
        $this->authorize('create', AccessRequest::class);

        $input = $request->all();
        $accessRequest = (new AccessRequestService())->addNewAccessRequest($input);

        // send notifications
        $accessRequest->load(['document.owner', 'document.accessManagers']);

        $accessManagers = $accessRequest->document->accessManagers;
        $documentOwner = $accessRequest->document->owner;

        $notificationRecipients = collect($accessManagers)
                                        ->push($documentOwner)
                                        ->unique('email');

        foreach ($notificationRecipients as $recipient) {
            $recipient->notify(new AccessRequestMade($accessRequest));
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(AccessRequest $accessRequest)
    {
        $this->authorize('view', $accessRequest);
        $accessRequest->load(['document', 'requestedBy', 'rejectedBy', 'grantedBy']);
        return response()->json((new AccessRequestTransformer())->transform($accessRequest), Response::HTTP_OK);
    }

    public function update(AccessRequestRequest $request, AccessRequest $accessRequest)
    {
        $this->authorize('update', $accessRequest);

        $input = $request->all();
        $accessRequest = (new AccessRequestService())->updateAccess($accessRequest, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(AccessRequest $accessRequest)
    {
        $this->authorize('delete', $accessRequest);

        $accessRequest->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function approve(Request $request, AccessRequest $accessRequest)
    {
        Gate::authorize('approve-access-request', $accessRequest);

        $accessRequest = DB::transaction(function () use ($accessRequest) {

            $document = $accessRequest->document;

            $access_values = [
                'user_id' => $accessRequest->requested_by,
                'view' => true,
                'expires_at' => $accessRequest->expiry_time,
            ];

            $access = (new DocumentAccessService())->grantAccess($document, $access_values);

            $access->accessRequest()->associate($accessRequest);
            $access->save();

            $accessRequest->update([
                'granted' => true,
                'granted_by' => auth()->id(),
            ]);

            return $accessRequest;

        });

        if (isset($accessRequest->requestedBy)) {
            $accessRequest->requestedBy->notify(new AccessRequestGranted($accessRequest->refresh()));
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    public function reject(Request $request, AccessRequest $accessRequest)
    {
        Gate::authorize('reject-access-request', $accessRequest);

        $accessRequest->update([
            'rejected' => true,
            'rejected_by' => auth()->id(),
            'rejected_reason' => $request->rejected_reason,
        ]);

        if (isset($accessRequest->requestedBy)) {
            $accessRequest->requestedBy->notify(new AccessRequestRejected($accessRequest->refresh()));
        }

        return response()->json([], Response::HTTP_OK);
    }

}
