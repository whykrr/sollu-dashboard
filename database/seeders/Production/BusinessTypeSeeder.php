<?php

namespace Database\Seeders\Production;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featuresRetail = [
            'pos_cashier', 'shift_management', 'cash_drawer', 'split_payment', 'invoice_debt', 'void_refund',
            'product_catalog', 'product_categories', 'product_variants', 'product_bundles', 'product_import_export',
            'inventory_management', 'stock_movements', 'stock_adjustments', 'stock_freeze', 'stock_opname', 'stock_transfers', 'supplier_management', 'purchase_orders', 'inventory_import_export',
            'promo_management', 'discount_vouchers', 'customer_management', 'customer_loyalty', 'customer_import_export',
            'basic_reports', 'advanced_reports', 'sales_reports', 'product_reports', 'stock_reports', 'cashier_reports', 'promo_reports', 'customer_reports', 'report_export',
            'multi_outlet', 'operational_hours', 'receipt_customization', 'tax_and_service_charge', 'device_management', 'custom_payment_methods',
            'employee_management', 'role_permissions', 'unlimited_users', 'audit_logs',
            'payment_gateway', 'pos_device_sync', 'developer_api'
        ];

        $featuresFnB = [
            'pos_cashier', 'shift_management', 'cash_drawer', 'split_payment', 'invoice_debt', 'void_refund',
            'product_catalog', 'product_categories', 'product_variants', 'product_modifiers', 'product_bundles', 'recipe_management', 'product_import_export',
            'inventory_management', 'raw_materials', 'stock_movements', 'stock_adjustments', 'stock_freeze', 'stock_opname', 'stock_transfers', 'supplier_management', 'purchase_orders', 'inventory_import_export',
            'promo_management', 'discount_vouchers', 'customer_management', 'customer_loyalty', 'customer_import_export',
            'basic_reports', 'advanced_reports', 'sales_reports', 'product_reports', 'stock_reports', 'cashier_reports', 'promo_reports', 'customer_reports', 'report_export',
            'multi_outlet', 'operational_hours', 'receipt_customization', 'tax_and_service_charge', 'device_management', 'custom_payment_methods',
            'employee_management', 'role_permissions', 'unlimited_users', 'audit_logs',
            'payment_gateway', 'pos_device_sync', 'developer_api'
        ];

        $featuresService = [
            'pos_cashier', 'shift_management', 'cash_drawer', 'split_payment', 'invoice_debt', 'void_refund',
            'product_catalog', 'product_categories', 'product_variants', 'product_bundles', 'product_import_export',
            'inventory_management', 'stock_movements', 'stock_adjustments', 'stock_freeze', 'stock_opname', 'stock_transfers', 'supplier_management', 'purchase_orders', 'inventory_import_export',
            'promo_management', 'discount_vouchers', 'customer_management', 'customer_loyalty', 'customer_import_export',
            'basic_reports', 'advanced_reports', 'sales_reports', 'product_reports', 'stock_reports', 'cashier_reports', 'promo_reports', 'customer_reports', 'report_export',
            'multi_outlet', 'operational_hours', 'receipt_customization', 'tax_and_service_charge', 'device_management', 'custom_payment_methods',
            'employee_management', 'role_permissions', 'unlimited_users', 'audit_logs',
            'payment_gateway', 'pos_device_sync', 'developer_api'
        ];

        $businessTypes = [
            ['code' => 'minimarket', 'name' => 'Minimarket', 'is_visible' => true, 'features' => $featuresRetail],
            ['code' => 'grocery', 'name' => 'Grocery / Sembako', 'is_visible' => true, 'features' => $featuresRetail],
            ['code' => 'convenience_store', 'name' => 'Toserba', 'is_visible' => true, 'features' => $featuresRetail],
            ['code' => 'fashion_store', 'name' => 'Toko Fesyen', 'is_visible' => true, 'features' => $featuresRetail],
            ['code' => 'coffee_shop', 'name' => 'Coffee Shop', 'is_visible' => true, 'features' => $featuresFnB],
            ['code' => 'restaurant', 'name' => 'Restoran', 'is_visible' => true, 'features' => $featuresFnB],
            ['code' => 'food_stall', 'name' => 'Kedai Makanan', 'is_visible' => true, 'features' => $featuresFnB],
            ['code' => 'bakery', 'name' => 'Bakery', 'is_visible' => true, 'features' => $featuresFnB],
            ['code' => 'laundry', 'name' => 'Laundry', 'is_visible' => false, 'features' => $featuresService],
            ['code' => 'barbershop', 'name' => 'Barbershop', 'is_visible' => false, 'features' => $featuresService],
            ['code' => 'salon', 'name' => 'Salon, Spa & Beauty', 'is_visible' => false, 'features' => $featuresService],
            ['code' => 'repair_shop', 'name' => 'Bengkel', 'is_visible' => false, 'features' => $featuresService],
            ['code' => 'pharmacy', 'name' => 'Apotek', 'is_visible' => false, 'features' => $featuresRetail],
            ['code' => 'vape_store', 'name' => 'Vape Store', 'is_visible' => false, 'features' => $featuresRetail],
            ['code' => 'thrift_store', 'name' => 'Toko Thrift', 'is_visible' => false, 'features' => $featuresRetail],
        ];

        foreach ($businessTypes as $type) {
            BusinessType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'is_visible' => $type['is_visible'],
                    'features' => $type['features'],
                ]
            );
        }

        /*
        $businessTypes->updateOrCreate(['code' => 'car_wash', 'name' => 'Car Wash', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'repair_shop', 'name' => 'Toko Reparasi', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'photo_studio', 'name' => 'Studio Foto', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'electronic_store', 'name' => 'Toko Elektronik', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'phone_store', 'name' => 'Toko HP & Gadget', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'computer_store', 'name' => 'Toko Komputer', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'book_store', 'name' => 'Toko Buku', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'toy_store', 'name' => 'Toko Mainan', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'cosmetic_store', 'name' => 'Toko Kosmetik', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'hardware_store', 'name' => 'Toko Bangunan / Hardware', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'furniture_store', 'name' => 'Furniture & Home Living', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'jewelry_store', 'name' => 'Toko Perhiasan', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'sports_store', 'name' => 'Toko Olahraga', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'automotive_store', 'name' => 'Toko Otomotif', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'florist', 'name' => 'Toko Bunga', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'souvenir_store', 'name' => 'Toko Souvenir', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'optical_store', 'name' => 'Optik', 'is_visible' => true]);
        $businessTypes->updateOrCreate(['code' => 'baby_store', 'name' => 'Toko Bayi', 'is_visible' => true]);
         */
    }
}
