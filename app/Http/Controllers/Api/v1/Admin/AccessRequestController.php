<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccessRequestRequest;
use App\Models\AccessRequest;
use App\Services\AccessRequestService;
use App\Transformers\Admin\AccessRequestTransformer;
use Illuminate\Http\Response;

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
        (new AccessRequestService())->addNewAccessRequest($input);

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

        $accessRequest->update([
            'description' => isset($request->description) ? $request->description : $accessRequest->description,
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(AccessRequest $accessRequest)
    {
        $this->authorize('delete', $accessRequest);

        $accessRequest->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
