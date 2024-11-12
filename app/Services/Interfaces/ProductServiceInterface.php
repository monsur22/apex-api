<?php

namespace App\Services\Interfaces;

use App\Models\Product;

interface ProductServiceInterface
{
    public function getAllProducts();
    public function getProductById(int $id);
    public function createProduct(array $data);
    public function updateProduct(Product $product, array $data);
    public function deleteProduct($id);
}
