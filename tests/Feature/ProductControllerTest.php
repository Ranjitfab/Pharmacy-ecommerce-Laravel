<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_create_form_uses_the_store_category_field(): void
    {
        $response = $this->get(route('products.create'));

        $response->assertOk();
        $response->assertSee('name="product_category_id"', false);
        $response->assertDontSee('name="procuct_category_id"', false);
    }

    public function test_valid_product_submission_creates_the_product_and_redirects(): void
    {
        $category = ProductCategory::create([
            'name' => 'Medicine',
        ]);
        $supplier = Supplier::create([
            'name' => 'Sample Supplier',
        ]);

        $response = $this->post(route('products.store'), [
            'product_category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Sample Product',
            'description' => 'Product description',
            'price' => '12.50',
            'stock' => 5,
            'expiration_date' => null,
        ]);

        $response->assertRedirectToRoute('products.index');
        $response->assertSessionHas('success', 'Product created successfully');
        $this->assertDatabaseHas('products', [
            'product_category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Sample Product',
            'description' => 'Product description',
            'price' => '12.50',
            'stock' => 5,
        ]);
    }
}
