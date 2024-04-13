<?php

namespace App\Modules\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PrivilegeService
{

    public function assignRoleToUser(int $roleId, int $userId): JsonResponse
    {
        $user = User::query()
            ->findOrFail($userId);
        $role = Role::query()
            ->findOrFail($roleId);

        $user->assignRole($role);

        return response()->json(['message' => 'Role assigned to user successfully', 'user' => $user]);
    }

    public function assignPermissionToUser(int $permissionId, int $userId): JsonResponse
    {
        $user = User::query()
            ->findOrFail($userId);

        $permission = Permission::query()
            ->findOrFail($permissionId);

        $user->givePermissionTo($permission);

        return response()->json(['message' => 'Permission assigned to user successfully', 'user' => $user]);
    }

    public function assignPermissionToRole(int $permissionId, int $roleId): JsonResponse
    {
        $role = Role::query()
            ->findOrFail($roleId);

        $permission = Permission::query()
            ->findOrFail($permissionId);

        $role->givePermissionTo($permission);

        return response()->json(['message' => 'Permission assigned to role successfully', 'role' => $role]);
    }

    public function storeRole(array $attributes): JsonResponse
    {
        $role = Role::create(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']]);

        return response()->json(['role' => $role], 201);
    }

    public function updateRole(array $attributes, int $id): JsonResponse
    {
        $role = Role::query()
            ->findOrFail($id);

        $role->update(['name' => $attributes['name']]);

        return response()->json(['role' => $role]);
    }

    public function storePermission(array $attributes): JsonResponse
    {
        $role = Permission::create([
            'name' => $attributes['name'],
            'guard_name' => $attributes['guard_name']
        ]);

        return response()->json(['role' => $role], 201);
    }
}
