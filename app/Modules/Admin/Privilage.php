<?php

namespace App\Modules\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Privilege
{

    private int $roleId;

    private int $permissionId;

    private int $userId;

    public static function newRoleUserConstructor(int $roleId, int $userId): Privilege
    {
        $obj = new self();
        $obj->roleId = $roleId;
        $obj->userId = $userId;

        return $obj;
    }

    public static function newPermissionUserConstructor(int $permissionId, int $userId): Privilege
    {
        $obj = new self();
        $obj->permissionId = $permissionId;
        $obj->userId = $userId;

        return $obj;
    }

    public static function newPermissionRoleConstructor(int $permissionId, int $roleId): Privilege
    {
        $obj = new self();
        $obj->permissionId = $permissionId;
        $obj->roleId = $roleId;

        return $obj;
    }

    public function assignRoleToUser(): void
    {
        $user = User::query()
            ->findOrFail($this->userId);
        $role = Role::query()
            ->findOrFail($this->roleId);

        $user->assignRole($role);
    }

    public function assignPermissionToUser(): void
    {
        $user = User::query()
            ->findOrFail($this->userId);

        $permission = Permission::query()
            ->findOrFail($this->permissionId);

        $user->givePermissionTo($permission);
    }

    public function assignPermissionToRole()
    {
        $role = Role::query()
            ->findOrFail($this->roleId);

        $permission = Permission::query()
            ->findOrFail($this->permissionId);

        $role->givePermissionTo($permission);
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
