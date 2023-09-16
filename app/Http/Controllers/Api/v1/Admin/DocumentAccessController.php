<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentAccessRequest;
use App\Http\Requests\Admin\UpdateDocumentAccessRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Services\DocumentAccessService;
use App\Transformers\Admin\DocumentAccessTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DocumentAccessController extends Controller
{
    public function index(Document $document)
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

    public function store(DocumentAccessRequest $request, Document $document)
    {
        Gate::authorize('grant-document-access', $document);

        $input = $request->all();
        (new DocumentAccessService())->grantAccess($document, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Document $document, DocumentAccess $access)
    {
        // add maybe only admin authorization
        $document->access()->normalAccess()->findOrFail($access->id);
        return response()->json((new DocumentAccessTransformer())->transform($access), Response::HTTP_OK);
    }

    public function update(DocumentAccessRequest $request, Document $document, $id)
    {
        Gate::authorize('grant-document-access', $document);

        $input = $request->all();

        $documentAccess = $document->access()->normalAccess()->findOrFail($id);

        (new DocumentAccessService())->updateAccess($documentAccess, $input);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Document $document, DocumentAccess $access)
    {
        Gate::authorize('grant-document-access', $document);

        $access = $document->access()->normalAccess()->findOrFail($access->id);

        $access->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function updateDocumentAccess(UpdateDocumentAccessRequest $request, Document $document)
    {
        Gate::authorize('grant-document-access', $document);

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

    public function revoke(Document $document, DocumentAccess $access)
    {
        Gate::authorize('grant-document-access', $document);

        $access = $document->access()->normalAccess()->findOrFail($access->id);

        (new DocumentAccessService())->revokeAccess($access);

        return response()->json(null, Response::HTTP_OK);
    }

}
