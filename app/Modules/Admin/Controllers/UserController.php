<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Requests\UserStoreRequest;
use App\Modules\Admin\Requests\UserUpdateRequest;
use App\Modules\Admin\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): LengthAwarePaginator|JsonResponse
    {
        $user = UserRepository::newAttributesConstructor($request->all());
        $usersPagination = $user->withPrivilegesPagination();

        return response()->json(['user' => $usersPagination]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $userEO = UserRepository::newAttributesConstructor($request->all());
        $userEO->store();

        return response()->json(['user' => $userEO->user()], 201);
    }

    public function update(UserUpdateRequest $request, $userId): JsonResponse
    {
        $userEO = UserRepository::newAttributesAndUserIdConstructor($request->all(), $userId);
        $userEO->update();

        return response()->json(['user' => $userEO->user()], 201);
    }

    public function delete(int $userId): JsonResponse
    {
        $userEO = UserRepository::newUserIdConstructor($userId);
        $userEO->delete();

        return response()->json(null, 204);
    }

}
