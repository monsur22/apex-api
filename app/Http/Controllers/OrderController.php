<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->middleware('permission:create-orders')->only('store');
        $this->middleware('permission:view-own-orders')->only('history');

        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->placeOrder($request);
            return $this->successResponse(new OrderResource($order));
        } catch (\Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('There was an issue creating your order. Please try again later.', [], 500);
        }
    }

    public function history()
    {
        $user = Auth::user();
        $orders = $this->orderService->getUserOrderHistory($user->id);
        return $this->successResponse(OrderResource::collection($orders), 'Order history fetched successfully');
    }
}
