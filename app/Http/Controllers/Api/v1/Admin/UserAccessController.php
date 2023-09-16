<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserAccessRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Services\DocumentAccessService;
use App\Transformers\Admin\UserAccessTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserAccessController extends Controller
{
    public function index(Document $document)
    {
        // add maybe only admin authorization
        $userAccess = $document->access()
                                ->specialUserAccess()
                                ->get()
                                ->map(function($access) {
                                    return (new UserAccessTransformer())->transform($access);
                                });

        return response()->json($userAccess, Response::HTTP_OK);
    }

    public function store(UserAccessRequest $request, Document $document)
    {
        Gate::authorize('grant-document-access', $document);

        $accesses = $request->access;
        $accessService = new DocumentAccessService();
        foreach ($accesses as $access_values) {
            $accessService->grantAccess($document, $access_values);
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Document $document, $id)
    {
        // add maybe only admin authorization
        $access = $document->access()->specialUserAccess()->findOrFail($id);
        return response()->json((new UserAccessTransformer())->transform($access), Response::HTTP_OK);
    }

    public function update(UserAccessRequest $request, Document $document)
    {
        Gate::authorize('grant-document-access', $document);

        $accesses = $request->access;
        $accessService = new DocumentAccessService();
        foreach ($accesses as $access_values) {
            $documentAccess = DocumentAccess::findOrFail($access_values['id']);
            $accessService->updateAccess($documentAccess, $access_values);
        }

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Document $document, $id)
    {
        Gate::authorize('grant-document-access', $document);

        $access = $document->access()->specialUserAccess()->findOrFail($id);

        $access->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function revoke(Document $document, $id)
    {
        Gate::authorize('grant-document-access', $document);

        $access = $document->access()->specialUserAccess()->findOrFail($id);

        (new DocumentAccessService())->revokeAccess($access);

        return response()->json(null, Response::HTTP_OK);
    }

}
