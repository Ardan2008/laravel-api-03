<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Http\Resources\KategoriResource;
// use App\Http\Resources\ProductCollection;
use App\Models\Kategori;
use Illuminate\Http\Response;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::latest()->paginate(10);
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
            'message' => 'Kategori created successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_CREATED);
    }

    public function show(Kategori $kategori)
    {
        return response()->json([
            'status' => true,
            'message' => 'Kategori retrieved successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_OK);
    }

    public function update(KategoriRequest $request, Kategori $kategori)
    {
        $kategori->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Kategori updated successfully',
            'data' => new KategoriResource($kategori),
        ], Response::HTTP_OK);
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kategori deleted successfully',
        ], Response::HTTP_OK);
    }
}