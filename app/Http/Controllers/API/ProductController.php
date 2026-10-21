<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function storeImage(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store('products', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function index()
    {
        $products = Product::with('kategori')->latest()->get();
        return response()->json(
            ProductResource::collection($products),
            Response::HTTP_OK
        );
    }


    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $product = Product::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product),
        ], Response::HTTP_CREATED);
    }


    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'data' => new ProductResource($product)
        ], Response::HTTP_OK);
    }


    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // simpan dulu file baru, baru hapus yang lama: kalau upload gagal,
            // gambar yang masih terpakai tidak ikut hilang
            $oldImage = $product->image;
            $data['image'] = $this->storeImage($request->file('image'));

            $product->update($data);
            $this->deleteImage($oldImage);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product),
            ], Response::HTTP_OK);
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($product->image);
            $data['image'] = null;
        }

        $product->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product),
        ], Response::HTTP_OK);
    }


    public function destroy(Product $product)
    {
        $this->deleteImage($product->image);

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], Response::HTTP_OK);
    }
}