<?php

namespace Tests\Unit\Services\App\Master;

use App\Models\Business;
use App\Models\Master\InventoryItem;
use App\Models\Master\Product;
use App\Models\User;
use App\Services\App\Master\AuditLogService;
use App\Services\App\Master\InventoryService;
use App\Services\App\Master\ProductService;
use App\Services\App\Master\RecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogService $auditLogServiceMock;
    protected InventoryService $inventoryServiceMock;
    protected RecipeService $recipeServiceMock;
    protected ProductService $service;
    
    protected User $user;
    protected Business $business;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->user = User::first();
        $this->business = $this->user->business;

        $this->auditLogServiceMock = Mockery::mock(AuditLogService::class);
        $this->auditLogServiceMock->shouldReceive('log')->andReturnNull();

        $this->inventoryServiceMock = Mockery::mock(InventoryService::class);
        
        $this->recipeServiceMock = Mockery::mock(RecipeService::class);
        $this->recipeServiceMock->shouldReceive('syncRecipe')->andReturnNull();

        $this->service = new ProductService(
            $this->auditLogServiceMock,
            $this->inventoryServiceMock,
            $this->recipeServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_basic_product_without_variant()
    {
        $invItem = new InventoryItem(['id' => Str::uuid()->toString()]);
        
        $this->inventoryServiceMock->shouldReceive('createVariantInventory')
            ->once()
            ->andReturn($invItem);

        $data = [
            'business_id' => $this->business->id,
            'name' => 'Basic Product',
            'product_type' => 'basic',
            'base_price' => 10000,
            'has_variant' => false,
            'track_inventory' => true,
        ];

        $product = $this->service->createProduct($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Basic Product', $product->name);
        $this->assertCount(1, $product->prices);
        $this->assertEquals(10000, $product->prices->first()->amount);
        $this->assertEquals($invItem->id, $product->prices->first()->inventory_item_id);
    }

    public function test_it_updates_basic_product_without_variant()
    {
        $product = Product::create([
            'business_id' => $this->business->id,
            'name' => 'Old Name',
            'product_type' => 'basic',
            'has_variant' => false,
            'has_modifier' => false,
            'has_recipe' => false,
            'track_inventory' => true,
            'is_show' => true,
            'sellable' => true,
            'purchasable' => false,
        ]);

        $invItem = InventoryItem::create([
            'business_id' => $this->business->id,
            'product_id' => $product->id,
            'name' => 'Old Name',
            'item_type' => 'variant_sku',
            'track_inventory' => true,
            'min_stock' => 0,
        ]);
        
        $this->inventoryServiceMock->shouldReceive('syncInventoryBalances')->andReturnNull();

        $updateData = [
            'name' => 'New Name',
            'base_price' => 15000,
        ];

        $updatedProduct = $this->service->updateProduct($product, $updateData);

        $this->assertEquals('New Name', $updatedProduct->name);
        $this->assertEquals('New Name', $invItem->fresh()->name);
        $this->assertCount(1, $updatedProduct->prices);
        $this->assertEquals(15000, $updatedProduct->prices->first()->amount);
    }

    public function test_it_creates_bundle_product()
    {
        $component = Product::create([
            'business_id' => $this->business->id,
            'name' => 'Component',
            'product_type' => 'basic',
        ]);

        $data = [
            'business_id' => $this->business->id,
            'name' => 'Bundle Product',
            'product_type' => 'bundle',
            'base_price' => 50000,
            'bundle_items' => [
                [
                    'component_product_id' => $component->id,
                    'qty' => 2,
                ]
            ]
        ];

        $product = $this->service->createProduct($data);

        $this->assertEquals('bundle', $product->product_type);
        $this->assertFalse($product->track_inventory); // Overridden in service
        $this->assertCount(1, $product->bundleItems);
        $this->assertEquals($component->id, $product->bundleItems->first()->component_product_id);
    }
}
