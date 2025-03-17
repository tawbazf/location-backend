<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *     title="Car Rental API",
 *     version="1.0.0",
 *     description="API documentation for the Car Rental System"
 * )
 *
 * @OA\Server(
 *     url="http://carrental.test/api",
 *     description="Local API Server"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints related to user authentication (login, register, logout)"
 * )
 *
 * @OA\Tag(
 *     name="Cars",
 *     description="Endpoints for managing cars (CRUD and search)"
 * )
 *
 * @OA\Tag(
 *     name="Payments",
 *     description="Endpoints for payment processing and retrieval"
 * )
 *
 * @OA\Tag(
 *     name="Rentals",
 *     description="Endpoints for rental management (booking, success, cancellation)"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use this to authenticate with Sanctum."
 * )
 */

abstract class Controller
{
    //
}
