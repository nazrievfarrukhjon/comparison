<?php

namespace App\Modules\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    public function get(array $attributes): LengthAwarePaginator|JsonResponse
    {
        try {
            $perPage = $attributes['per_page'] ?? 10;
            $page = $attributes['page'] ?? 1;
            $order = $attributes['order'] ?? 'asc';
            $orderBy = $attributes['order_by'] ?? 'id';


            return User::query()
                ->with(['roles', 'permissions'])
                ->orderBy($orderBy, $order)
                ->paginate($perPage, ['*'], 'page', $page);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function store(array $attributes): JsonResponse
    {
        $user = User::query()
            ->create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => Hash::make($attributes['password']),
            ]);

        return response()->json(['user' => $user], 201);
    }

    public function update(array $attributes, $id): JsonResponse
    {
        $user = User::query()
            ->findOrFail($id);

        $user->name = $attributes['name'];
        $user->email = $attributes['email'];

        if (isset($attributes['password'])) {
            $user->password = Hash::make($attributes['password']);
        }

        $user->save();

        return response()->json(['user' => $user]);
    }
}
