<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\FolderRequest;
use App\Models\Folder;
use App\Services\DocumentAccessService;
use App\Transformers\Client\FolderTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FoldersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
//        $folders = Folder::with(['parentFolder', 'childFolders'])->get();
        $folders = (new DocumentAccessService())->foldersAccessibleByUser($request->user());

        $folders = $folders->map(function ($folder) {
                        return (new FolderTransformer())->transform($folder);
                    });

        return response()->json($folders, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FolderRequest $request)
    {
        $this->authorize('create', Folder::class);

        $input = $request->all();

        Folder::create([
            "name" => $input['name'],
            "parent_id" => isset($input['parent_id']) ? $input['parent_id'] : null,
            "added_by" => auth()->id(),
        ]);

        return response()->json([], Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Folder $folder)
    {
        $this->authorize('view', $folder);

        $folder->load(['parentFolder', 'childFolders']);
        $folder = (new FolderTransformer())->transform($folder);

        return response()->json($folder, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FolderRequest $request
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(FolderRequest $request, Folder $folder)
    {
        $this->authorize('update', $folder);

        $folder->update([
            'name' => $request->name,
            "parent_id" => isset($request->parent_id) ? $request->parent_id : null,
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Folder $folder)
    {
        $this->authorize('delete', $folder);

        $folder->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
