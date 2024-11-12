<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    private $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);

        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return $this->successResponse(ProductResource::collection($products));
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return $this->successResponse(new ProductResource($product), 'Product created successfully');

    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        if ($product) {
            return $this->successResponse(new ProductResource($product), 'Product found.');
        } else {
            return $this->errorResponse('Product is not available', [], 404);
        }
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());
        return $this->successResponse(new ProductResource($updatedProduct), 'Product updated successfully');
    }

    public function destroy($id)
    {
        $deleted = $this->productService->deleteProduct($id);
        if ($deleted) {
            return $this->successResponse(null, 'Product deleted successfully');
        } else {
            return $this->errorResponse('Product not found', [], 404);
        }
    }
}
