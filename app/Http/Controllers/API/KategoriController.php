<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\Response;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::latest()->get();
        return response()->json(
            KategoriResource::collection($kategoris),
            Response::HTTP_OK
        );
    }

    public function store(KategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_CREATED);
    }

    public function show(Kategori $kategori)
    {
        return response()->json([
            'status' => true,
            'message' => 'Category retrieved successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_OK);
    }

    public function update(KategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_OK);
    }

    public function destroy(Kategori $kategori)
    {
        // foreign key di DB juga menolak, tapi dicek di sini supaya pesannya ramah
        if ($kategori->products()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Category is still used by existing products. Move or delete those products first.',
            ], Response::HTTP_CONFLICT);
        }

        $kategori->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
        ], Response::HTTP_OK);
    }
}