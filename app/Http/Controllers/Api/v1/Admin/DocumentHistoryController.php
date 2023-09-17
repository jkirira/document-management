<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Transformers\Admin\DocumentTransformer;
use Illuminate\Http\Response;

class DocumentHistoryController extends Controller
{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $this->authorize('viewAny', Document::class);
        $documents = Document::with(['folder', 'owner', 'deletedBy'])
                            ->withTrashed()
                            ->get()
                            ->map(function ($document) {
                                return (new DocumentTransformer())->transformForHistory($document);
                            });

        return response()->json($documents, Response::HTTP_OK);
    }

    public function show($id)
    {
        $document = Document::with(['folder', 'owner', 'deletedBy'])
                            ->withTrashed()
                            ->findOrFail($id);

        $this->authorize('view', $document);
        return response()->json((new DocumentTransformer())->transformForHistory($document), Response::HTTP_OK);
    }

}
