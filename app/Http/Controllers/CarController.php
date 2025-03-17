<?php

namespace App\Http\Controllers;

use App\Models\car;
use Illuminate\Http\Request;


/**
 * @OA\Tag(
 *     name="Cars",
 *     description="Endpoints for managing cars"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Car",
 *     type="object",
 *     required={"brand", "model", "year", "price_per_day", "is_available"},
 *     @OA\Property(property="brand", type="string", example="Toyota"),
 *     @OA\Property(property="model", type="string", example="Corolla"),
 *     @OA\Property(property="year", type="integer", example=2022),
 *     @OA\Property(property="price_per_day", type="number", format="float", example=50.0),
 *     @OA\Property(property="is_available", type="boolean", example=true)
 * )
 */
class CarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cars",
     *     summary="List all cars",
     *     tags={"Cars"},
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of cars",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/Car")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */

    public function index()
    {
        return response()->json(car::paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/cars",
     *     summary="Create a new car",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Car object that needs to be added",
     *         @OA\JsonContent(
     *             required={"brand", "model", "year", "price_per_day", "is_available"},
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Corolla"),
     *             @OA\Property(property="year", type="integer", example=2022),
     *             @OA\Property(property="price_per_day", type="number", format="float", example=50.0),
     *             @OA\Property(property="is_available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required',
            'price_per_day' => 'required',
            'is_available' => 'required',
        ]);
        $car = car::create($data);
        return response()->json($car);
    }

    /**
     * @OA\Get(
     *     path="/cars/{id}",
     *     summary="Get a specific car",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the car to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function show(Car $car)
    {
        return response()->json($car);
    }
    /**
     * @OA\Put(
     *     path="/cars/{id}",
     *     summary="Update a car",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the car to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Car object with updated information",
     *         @OA\JsonContent(
     *             required={"brand", "model", "year", "price_per_day", "is_available"},
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Corolla"),
     *             @OA\Property(property="year", type="integer", example=2022),
     *             @OA\Property(property="price_per_day", type="number", format="float", example=50.0),
     *             @OA\Property(property="is_available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */

    public function update(Request $request, Car $car)
    {
        $data = $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required',
            'price_per_day' => 'required',
            'is_available' => 'required',
        ]);
        $car->update($data);
        return response()->json($car);
    }
    /**
     * @OA\Patch(
     *     path="/cars/{id}",
     *     summary="Partially update a car",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the car to partially update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Car object with partial updated information",
     *         @OA\JsonContent(
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Corolla"),
     *             @OA\Property(property="year", type="integer", example=2022),
     *             @OA\Property(property="price_per_day", type="number", format="float", example=50.0),
     *             @OA\Property(property="is_available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car partially updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function patch(Request $request, Car $car)
    {
        $data = $request->validate([
            'brand' => '',
            'model' => '',
            'year' => '',
            'price_per_day' => '',
            'is_available' => '',
        ]);
        $car->update($data);
        return response()->json($car);
    }
    /**
     * @OA\Get(
     *     path="/search/cars",
     *     summary="Search for cars",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search term for filtering cars",
     *         required=true,
     *         @OA\Schema(type="string", example="iph")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Car")
     *         )
     *     )
     * )
     */

    public function search(Request $request)
    {
        $query = $request->input('query');
        $cars = Car::search($query)->get();
        return response()->json($cars);
    }

    /**
     * @OA\Delete(
     *     path="/cars/{id}",
     *     summary="Delete a car",
     *     tags={"Cars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the car to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Car deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function destroy(car $car)
    {
        $car->delete();
        return response()->json(['message' => 'Car deleted successfully']);
    }
}
