<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\FolderRequest;
use App\Models\Folder;
use App\Services\DocumentAccessService;
use App\Transformers\Client\DocumentTransformer;
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
        $folders = (new DocumentAccessService())->foldersAccessibleByUser($request->user());

        if (isset($request->search)) {
            $allFolders = Folder::search($request->search)->get();
            $ids = $folders->pluck('id')->all();

            $folders = $allFolders->filter(function ($folder) use ($ids) {
                            return in_array($folder->id, $ids);
                        });
        }

        $query = $request->query();
        if (isset($query['type'])) {
            if ($query['type'] == 'root') {
                $folders = $folders->filter(function ($folder) {
                                return !isset($folder->parent_id);
                            });

            } elseif ($query['type'] == 'non_root') {
                $folders = $folders->filter(function ($folder) {
                                return isset($folder->parent_id);
                            });

            }
        }

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
        return response()->json((new FolderTransformer())->transform($folder), Response::HTTP_OK);
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

    public function content(Request $request, Folder $folder)
    {
        $this->authorize('view', $folder);

        $data = [];

        $query = $request->query();

        if (!isset($query['content_type']) || in_array($query['content_type'], ['all', 'folders'])) {
            $accessibleFolders = (new DocumentAccessService())->foldersAccessibleByUser($request->user());
            $folders = $folder->childFolders()
//                        ->hasDocumentsThatUserCanAccess($request->user())
                            ->whereIn('id', $accessibleFolders->pluck('id'))
                            ->get()
                            ->map(function ($folder) {
                                $folder = (new FolderTransformer())->transform($folder);
                                $folder['type'] = 'folder';
                                return $folder;
                            });

            $data = isset($data) ? collect($data)->merge($folders)->all() : $folders;
        }

        if (!isset($query['content_type']) || in_array($query['content_type'], ['all', 'documents'])) {
            $documents = $folder->documents()
                                ->accessibleToUser($request->user())
                                ->get()
                                ->map(function ($document) {
                                    $document = (new DocumentTransformer())->transform($document);
                                    $document['type'] = 'document';
                                    return $document;
                                });

            $data = isset($data) ? collect($data)->merge($documents)->all() : $documents;
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function breadcrumbs(Folder $folder)
    {
        $breadcrumbs = [(new FolderTransformer())->transform($folder)];

        $currentFolder = $folder;
        while($currentFolder->parent_id) {
            array_unshift($breadcrumbs, (new FolderTransformer())->transform($currentFolder->parentFolder));
            $currentFolder = $currentFolder->parentFolder;
        }

        return response()->json($breadcrumbs, Response::HTTP_OK);
    }

}
