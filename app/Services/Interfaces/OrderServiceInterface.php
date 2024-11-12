<?php

namespace App\Services\Interfaces;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Collection;

interface OrderServiceInterface
{
    public function placeOrder(StoreOrderRequest $request): Order;
    public function getUserOrderHistory($userId): Collection;
}
