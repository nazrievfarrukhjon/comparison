<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Privilege;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PrivilegeController extends Controller
{

    // privileges
    public function assignRoleToUser(int $roleId, int $userId): JsonResponse
    {
        $privilege = Privilege::newRoleUserConstructor($roleId, $userId);
        $privilege->assignRoleToUser();

        return response()->json([
            'message' => 'Role assigned to user successfully',
            'user' => User::query()->find($userId)
        ]);
    }

    public function assignPermissionToUser(int $permissionId, int $userId): JsonResponse
    {
        $privilege = Privilege::newPermissionUserConstructor($permissionId, $userId);
        $privilege->assignPermissionToUser();

        return response()->json([
            'message' => 'Permission assigned to user successfully',
            'user' => User::query()->find($userId)
        ]);
    }

    public function assignPermissionToRole(int $permissionId, int $roleId): JsonResponse
    {
        $privilege = Privilege::newPermissionRoleConstructor($permissionId, $roleId);
        $privilege->assignPermissionToRole();

        return response()->json([
            'message' => 'Permission assigned to role successfully',
            'role' => Role::query()->find($roleId)
        ]);
    }

    //roles
    public function getRoles(): LengthAwarePaginator
    {
        $privilege = Privilege::newDefaultConstructor();

        return $privilege->rolesWithPermissionsPagination();
    }

    public function storeRole(Request $request): JsonResponse
    {
        $privilege = Privilege::newAttributesConstructor($request->all());
        $privilege->storeRole();

        return response()->json(['role' => $privilege->role()]);
    }

    public function updateRole(Request $request, int $roleId): JsonResponse
    {
        $privilege = Privilege::newAttributesAndRoleIdConstructor($request->all(), $roleId);
        $privilege->updateRole();

        return response()->json(['role' => $privilege->role()]);
    }

    public function destroyRole(int $roleId): JsonResponse
    {
        $privilege = Privilege::newRoleIdConstructor($roleId);
        $privilege->deleteRole();

        return response()->json(null, 204);
    }

    public function getPermission(): LengthAwarePaginator
    {
        $privilege = Privilege::newDefaultConstructor();

        return $privilege->permissionsPagination();
    }

    public function storePermission(Request $request): JsonResponse
    {
        $privilege = Privilege::newAttributesConstructor($request->all());
        $privilege->storePermission();

        return response()->json(['permission' => $privilege->permission()], 201);
    }

    public function updatePermission(Request $request, int $permissionId): JsonResponse
    {
        $privilege = Privilege::newAttributesAndPermissionIdConstructor($request->all(), $permissionId);
        $privilege->updatePermission();

        return response()->json(['permission' => $privilege->permission()], 201);
    }

    public function destroyPermission(int $permissionId): JsonResponse
    {
        $privilege = Privilege::newPermissionIdConstructor($permissionId);
        $privilege->deletePermission();

        return response()->json(null, 204);
    }
}
