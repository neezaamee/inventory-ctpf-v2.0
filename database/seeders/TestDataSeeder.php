<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\InventoryBatch;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Demo Staff (Traffic Police Faisalabad Wardens)
        $staffMembers = [
            [
                'belt_no' => '542',
                'cnic' => '33100-1234567-1',
                'first_name' => 'Muhammad',
                'last_name' => 'Ali',
                'rank' => 'Traffic Warden',
                'gender' => 'male',
                'phone_no' => '0300-1234567',
                'current_posting' => 'Kohinoor Circle, Faisalabad',
                'status' => 'active'
            ],
            [
                'belt_no' => '608',
                'cnic' => '33102-7654321-2',
                'first_name' => 'Zainab',
                'last_name' => 'Bibi',
                'rank' => 'Senior Traffic Warden',
                'gender' => 'female',
                'phone_no' => '0312-3456789',
                'current_posting' => 'Samanabad Sector, Faisalabad',
                'status' => 'active'
            ],
            [
                'belt_no' => '1025',
                'cnic' => '33103-9876543-3',
                'first_name' => 'Yasir',
                'last_name' => 'Khan',
                'rank' => 'Traffic Inspector',
                'gender' => 'male',
                'phone_no' => '0321-7654321',
                'current_posting' => 'Headquarters Circle, Faisalabad',
                'status' => 'active'
            ]
        ];

        foreach ($staffMembers as $s) {
            Staff::create($s);
        }

        // 2. Seed Default Supplier
        $supplier = Supplier::create([
            'company_name' => 'Punjab Police Logistics Depot',
            'contact_person' => 'Sub-Inspector Asif',
            'phone' => '042-99211000',
            'email' => 'logistics@punjabpolice.gov.pk',
            'address' => 'Police Lines, Lahore',
            'ntn_number' => '1234567-9',
            'status' => 'active'
        ]);

        // 3. Seed Inventory Categories
        $categories = [
            'Uniforms' => 'Seasonal uniforms (shirts, trousers)',
            'Caps & Badges' => 'Official service caps, metal chest badges, rank shoulder stars',
            'Shoes & Belts' => 'Service boots, leather belts, whistles',
            'Equipment' => 'Wireless sets, umbrellas, safety helmets, raincoats'
        ];

        $catModels = [];
        foreach ($categories as $name => $desc) {
            $catModels[$name] = Category::create([
                'name' => $name,
                'description' => $desc
            ]);
        }

        // 4. Seed Inventory Items with Sizing splits
        $items = [
            [
                'category_name' => 'Uniforms',
                'name' => 'Summer Uniform Shirt',
                'sku' => 'UNIF-SH-SUM-15.5',
                'size_attribute' => '15.5',
                'min_quantity' => 15,
                'unit_of_measure' => 'pcs',
                'is_trackable' => true
            ],
            [
                'category_name' => 'Uniforms',
                'name' => 'Summer Uniform Shirt',
                'sku' => 'UNIF-SH-SUM-16.0',
                'size_attribute' => '16.0',
                'min_quantity' => 15,
                'unit_of_measure' => 'pcs',
                'is_trackable' => true
            ],
            [
                'category_name' => 'Caps & Badges',
                'name' => 'CTPF Official Peak Cap',
                'sku' => 'CAP-PEAK-CTPF-7.5',
                'size_attribute' => '7.5',
                'min_quantity' => 10,
                'unit_of_measure' => 'pcs',
                'is_trackable' => true
            ],
            [
                'category_name' => 'Shoes & Belts',
                'name' => 'Official Leather Service Boots',
                'sku' => 'SHOE-BOOT-LTH-9',
                'size_attribute' => '9',
                'min_quantity' => 8,
                'unit_of_measure' => 'pairs',
                'is_trackable' => true
            ],
            [
                'category_name' => 'Shoes & Belts',
                'name' => 'Official Leather Service Boots',
                'sku' => 'SHOE-BOOT-LTH-10',
                'size_attribute' => '10',
                'min_quantity' => 8,
                'unit_of_measure' => 'pairs',
                'is_trackable' => true
            ],
            [
                'category_name' => 'Equipment',
                'name' => 'Motorola GP338 Wireless Set',
                'sku' => 'EQP-WIRELESS-GP338',
                'size_attribute' => null,
                'min_quantity' => 3,
                'unit_of_measure' => 'pcs',
                'is_trackable' => true
            ]
        ];

        foreach ($items as $it) {
            $catId = $catModels[$it['category_name']]->id;
            
            $itemModel = Item::create([
                'category_id' => $catId,
                'name' => $it['name'],
                'sku' => $it['sku'],
                'size_attribute' => $it['size_attribute'],
                'min_quantity' => $it['min_quantity'],
                'unit_of_measure' => $it['unit_of_measure'],
                'is_trackable' => $it['is_trackable']
            ]);

            // 5. Seed Inventory Batch for each item to make them immediately checkout-ready
            InventoryBatch::create([
                'item_id' => $itemModel->id,
                'supplier_id' => $supplier->id,
                'batch_number' => 'BATCH-2026-SUM',
                'unit_purchase_cost' => mt_rand(1500, 4500),
                'initial_quantity' => mt_rand(30, 80),
                'current_quantity' => mt_rand(5, 45), // Some low, some high to trigger alerts on dashboard
                'received_date' => now()->subDays(15)->toDateString()
            ]);
        }
    }
}
