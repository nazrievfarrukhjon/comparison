<?php

namespace App\Modules\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    private User $user;

    private array $attributes;

    private int $userId;

    public static function newAttributesConstructor(array $attributes): UserRepository
    {
        $obj = new self();
        $obj->attributes = $attributes;
        return $obj;
    }

    public static function newAttributesAndUserIdConstructor(array $attributes, int $userId): UserRepository
    {
        $obj = new self();
        $obj->attributes = $attributes;
        $obj->userId = $userId;
        return $obj;
    }

    public static function newUserIdConstructor($userId): UserRepository
    {
        $obj = new self();
        $obj->userId = $userId;
        return $obj;
    }

    public function user(): User
    {
        return $this->user;

    }

    public function withPrivilegesPagination(): LengthAwarePaginator|JsonResponse
    {
            $perPage = $this->attributes['per_page'] ?? 10;
            $page = $this->attributes['page'] ?? 1;
            $order = $this->attributes['order'] ?? 'asc';
            $orderBy = $this->attributes['order_by'] ?? 'id';

            return User::query()
                ->with(['roles', 'permissions'])
                ->orderBy($orderBy, $order)
                ->paginate($perPage, ['*'], 'page', $page);
    }

    public function store(): void
    {
        User::query()
            ->create([
                'name' => $this->attributes['name'],
                'email' => $this->attributes['email'],
                'password' => Hash::make($this->attributes['password']),
            ]);
    }

    public function update(): void
    {
        $user = User::query()
            ->findOrFail($this->userId);

        $user->name = $this->attributes['name'];
        $user->email = $this->attributes['email'];

        if (isset($this->attributes['password'])) {
            $user->password = Hash::make($this->attributes['password']);
        }

        $user->save();
    }

    public function delete(): void
    {
        User::query()
            ->findOrFail($this->userId)
            ->delete();
    }
}
