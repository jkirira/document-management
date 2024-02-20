<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\DocumentAccessRequest;
use App\Http\Requests\Client\UpdateDocumentAccessRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Services\DocumentAccessService;
use App\Transformers\Client\DocumentAccessTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DocumentAccessController extends Controller
{
    public function index()
    {
        // add maybe only admin authorization
        $documentAccess = DocumentAccess::query()
                                    ->normalAccess()
                                    ->get()
                                    ->map(function($access) {
                                        return (new DocumentAccessTransformer())->transform($access);
                                    });

        return response()->json($documentAccess, Response::HTTP_OK);
    }

    public function store(DocumentAccessRequest $request)
    {
        $document = Document::findOrFail($request->document_id);

        Gate::authorize('manage-document-access', $document);

        $input = $request->all();
        (new DocumentAccessService())->grantAccess($document, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        // add maybe only admin authorization
        $access = DocumentAccess::normalAccess()->findOrFail($id);
        return response()->json((new DocumentAccessTransformer())->transform($access), Response::HTTP_OK);
    }

    public function update(DocumentAccessRequest $request, $id)
    {
        $access = DocumentAccess::normalAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        $input = $request->all();

        (new DocumentAccessService())->updateAccess($access, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, $id)
    {
        $access = DocumentAccess::normalAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        $access->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function revoke(Request $request, $id)
    {
        $access = DocumentAccess::normalAccess()->findOrFail($id);

        Gate::authorize('manage-document-access', $access->document);

        (new DocumentAccessService())->revokeAccess($access);

        return response()->json(null, Response::HTTP_OK);
    }

    public function documentAccess(Document $document)
    {
        // add maybe only admin authorization
        $documentAccess = $document->access()
                                    ->normalAccess()
                                    ->get()
                                    ->map(function($access) {
                                        return (new DocumentAccessTransformer())->transform($access);
                                    });

        return response()->json($documentAccess, Response::HTTP_OK);
    }

    public function updateDocumentAccess(UpdateDocumentAccessRequest $request, Document $document)
    {
        Gate::authorize('manage-document-access', $document);

        $accessService = new DocumentAccessService();

        DB::transaction(function () use ($request,  $document, $accessService) {
            $accesses = $request->access;

            foreach ($accesses as $access_values) {
//            $documentAccess = DocumentAccess::findOrFail($access_values['id']);
                $documentAccess = $document->access()->normalAccess()->findOrFail($access_values['id']);
                $accessService->updateAccess($documentAccess, $access_values);
            }

        });

        return response()->json([], Response::HTTP_CREATED);
    }

}
