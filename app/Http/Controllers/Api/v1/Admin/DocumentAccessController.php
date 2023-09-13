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
use Illuminate\Support\Facades\Gate;

class DocumentAccessController extends Controller
{
    public function index(Document $document)
    {
        // add maybe only admin authorization
        $documentAccess = $document->access->map(function($access) {
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
        return response()->json((new DocumentAccessTransformer())->transform($access), Response::HTTP_OK);
    }

    public function update(UpdateDocumentAccessRequest $request, Document $document, DocumentAccess $access)
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

    public function destroy(Document $document, DocumentAccess $access)
    {
        Gate::authorize('grant-document-access', $document);

        (new DocumentAccessService())->revokeAccess($access);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
