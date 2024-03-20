<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\DocumentAccessManagerRequest;
use App\Models\Document;
use App\Transformers\Client\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DocumentAccessManagersController extends Controller
{
    public function index(Request $request, Document $document)
    {
        Gate::authorize('manage-document-access-managers', $document);

        $accessManagers = $document->accessManagers()
                                    ->when(isset($request->search), function ($query) use ($request) {
                                        return $query->search($request->search, 10);
                                    })
                                    ->get()
                                    ->map(function ($accessManager) {
                                        return (new UserTransformer())->transform($accessManager);
                                    });

        return response()->json($accessManagers, Response::HTTP_OK);
    }

    public function store(DocumentAccessManagerRequest $request, Document $document)
    {
        Gate::authorize('manage-document-access-managers', $document);

//        $document->accessManagers()->sync($request['access_managers']);
        DB::transaction(function () use ($request, $document) {
            $document->accessManagers()->detach($request['user_id']);
            $document->accessManagers()->attach($request['user_id']);
        });

        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Document $document, $id)
    {
        Gate::authorize('manage-document-access-managers', $document);

        $user = $document->accessManagers()->where('user_id', $id)->first();

        return response()->json((new UserTransformer())->transform($user), Response::HTTP_OK);
    }

    public function update(DocumentAccessManagerRequest $request, Document $document)
    {
        Gate::authorize('manage-document-access-managers', $document);

        $document->accessManagers()->sync($request['access_managers']);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Document $document, $id)
    {
        Gate::authorize('manage-document-access-managers', $document);

        $document->accessManagers()->detach($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
