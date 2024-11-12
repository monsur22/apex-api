<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\StoreOrderRequest;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class OrderService implements OrderServiceInterface
{
    // Place an order for the authenticated user.
    public function placeOrder(StoreOrderRequest $request): Order
    {
        $validated = $request->validated();
        $user = Auth::user();

        DB::beginTransaction();

        try {

            $order = $this->createOrder($user->id); // Create a new order
            $totalAmount = $this->processOrderItems($validated['items'], $order); // Process each item in the order
            $order->update(['total_amount' => $totalAmount]); // Update the total amount in the order

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function createOrder(int $userId): Order
    {
        return Order::create([
            'user_id' => $userId,
            'total_amount' => 0,
        ]);
    }

    private function processOrderItems(array $items, Order $order): float
    {
        $totalAmount = 0;

        foreach ($items as $item) {
            $product = $this->getProductById($item['product_id']); 
            $this->checkProductStock($product, $item['quantity']);
            $this->updateProductStock($product, $item['quantity']);
            $this->createOrderItem($order, $product, $item['quantity']);
            $totalAmount += $product->price * $item['quantity'];
        }

        return $totalAmount;
    }
    private function getProductById(int $productId): Product
    {
        return Product::findOrFail($productId);
    }

    private function checkProductStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new \Exception('Product with ID ' . $product->id . ' is out of stock');
        }
    }

    private function updateProductStock(Product $product, int $quantity): void
    {
        $product->decrement('stock', $quantity);
    }

    private function createOrderItem(Order $order, Product $product, int $quantity): void
    {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    public function getUserOrderHistory($userId): Collection
    {
        return Order::where('user_id', $userId)->with('orderItems.product')->get();
    }
}
