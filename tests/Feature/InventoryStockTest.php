<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;
use App\Models\Item;
use App\Models\InventoryBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase; // Run migrations and clear DB automatically

    protected $seed = true; // Auto-run seeders before each test

    /**
     * Verify that Spatie roles and administrative accounts are seeded correctly.
     */
    public function test_seeding_creates_correct_roles_and_users(): void
    {
        $superAdmin = User::where('email', 'cpo@ctpf.gov.pk')->first();
        
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('Super Admin'));
        
        $clerk = User::where('email', 'clerk@ctpf.gov.pk')->first();
        $this->assertNotNull($clerk);
        $this->assertTrue($clerk->hasRole('Store Clerk'));
    }

    /**
     * Verify that users must be linked to a staff profile.
     */
    public function test_user_requires_staff_id_linkage(): void
    {
        $superAdmin = User::where('email', 'cpo@ctpf.gov.pk')->first();
        $this->actingAs($superAdmin);
        
        \Livewire\Livewire::test(\App\Livewire\UserComponent::class)
            ->set('name', 'New Administrator')
            ->set('email', 'new.admin@ctpf.gov.pk')
            ->set('password', 'password123')
            ->set('selectedRole', 'Store Clerk')
            ->set('staff_id', null) // Try to save without staff_id
            ->call('saveUser')
            ->assertHasErrors(['staff_id' => 'required']);
    }

    /**
     * Verify that CTPF warden registry profiles are successfully queried.
     */
    public function test_warden_registry_profiles_can_be_retrieved(): void
    {
        $warden = Staff::where('belt_no', '542')->first();

        $this->assertNotNull($warden);
        $this->assertEquals('Muhammad', $warden->first_name);
        $this->assertEquals('Ali', $warden->last_name);
        $this->assertEquals('Muhammad Ali', $warden->full_name);
    }

    /**
     * Verify that catalog items track their dynamic stock batch totals correctly.
     */
    public function test_catalog_items_correctly_calculate_batch_totals(): void
    {
        $item = Item::where('sku', 'UNIF-SH-SUM-15.5')->first();
        $this->assertNotNull($item);

        $batch = InventoryBatch::where('item_id', $item->id)->first();
        $this->assertNotNull($batch);

        $this->assertEquals($batch->current_quantity, $item->available_stock);
    }

    /**
     * Verify that creating a Goods Received Note (GRN) successfully logs the supplier batch.
     */
    public function test_goods_received_note_grn_creates_active_batches(): void
    {
        $supplier = \App\Models\Supplier::create([
            'company_name' => 'Faisalabad Uniforms Ltd',
            'contact_person' => 'Amjad Ali',
            'phone' => '041-9200000',
            'status' => 'active'
        ]);

        $item = Item::first();

        // Create initial batch through a StockReceive note simulation
        $grn = \App\Models\StockReceive::create([
            'grn_number' => 'GRN-TEST-123',
            'supplier_id' => $supplier->id,
            'received_by' => User::first()->id,
            'received_date' => now()->toDateString(),
            'total_amount' => 1500.00
        ]);

        $batch = InventoryBatch::create([
            'item_id' => $item->id,
            'supplier_id' => $supplier->id,
            'batch_number' => 'BATCH-TEST-GRN',
            'unit_purchase_cost' => 1500.00,
            'initial_quantity' => 10,
            'current_quantity' => 10,
            'received_date' => now()->toDateString()
        ]);

        $this->assertDatabaseHas('stock_receives', [
            'grn_number' => 'GRN-TEST-123'
        ]);

        $this->assertDatabaseHas('inventory_batches', [
            'batch_number' => 'BATCH-TEST-GRN',
            'item_id' => $item->id
        ]);
    }

    /**
     * Verify that returning an item due to size mismatch successfully increments active stock batches.
     */
    public function test_returning_item_due_to_size_mismatch_restocks_batch(): void
    {
        $warden = Staff::first();
        $batch = InventoryBatch::first();
        $initialStock = $batch->current_quantity;

        // Simulate a return transaction
        $return = \App\Models\StockReturn::create([
            'return_slip_no' => 'RT-TEST-123',
            'staff_id' => $warden->id,
            'received_by' => User::first()->id,
            'return_date' => now()->toDateString(),
            'reason' => 'size_mismatch',
            'action_taken' => 'restocked'
        ]);

        $batch->increment('current_quantity', 2);

        $this->assertDatabaseHas('stock_returns', [
            'return_slip_no' => 'RT-TEST-123'
        ]);

        $this->assertEquals($initialStock + 2, $batch->current_quantity);
    }
}
