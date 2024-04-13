<?php

namespace App\Modules\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Privilege
{

    private int $roleId;

    private int $permissionId;

    private int $userId;

    private array $attributes;

    private Role $role;

    private Permission|\Spatie\Permission\Contracts\Permission $permission;

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

    public static function newDefaultConstructor(): Privilege
    {
        return new Privilege();
    }

    public static function newAttributesConstructor(array $attributes): Privilege
    {
        $obj = new self();
        $obj->attributes = $attributes;
        return $obj;
    }

    public static function newAttributesAndRoleIdConstructor(array $attributes, int $roleId): Privilege
    {
        $obj = new self();
        $obj->attributes = $attributes;
        $obj->roleId = $roleId;
        return $obj;
    }

    public static function newRoleIdConstructor(int $roleId): Privilege
    {
        $obj = new self();
        $obj->roleId = $roleId;
        return $obj;
    }

    public static function newAttributesAndPermissionIdConstructor(array $attributes, int $permissionId): Privilege
    {
        $obj = new self();
        $obj->attributes = $attributes;
        $obj->permissionId = $permissionId;
        return $obj;
    }

    public static function newPermissionIdConstructor(int $permissionId): Privilege
    {
        $obj = new self();
        $obj->permissionId = $permissionId;
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

    public function storeRole(): void
    {
        $role = Role::create([
            'name' => $this->attributes['name'],
            'guard_name' => $this->attributes['guard_name']
        ]);

        $this->role = $role;
    }

    public function updateRole(): void
    {
        $role = Role::query()
            ->findOrFail($this->roleId);

        $role->update(['name' => $this->attributes['name']]);

        $this->role = $role;
    }

    public function storePermission(): void
    {
        $permission = Permission::create([
            'name' => $this->attributes['name'],
            'guard_name' => $this->attributes['guard_name']
        ]);

        $this->permission = $permission;
    }

    public function rolesWithPermissionsPagination(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->paginate();
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function permission(): Permission
    {
        return $this->permission;
    }


    public function deleteRole(): void
    {
        Role::query()
            ->findOrFail($this->roleId)
            ->delete();
    }

    public function permissionsPagination(): LengthAwarePaginator
    {
        return Permission::query()->paginate();
    }

    public function updatePermission(): void
    {
        $permission = Permission::query()
            ->findOrFail($this->permissionId);
        $permission->update(['name' => $this->attributes['name']]);

        $this->permission = $permission;
    }

    public function deletePermission(): void
    {
        Permission::query()
            ->findOrFail($this->permissionId)
            ->delete();
    }

}
