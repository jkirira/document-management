<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Department::class);

        $departmentsQuery = Department::query()
                            ->when(isset($request->search), function($query) use ($request) {
                                return $query->search($request->search);
                            })
                            ->orderBy('id', 'desc');

        if (isset($request->page) && isset($request->perPage)) {
            return response()->json($departmentsQuery->paginate($request->perPage), Response::HTTP_OK);
        }

        return response()->json($departmentsQuery->get(), Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DepartmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(DepartmentRequest $request)
    {
        $this->authorize('create', Department::class);
        $department = Department::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Department $department)
    {
        $this->authorize('view', $department);
        return response()->json($department, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DepartmentRequest $request
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $this->authorize('update', $department);

        $department->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);

        $department->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
