<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorsApiController extends Controller
{
    /**
     * Display a listing of vendors
    * 
    * @OA\Get(
    *     path="/api/admin/vendors",
    *     tags={"Admin"},
    *     summary="List vendors",
    *     description="Returns all vendor accounts and approval status.",
    *     security={{"sanctum":{}}},
    *     @OA\Response(response=200, description="Vendors list")
    * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $vendors = User::whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        })->with('vendor')->latest()->get();

        $vendors = $vendors->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_approved' => $user->vendor?->is_approved ?? false,
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'data' => $vendors
        ]);
    }

    /**
     * Approve vendor
        * 
        * @OA\Patch(
        *     path="/api/admin/vendors/{id}/approve",
        *     tags={"Admin"},
        *     summary="Approve a vendor",
        *     description="Sets vendor approval status to true.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Vendor approved"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve($id)
    {
        $vendor = Vendor::where('user_id', $id)->firstOrFail();
        $vendor->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully.'
        ]);
    }

    /**
     * Block vendor
        * 
        * @OA\Patch(
        *     path="/api/admin/vendors/{id}/block",
        *     tags={"Admin"},
        *     summary="Block a vendor",
        *     description="Sets vendor approval status to false.",
        *     security={{"sanctum":{}}},
        *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
        *     @OA\Response(response=200, description="Vendor blocked"),
        *     @OA\Response(response=404, description="Not found")
        * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function block($id)
    {
        $vendor = Vendor::where('user_id', $id)->firstOrFail();
        $vendor->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor blocked successfully.'
        ]);
    }
}
