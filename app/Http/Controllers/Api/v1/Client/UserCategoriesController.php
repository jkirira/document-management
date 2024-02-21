<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categoriesQuery = UserCategory::query()
                                ->forUser($request->user())
                                ->when(isset($request->search), function($query) use ($request) {
                                    return $query->search($request->search);
                                })
                                ->orderBy('id', 'desc');

        if (isset($request->page) && isset($request->perPage)) {
            return response()->json($categoriesQuery->paginate($request->perPage), Response::HTTP_OK);
        }

        return response()->json($categoriesQuery->get(), Response::HTTP_OK);

    }

    public function store(Request $request)
    {
        $this->authorize('create', UserCategory::class);
        $userCategory = UserCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'user_id' => $request->user()->id,
        ]);
        return response()->json([], Response::HTTP_CREATED);
    }

    public function show(Request $request, $id)
    {
        $userCategory = UserCategory::forUser($request->user())->findOrFail($id);
        $this->authorize('view', $userCategory);
        return response()->json($userCategory, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $userCategory = UserCategory::forUser($request->user())->findOrFail($id);

        $this->authorize('update', $userCategory);

        $userCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, $id)
    {
        $userCategory = UserCategory::forUser($request->user())->findOrFail($id);

        $this->authorize('delete', $userCategory);

        $userCategory->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
