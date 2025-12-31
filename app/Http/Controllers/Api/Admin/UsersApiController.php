<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersApiController extends Controller
{
    /**
     * Display a listing of users
    * 
    * @OA\Get(
    *     path="/api/admin/users",
    *     tags={"Admin"},
    *     summary="List users",
    *     description="Returns all users with basic profile and role information.",
    *     security={{"sanctum":{}}},
    *     @OA\Response(response=200, description="Users list")
    * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'customer',
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'data' => $users
        ]);
    }

    /**
     * Display the specified user
        * 
        * @OA\Get(
        *     path="/api/admin/users/{id}",
        *     tags={"Admin"},
        *     summary="Get user",
        *     description="Returns a user's details by id.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="User details"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'customer',
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }
}
