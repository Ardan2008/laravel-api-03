<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): string
    {
        $user = User::factory()->create(['password' => 'secret123']);

        return 'Bearer '.auth('api')->login($user);
    }

    public function test_cannot_delete_kategori_that_is_still_used(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'coffee']);
        Product::create([
            'id_kategori' => $kategori->id,
            'name' => 'Latte',
            'price' => 25000,
            'stock' => 5,
        ]);

        $this->withHeader('Authorization', $header)
            ->deleteJson("/api/kategoris/{$kategori->id}")
            ->assertStatus(409)
            ->assertJsonPath('status', false);

        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id]);
        $this->assertDatabaseHas('products', ['id_kategori' => $kategori->id]);
    }

    public function test_can_delete_kategori_without_products(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'tea']);

        $this->withHeader('Authorization', $header)
            ->deleteJson("/api/kategoris/{$kategori->id}")
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->assertDatabaseMissing('kategoris', ['id' => $kategori->id]);
    }

    public function test_product_referencing_deleted_kategori_is_rejected_by_the_database(): void
    {
        $kategori = Kategori::create(['name' => 'coffee']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Product::create([
            'id_kategori' => $kategori->id + 500,
            'name' => 'Ghost',
            'price' => 1000,
            'stock' => 1,
        ]);
    }

    public function test_duplicate_kategori_name_is_rejected(): void
    {
        $header = $this->authHeader();
        Kategori::create(['name' => 'coffee']);

        $this->withHeader('Authorization', $header)
            ->postJson('/api/kategoris', ['name' => 'coffee'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseCount('kategoris', 1);
    }

    public function test_renaming_kategori_to_an_existing_name_is_rejected(): void
    {
        $header = $this->authHeader();
        Kategori::create(['name' => 'coffee']);
        $tea = Kategori::create(['name' => 'tea']);

        $this->withHeader('Authorization', $header)
            ->putJson("/api/kategoris/{$tea->id}", ['name' => 'coffee'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_kategori_can_be_renamed_to_its_own_name(): void
    {
        $header = $this->authHeader();
        $kategori = Kategori::create(['name' => 'coffee']);

        $this->withHeader('Authorization', $header)
            ->putJson("/api/kategoris/{$kategori->id}", ['name' => 'coffee'])
            ->assertOk();

        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id, 'name' => 'coffee']);
    }
}