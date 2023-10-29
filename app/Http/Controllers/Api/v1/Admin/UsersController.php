<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Notifications\AccountCreated;
use App\Services\UserService;
use App\Transformers\Admin\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $usersQuery = User::query()
                            ->with(['roles', 'department'])
                            ->when(isset($request->search), function($query) use ($request) {
                                return $query->search($request->search);
                            })
                            ->orderBy('id', 'desc');

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UserRequest $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $password = Str::random(8);
        $user = DB::transaction(function () use ($request, $password) {

            $userService = new UserService();

            $user = $userService->createUser($request->all());

            $user = $userService->updateUser($user, ['password' => Hash::make($password)]);

            return $user;

        });

        if ($user) {
            $user->notify(new AccountCreated(['password' => $password]));
        }

        return response()->json([], Response::HTTP_CREATED);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        DB::transaction(function () use ($user, $request) {

            (new UserService())->updateUser($user, $request->all());

        });

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

}
