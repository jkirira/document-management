<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentAccessManagerRequest;
use App\Models\Document;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DocumentAccessManagersController extends Controller
{
    public function index(Document $document)
    {
        return response()->json($document->accessManagers, Response::HTTP_OK);
    }

    public function store(DocumentAccessManagerRequest $request, Document $document)
    {
        Gate::authorize('manage-access-managers', $document);

        $document->accessManagers()->sync($request['access_managers']);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Document $document, $id)
    {
        $user = $document->accessManagers()->where('user_id', $id)->first();
        return response()->json($user, Response::HTTP_OK);
    }

    public function update(DocumentAccessManagerRequest $request, Document $document)
    {
        Gate::authorize('manage-access-managers', $document);

        $document->accessManagers()->sync($request['access_managers']);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Document $document, $id)
    {
        Gate::authorize('manage-access-managers', $document);

        $document->accessManagers()->detach($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
