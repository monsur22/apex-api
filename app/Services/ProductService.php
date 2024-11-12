<?php

namespace App\Services;

use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService implements ProductServiceInterface
{
    // Constants for cache keys and duration
    private const ALL_PRODUCTS_CACHE_KEY = 'all_products';
    private const CACHE_DURATION = 60;

    public function getAllProducts(): Collection
    {
        return $this->getCache(self::ALL_PRODUCTS_CACHE_KEY, function () {
            return Product::all();
        });
    }


    public function getProductById(int $id): ?Product
    {
        $cacheKey = $this->getProductCacheKey($id);

        return $this->getCache($cacheKey, function () use ($id) {
            return Product::find($id);
        });
    }

    public function createProduct(array $data): Product
    {
        $product = Product::create($data);
        $this->clearCache(self::ALL_PRODUCTS_CACHE_KEY);

        return $product;
    }


    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        $this->clearCache("product_{$product->id}");
        $this->clearCache(self::ALL_PRODUCTS_CACHE_KEY);

        return $product;
    }

    // Delete a product and clear the relevant cache.
    public function deleteProduct($id): bool
    {
        $product = $this->getProductById($id);
        if ($product) {
            $product->delete();

            $this->clearCache("product_{$id}");
            $this->clearCache(self::ALL_PRODUCTS_CACHE_KEY);

            return true;
        }

        return false;
    }

    private function getCache(string $cacheKey, \Closure $callback)
    {
        return Cache::remember($cacheKey, self::CACHE_DURATION, $callback);
    }


    // Clear the cache for the given key.
    private function clearCache(string $cacheKey): void
    {
        Cache::forget($cacheKey);
    }


    //Generate the cache key for a product based on its ID.
    private function getProductCacheKey(int $id): string
    {
        return "product_{$id}";
    }
}
