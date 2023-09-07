<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRequest;
use App\Models\Document;
use App\Models\Role;
use App\Services\DocumentAccessService;
use App\Services\DocumentService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DocumentsController extends Controller
{
    public $documentService;


    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $this->authorize('viewAny', Role::class);
        return response()->json(Document::all(), Response::HTTP_OK);
    }

    public function store(DocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        $savedDocument = DB::transaction(function () use ($request) {

            $input = $request->except('access');
            $document = $request->file('document');
            $user = $request->user();

            $savedDocument = $this->documentService->addNewDocument($document, $input, $user);

            $accesses = $request->access;
            $accessService = new DocumentAccessService();
            foreach ($accesses as $access) {
                $accessService->grantAccess($savedDocument, $access);
            }

            return $savedDocument;

        });

        return response()->json([], Response::HTTP_CREATED);

    }


}