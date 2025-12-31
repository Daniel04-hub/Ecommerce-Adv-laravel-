<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="E-Commerce Platform API Documentation",
 *     version="1.0.0",
 *     description="API documentation for mobile app, vendor integrations, and admin services",
 *     @OA\Contact(
 *         email="admin@ecommerce-platform.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server (localhost)"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum authentication token"
 * )
 * 
 * @OA\Tag(
 *     name="COD Verification",
 *     description="Cash on Delivery OTP verification APIs for delivery persons"
 * )
 * 
 * @OA\Tag(
 *     name="OTP Authentication",
 *     description="One-Time Password login system for customers"
 * )
 * 
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin management APIs"
 * )
 * 
 * @OA\Tag(
 *     name="Vendor",
 *     description="Vendor-facing APIs and tools"
 * )
 * 
 * @OA\Tag(
 *     name="Mobile",
 *     description="Mobile app consumer APIs"
 * )
 */
abstract class Controller
{
    //
}
