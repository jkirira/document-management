<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $rolesQuery = Role::query()
                        ->when(isset($request->search), function($query) use ($request) {
                            return $query->search($request->search);
                        })
                        ->orderBy('id', 'desc');

        if (isset($request->page) && isset($request->perPage)) {
            return response()->json($rolesQuery->paginate($request->perPage), Response::HTTP_OK);
        }

        return response()->json($rolesQuery->get(), Response::HTTP_OK);

    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);
        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return response()->json([], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('view', $role);
        return response()->json($role, Response::HTTP_OK);
    }

    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $this->authorize('update', $role);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $this->authorize('delete', $role);

        $role->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
