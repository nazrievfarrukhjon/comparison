<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\PrivilegeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PrivilegeController extends Controller
{

    public function __construct(private readonly PrivilegeService $privilegeService)
    {
    }

    // privileges
    public function assignRoleToUser(int $roleId, int $userId): JsonResponse
    {
        return $this->privilegeService->assignRoleToUser($roleId, $userId);
    }

    public function assignPermissionToUser(int $permissionId, int $userId): JsonResponse
    {
        return $this->privilegeService->assignPermissionToUser($permissionId, $userId);
    }

    public function assignPermissionToRole(int $permissionId, int $roleId): JsonResponse
    {
        return $this->privilegeService->assignPermissionToRole($permissionId, $roleId);
    }

    //roles
    public function getRoles(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->paginate();

    }

    public function storeRole(Request $request): JsonResponse
    {
        return $this->privilegeService->storeRole($request->all());
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        return $this->privilegeService->updateRole($request->all(), $id);
    }

    public function destroyRole(int $id): JsonResponse
    {
        Role::query()
            ->findOrFail($id)
            ->delete();

        return response()->json(null, 204);
    }

    public function getPermission(): LengthAwarePaginator
    {
        return Permission::query()->paginate();
    }

    public function storePermission(Request $request): JsonResponse
    {
        return $this->privilegeService->storePermission($request->all());
    }

    public function updatePermission(Request $request, int $id): bool|int
    {
        $permission = Permission::query()
            ->findOrFail($id);

        return $permission->update($request->all());
    }

    public function destroyPermission(int $id): JsonResponse
    {
        Permission::query()
            ->findOrFail($id)
            ->delete();

        return response()->json(null, 204);
    }
}
