<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $categoriesQuery = Category::query()
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
        $this->authorize('create', Category::class);
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return response()->json([], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('view', $category);
        return response()->json($category, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('delete', $category);

        $category->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
