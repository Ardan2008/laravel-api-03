<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): string
    {
        $user = User::factory()->create(['password' => 'secret123']);

        return 'Bearer '.auth('api')->login($user);
    }

    public function test_patch_updates_only_the_given_fields(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'bakery']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Croissant',
            'price' => 18000,
            'stock' => 3,
            'description' => 'Buttery',
        ]);

        $response = $this->withHeader('Authorization', $header)
            ->patchJson("/api/products/{$product->id}", ['stock' => 99]);

        $response->assertOk()
            ->assertJsonPath('data.stock', 99)
            ->assertJsonPath('data.name', 'Croissant')
            ->assertJsonPath('data.price', 18000)
            ->assertJsonPath('data.description', 'Buttery');
    }

    public function test_patch_rejects_invalid_values(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'bakery']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Croissant',
            'price' => 18000,
            'stock' => 3,
        ]);

        $this->withHeader('Authorization', $header)
            ->patchJson("/api/products/{$product->id}", ['stock' => -5])
            ->assertStatus(422)
            ->assertJsonValidationErrors('stock');

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_patch_rejects_unknown_kategori(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'bakery']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Croissant',
            'price' => 18000,
            'stock' => 3,
        ]);

        $this->withHeader('Authorization', $header)
            ->patchJson("/api/products/{$product->id}", ['id_kategori' => 9999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('id_kategori');
    }

    public function test_put_still_requires_every_field(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'bakery']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Croissant',
            'price' => 18000,
            'stock' => 3,
        ]);

        $this->withHeader('Authorization', $header)
            ->putJson("/api/products/{$product->id}", ['stock' => 99])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_replace_image_removes_the_previous_file(): void
    {
        Storage::fake('public');

        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'coffee']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Latte',
            'price' => 25000,
            'stock' => 5,
            'image' => 'products/old.png',
        ]);
        Storage::disk('public')->put('products/old.png', 'old');

        $response = $this->withHeader('Authorization', $header)
            ->post("/api/products/{$product->id}", [
                '_method' => 'PUT',
                'id_kategori' => $kategori->id,
                'name' => 'Latte',
                'price' => 25000,
                'stock' => 5,
                'image' => UploadedFile::fake()->image('new.jpg'),
            ]);

        $response->assertOk();

        $newImage = $product->fresh()->image;
        $this->assertNotSame('products/old.png', $newImage);
        Storage::disk('public')->assertMissing('products/old.png');
        Storage::disk('public')->assertExists($newImage);
    }

    public function test_rejected_image_upload_keeps_the_existing_file(): void
    {
        Storage::fake('public');

        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'coffee']);
        $product = Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Latte',
            'price' => 25000,
            'stock' => 5,
            'image' => 'products/old.png',
        ]);
        Storage::disk('public')->put('products/old.png', 'old');

$this->withHeaders(['Authorization' => $header, 'Accept' => 'application/json'])
            ->post("/api/products/{$product->id}", [
                '_method' => 'PUT',
                'id_kategori' => $kategori->id,
                'name' => 'Latte',
                'price' => 25000,
                'stock' => 5,
                'image' => UploadedFile::fake()->create('payload.pdf', 10, 'application/pdf'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('image');

        $this->assertSame('products/old.png', $product->fresh()->image);
        Storage::disk('public')->assertExists('products/old.png');
    }

    public function test_writes_require_authentication(): void
    {
        $kategori = Kategori::create(['name' => 'coffee']);

        $this->postJson('/api/products', [
            'id_kategori' => $kategori->id,
            'name' => 'Latte',
            'price' => 25000,
            'stock' => 5,
        ])->assertStatus(401);

        $this->postJson('/api/kategoris', ['name' => 'tea'])->assertStatus(401);
    }
}