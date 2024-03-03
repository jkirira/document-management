<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\Client\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $usersQuery = User::with(['roles', 'department'])->orderBy('id', 'desc');

        if (isset($request->page) && isset($request->perPage)) {
            $paginatedUsers = $usersQuery->paginate($request->perPage);
            $paginatedUsers->getCollection()->transform(function ($value) {
                return (new UserTransformer())->transform($value);
            });
            return response()->json($paginatedUsers, Response::HTTP_OK);
        }

        $users = $usersQuery->get()->map(function ($value) {
            return (new UserTransformer())->transform($value);
        });

        return response()->json($users, Response::HTTP_OK);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with(['roles', 'department'])->findOrFail($id);

        $this->authorize('view', $user);

        return response()->json($user, Response::HTTP_CREATED);
    }

}
