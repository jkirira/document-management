<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserAccessRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Services\DocumentAccessService;
use App\Transformers\Admin\UserAccessTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserAccessController extends Controller
{
    public function index()
    {
        // add maybe only admin authorization
        $userAccess = DocumentAccess::query()
                                ->specialUserAccess()
                                ->get()
                                ->map(function($access) {
                                    return (new UserAccessTransformer())->transform($access);
                                });

        return response()->json($userAccess, Response::HTTP_OK);
    }

    public function store(UserAccessRequest $request)
    {
        $input = $request->all();

        $document = Document::findOrFail($input['document_id']);

        Gate::authorize('manage-document-access', $document);

        (new DocumentAccessService())->grantAccess($document, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        // add maybe only admin authorization
        $access = DocumentAccess::specialUserAccess()->findOrFail($id);
        return response()->json((new UserAccessTransformer())->transform($access), Response::HTTP_OK);
    }

    public function update(UserAccessRequest $request, $id)
    {
        $input = $request->all();

        $access = DocumentAccess::specialUserAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        (new DocumentAccessService())->updateAccess($access, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, $id)
    {
        $access = DocumentAccess::specialUserAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        $access->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function revoke(Request $request, $id)
    {
        $access = DocumentAccess::specialUserAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        (new DocumentAccessService())->revokeAccess($access);

        return response()->json(null, Response::HTTP_OK);
    }

    public function userAccess(Document $document)
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

}
