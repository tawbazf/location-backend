<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Endpoints for managing payments"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     required={"rental_id", "amount", "payment_method", "status"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="rental_id", type="integer", example=10),
 *     @OA\Property(property="amount", type="number", format="float", example=99.99),
 *     @OA\Property(
 *         property="payment_method",
 *         type="string",
 *         enum={"credit_card", "paypal", "cash"},
 *         example="credit_card"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "completed", "failed"},
 *         example="completed"
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2021-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, example="2021-01-01T12:00:00Z")
 * )
 */

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/payments",
     *     summary="List all payments",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of payments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/Payment")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Payment::paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payment object that needs to be added",
     *         @OA\JsonContent(
     *             required={"rental_id", "amount", "payment_method", "status"},
     *             @OA\Property(property="rental_id", type="integer", example=10),
     *             @OA\Property(property="amount", type="number", format="float", example=99.99),
     *             @OA\Property(
     *                 property="payment_method",
     *                 type="string",
     *                 enum={"credit_card", "paypal", "cash"},
     *                 example="credit_card"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending", "completed", "failed"},
     *                 example="completed"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:credit_card,paypal,cash',
            'status' => 'required|in:pending,completed,failed',
        ]);
        $payment = Payment::create($request->all());
        return response()->json($payment);
    }

    /**
     * @OA\Get(
     *     path="/payments/{id}",
     *     summary="Get a specific payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the payment to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     )
     * )
     */
    public function show(Payment $payment)
    {
        return response()->json($payment);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/payments",
     *     summary="Get payments by user",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user whose payments to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payments retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or no payments available"
     *     )
     * )
     */
    public function getByUser(User $user)
    {
        return response()->json($user->payments);
    }

    /**
     * @OA\Get(
     *     path="/rentals/{rentalId}/payments",
     *     summary="Get payments by rental",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         description="ID of the rental whose payments to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payments retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Payment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found or no payments available"
     *     )
     * )
     */
    public function getByRental(Rental $rental)
    {
        return response()->json($rental->payments);
    }
}

