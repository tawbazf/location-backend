<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

/**
 * @OA\Tag(
 *     name="Rentals",
 *     description="Endpoints for managing rentals"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Rental",
 *     type="object",
 *     required={"user_id", "car_id", "start_date", "end_date", "total_price"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="car_id", type="integer", example=5),
 *     @OA\Property(property="start_date", type="string", format="date", example="2022-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2022-01-05"),
 *     @OA\Property(property="total_price", type="number", format="float", example=150.50),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2021-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, example="2021-01-01T12:00:00Z")
 * )
 */
class RentalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/rentals",
     *     summary="List all rentals",
     *     tags={"Rentals"},
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of rentals",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Rental")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Rental::paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/rentals",
     *     summary="Create a new rental and initiate Stripe checkout session",
     *     tags={"Rentals"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Rental data",
     *         @OA\JsonContent(
     *             required={"car_id", "start_date", "end_date", "total_price"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="car_id", type="integer", example=5),
     *             @OA\Property(property="start_date", type="string", format="date", example="2022-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2022-01-05"),
     *             @OA\Property(property="total_price", type="number", format="float", example=150.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stripe checkout session URL",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://checkout.stripe.com/cs_test_a1b2c3")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error processing rental or payment"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_price' => 'required|numeric',
        ]);
        $data['user_id'] = $request->user()->id;
        try {
            DB::beginTransaction();
            $rental = Rental::create($data);

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = CheckoutSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Car Rental',
                        ],
                        'unit_amount' => $data['total_price'] * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => env('FRONTEND_URL') . "/payment-success/" . $rental->id,
                'cancel_url' => env('FRONTEND_URL') . "/payment-cancel/" . $rental->id,
                'metadata' => [
                    'rental_id' => $rental->id,
                    'user_id' => $data['user_id'],
                ],
            ]);

            DB::commit();
            return response()->json(['url' => $session->url]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/rentals/{id}",
     *     summary="Get a specific rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found"
     *     )
     * )
     */
    public function show(Rental $rental)
    {
        return response()->json($rental);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/rentals",
     *     summary="Get rentals by user",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user whose rentals to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rentals retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Rental")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or no rentals available"
     *     )
     * )
     */
    public function getByUser(User $user)
    {
        return response()->json($user->rentals);
    }

    /**
     * @OA\Get(
     *     path="/cars/{carId}/rentals",
     *     summary="Get rentals by car",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="carId",
     *         in="path",
     *         description="ID of the car whose rentals to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rentals retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Rental")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found or no rentals available"
     *     )
     * )
     */
    public function getByCar(Car $car)
    {
        return response()->json($car->rentals);
    }

    /**
     * @OA\Delete(
     *     path="/rentals/{id}",
     *     summary="Delete a rental",
     *     tags={"Rentals"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="deleted", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found"
     *     )
     * )
     */
    public function destroy(Rental $rental)
    {
        return response()->json($rental->delete());
    }

    /**
     * @OA\Delete(
     *     path="/rentals/{id}/cancel",
     *     summary="Cancel a rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental to cancel",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental cancelled successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found"
     *     )
     * )
     */
    public function cancel(Rental $rental)
    {
        $rental->delete();
    }

    /**
     * @OA\Get(
     *     path="/rentals/{id}/success",
     *     summary="Handle rental payment success",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental that succeeded payment",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment record created for rental success",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found"
     *     )
     * )
     */
    public function success(Rental $rental)
    {
        Payment::create([
            'user_id' => $rental->user_id,
            'rental_id' => $rental->id,
            'amount' => $rental->total_price,
            'status' => 'paid',
            'payment_method' => 'stripe',
        ]);
    }
}


