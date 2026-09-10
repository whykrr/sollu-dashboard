<?php

namespace App\Enums;

enum FeatureEnum: string
{
    /*
    |--------------------------------------------------------------------------
    | Penjualan & Kasir (Point of Sale & Transactions)
    |--------------------------------------------------------------------------
    */

    case POS_CASHIER = 'pos_cashier';
    case SHIFT_MANAGEMENT = 'shift_management';
    case CASH_DRAWER = 'cash_drawer';
    case SPLIT_PAYMENT = 'split_payment';
    case INVOICE_DEBT = 'invoice_debt';
    case VOID_REFUND = 'void_refund';

    /*
    |--------------------------------------------------------------------------
    | Produk & Menu (Products & Catalog)
    |--------------------------------------------------------------------------
    */

    case PRODUCT_CATALOG = 'product_catalog';
    case PRODUCT_CATEGORIES = 'product_categories';
    case PRODUCT_VARIANTS = 'product_variants';
    case PRODUCT_MODIFIERS = 'product_modifiers';
    case PRODUCT_BUNDLES = 'product_bundles';
    case RECIPE_MANAGEMENT = 'recipe_management';
    case PRODUCT_IMPORT_EXPORT = 'product_import_export';

    /*
    |--------------------------------------------------------------------------
    | Inventori & Rantai Pasok (Inventory & Supply Chain)
    |--------------------------------------------------------------------------
    */

    case INVENTORY_MANAGEMENT = 'inventory_management';
    case RAW_MATERIALS = 'raw_materials';
    case STOCK_MOVEMENTS = 'stock_movements';
    case STOCK_ADJUSTMENTS = 'stock_adjustments';
    case STOCK_FREEZE = 'stock_freeze';
    case STOCK_OPNAME = 'stock_opname';
    case STOCK_TRANSFERS = 'stock_transfers';
    case SUPPLIER_MANAGEMENT = 'supplier_management';
    case PURCHASE_ORDERS = 'purchase_orders';
    case INVENTORY_IMPORT_EXPORT = 'inventory_import_export';

    /*
    |--------------------------------------------------------------------------
    | Promosi & Pemasaran (Promotions & Marketing)
    |--------------------------------------------------------------------------
    */

    case PROMO_MANAGEMENT = 'promo_management';
    case DISCOUNT_VOUCHERS = 'discount_vouchers';

    /*
    |--------------------------------------------------------------------------
    | Pelanggan & CRM (Customers & Loyalty)
    |--------------------------------------------------------------------------
    */

    case CUSTOMER_MANAGEMENT = 'customer_management';
    case CUSTOMER_LOYALTY = 'customer_loyalty';
    case CUSTOMER_IMPORT_EXPORT = 'customer_import_export';

    /*
    |--------------------------------------------------------------------------
    | Laporan & Analitik (Reports & Analytics)
    |--------------------------------------------------------------------------
    */

    case BASIC_REPORTS = 'basic_reports';
    case ADVANCED_REPORTS = 'advanced_reports';
    case SALES_REPORTS = 'sales_reports';
    case PRODUCT_REPORTS = 'product_reports';
    case STOCK_REPORTS = 'stock_reports';
    case CASHIER_REPORTS = 'cashier_reports';
    case PROMO_REPORTS = 'promo_reports';
    case CUSTOMER_REPORTS = 'customer_reports';
    case REPORT_EXPORT = 'report_export';

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Outlet & Operasional (Outlets & Operations)
    |--------------------------------------------------------------------------
    */

    case MULTI_OUTLET = 'multi_outlet';
    case OPERATIONAL_HOURS = 'operational_hours';
    case RECEIPT_CUSTOMIZATION = 'receipt_customization';
    case TAX_AND_SERVICE_CHARGE = 'tax_and_service_charge';
    case DEVICE_MANAGEMENT = 'device_management';
    case CUSTOM_PAYMENT_METHODS = 'custom_payment_methods';

    /*
    |--------------------------------------------------------------------------
    | Karyawan & Keamanan (Employees & Security)
    |--------------------------------------------------------------------------
    */

    case EMPLOYEE_MANAGEMENT = 'employee_management';
    case ROLE_PERMISSIONS = 'role_permissions';
    case UNLIMITED_USERS = 'unlimited_users';
    case AUDIT_LOGS = 'audit_logs';

    /*
    |--------------------------------------------------------------------------
    | Integrasi & Platform (Integrations & Platform)
    |--------------------------------------------------------------------------
    */

    case PAYMENT_GATEWAY = 'payment_gateway';
    case POS_DEVICE_SYNC = 'pos_device_sync';
    case DEVELOPER_API = 'developer_api';

    /**
     * Nama label tampilan fitur dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            // Penjualan & Kasir
            self::POS_CASHIER => 'Aplikasi Kasir (POS)',
            self::SHIFT_MANAGEMENT => 'Manajemen Shift Kasir',
            self::CASH_DRAWER => 'Manajemen Kas Laci (Cash Drawer)',
            self::SPLIT_PAYMENT => 'Split & Multi Pembayaran',
            self::INVOICE_DEBT => 'Invoice & Piutang Pelanggan',
            self::VOID_REFUND => 'Void & Refund Transaksi',

            // Produk & Menu
            self::PRODUCT_CATALOG => 'Katalog Produk',
            self::PRODUCT_CATEGORIES => 'Kategori Produk',
            self::PRODUCT_VARIANTS => 'Varian Produk',
            self::PRODUCT_MODIFIERS => 'Opsi Tambahan (Modifier / Add-on)',
            self::PRODUCT_BUNDLES => 'Paket Produk (Bundling / Combo)',
            self::RECIPE_MANAGEMENT => 'Manajemen Resep & HPP',
            self::PRODUCT_IMPORT_EXPORT => 'Import & Export Produk',

            // Inventori & Rantai Pasok
            self::INVENTORY_MANAGEMENT => 'Manajemen Inventori & Stok',
            self::RAW_MATERIALS => 'Bahan Baku (Raw Materials)',
            self::STOCK_MOVEMENTS => 'Kartu Stok & Riwayat Mutasi',
            self::STOCK_ADJUSTMENTS => 'Penyesuaian Stok (Stock Adjustment)',
            self::STOCK_FREEZE => 'Pembekuan Stok (Stock Freeze)',
            self::STOCK_OPNAME => 'Stock Opname',
            self::STOCK_TRANSFERS => 'Transfer Stok Antar Outlet',
            self::SUPPLIER_MANAGEMENT => 'Manajemen Pemasok (Supplier)',
            self::PURCHASE_ORDERS => 'Pesanan Pembelian (Purchase Order)',
            self::INVENTORY_IMPORT_EXPORT => 'Import & Export Inventori',

            // Promosi & Pemasaran
            self::PROMO_MANAGEMENT => 'Manajemen Promosi & Diskon',
            self::DISCOUNT_VOUCHERS => 'Kupon & Voucher Diskon',

            // Pelanggan & CRM
            self::CUSTOMER_MANAGEMENT => 'Database Pelanggan',
            self::CUSTOMER_LOYALTY => 'Program Loyalitas & Poin',
            self::CUSTOMER_IMPORT_EXPORT => 'Import & Export Pelanggan',

            // Laporan & Analitik
            self::BASIC_REPORTS => 'Laporan Penjualan Dasar',
            self::ADVANCED_REPORTS => 'Laporan & Analitik Lanjutan',
            self::SALES_REPORTS => 'Laporan Penjualan Detail',
            self::PRODUCT_REPORTS => 'Laporan Performa Produk',
            self::STOCK_REPORTS => 'Laporan Stok & Valuasi Aset',
            self::CASHIER_REPORTS => 'Laporan Kasir & Arus Kas',
            self::PROMO_REPORTS => 'Laporan Efektivitas Promo',
            self::CUSTOMER_REPORTS => 'Laporan Pelanggan',
            self::REPORT_EXPORT => 'Ekspor Laporan (PDF & Excel)',

            // Outlet & Operasional
            self::MULTI_OUTLET => 'Multi Outlet & Cabang',
            self::OPERATIONAL_HOURS => 'Pengaturan Jam Operasional',
            self::RECEIPT_CUSTOMIZATION => 'Kustomisasi Layout Struk',
            self::TAX_AND_SERVICE_CHARGE => 'Pajak & Biaya Layanan',
            self::DEVICE_MANAGEMENT => 'Manajemen Perangkat Kasir & Printer',
            self::CUSTOM_PAYMENT_METHODS => 'Kustomisasi Metode Pembayaran',

            // Karyawan & Keamanan
            self::EMPLOYEE_MANAGEMENT => 'Manajemen Pegawai & Staf',
            self::ROLE_PERMISSIONS => 'Hak Akses Berjenjang (RBAC)',
            self::UNLIMITED_USERS => 'Pengguna Tanpa Batas',
            self::AUDIT_LOGS => 'Log Audit & Jejak Aktivitas',

            // Integrasi & Platform
            self::PAYMENT_GATEWAY => 'Integrasi Pembayaran Digital (QRIS & VA)',
            self::POS_DEVICE_SYNC => 'Sinkronisasi Otomatis Data POS',
            self::DEVELOPER_API => 'Akses API Pengembang (Developer API)',
        };
    }

    /**
     * Penjelasan fungsionalitas fitur bagi pengguna.
     */
    public function description(): string
    {
        return match ($this) {
            // Penjualan & Kasir
            self::POS_CASHIER => 'Akses penuh ke antarmuka transaksi penjualan kasir (POS) berbasis web dan perangkat tablet.',
            self::SHIFT_MANAGEMENT => 'Buka dan tutup shift kasir, tracking selisih kas, dan pergantian jam kerja staf kasir.',
            self::CASH_DRAWER => 'Pencatatan kas masuk, kas keluar, modal awal, dan rekonsiliasi uang fisik di laci kasir.',
            self::SPLIT_PAYMENT => 'Membagi satu tagihan transaksi menggunakan kombinasi berbagai metode pembayaran berbeda.',
            self::INVOICE_DEBT => 'Penerbitan invoice tagihan tempo, penagihan, serta pencatatan cicilan dan pelunasan piutang pelanggan.',
            self::VOID_REFUND => 'Otorisasi pembatalan transaksi tersimpan, void struk kasir, dan proses pengembalian dana transaksi.',

            // Produk & Menu
            self::PRODUCT_CATALOG => 'Manajemen data katalog produk, foto, barcode/SKU, satuan unit (UOM), dan visibilitas penjualan.',
            self::PRODUCT_CATEGORIES => 'Pengelompokan hierarki kategori produk dan pengaturan urutan display kategori menu kasir.',
            self::PRODUCT_VARIANTS => 'Matriks variasi produk multi-dimensi (ukuran, warna, rasa) dengan SKU dan harga khusus.',
            self::PRODUCT_MODIFIERS => 'Pengaturan opsi tambahan produk seperti topping, level gula, ekstra shot dengan tambahan harga.',
            self::PRODUCT_BUNDLES => 'Penjualan paket bundling / combo beberapa produk dengan harga paket promo terintegrasi.',
            self::RECIPE_MANAGEMENT => 'Perhitungan otomatis Harga Pokok Penjualan (HPP) berbasis komposisi bahan baku dan versi resep.',
            self::PRODUCT_IMPORT_EXPORT => 'Import data produk masal melalui template spreadsheet Excel/CSV serta backup data produk.',

            // Inventori & Rantai Pasok
            self::INVENTORY_MANAGEMENT => 'Pelacakan stok real-time, pengingat batas minimum stok, dan alokasi stok per outlet.',
            self::RAW_MATERIALS => 'Pengelolaan inventori khusus bahan baku dapur dan bahan setengah jadi yang tidak dijual langsung.',
            self::STOCK_MOVEMENTS => 'Audit jejak mutasi masuk/keluar stok, FIFO cost layers, dan riwayat pergerakan inventori terperinci.',
            self::STOCK_ADJUSTMENTS => 'Pencatatan selisih barang rusak, hilang, kedaluwarsa, dan penyesuaian stok manual.',
            self::STOCK_FREEZE => 'Membekukan pergerakan stok outlet sementara selama proses audit atau stock opname berlangsung.',
            self::STOCK_OPNAME => 'Audit fisik stok berkala dengan alur draf penghitungan, approval manajer, dan rekonsiliasi otomatis.',
            self::STOCK_TRANSFERS => 'Distribusi dan transfer mutasi stok antar cabang dengan alur pengiriman (ship) dan penerimaan (receive).',
            self::SUPPLIER_MANAGEMENT => 'Pencatatan kontak database vendor/supplier, katalog item supply, dan riwayat pesanan.',
            self::PURCHASE_ORDERS => 'Pembuatan purchase order (PO), alur persetujuan pembelian barang, dan pencatatan barang datang.',
            self::INVENTORY_IMPORT_EXPORT => 'Export rekapitulasi saldo inventori dan import data saldo awal stok via spreadsheet.',

            // Promosi & Pemasaran
            self::PROMO_MANAGEMENT => 'Pembuatan diskon persentase, nominal, buy X get Y, bundling, dengan jadwal dan kuota penggunaan.',
            self::DISCOUNT_VOUCHERS => 'Penerbitan kode kupon dan voucher diskon khusus untuk program kampanye pemasaran.',

            // Pelanggan & CRM
            self::CUSTOMER_MANAGEMENT => 'Pengelolaan direktori profil pelanggan, kontak, preferensi, dan histori transaksi belanja.',
            self::CUSTOMER_LOYALTY => 'Sistem akumulasi poin belanja, tingkatan membership pelanggan, dan penukaran reward loyalitas.',
            self::CUSTOMER_IMPORT_EXPORT => 'Import database kontak pelanggan secara masal dan ekspor riwayat belanja pelanggan.',

            // Laporan & Analitik
            self::BASIC_REPORTS => 'Ringkasan ringkas omzet harian, jumlah transaksi, dan rekapitulasi metode pembayaran dasar.',
            self::ADVANCED_REPORTS => 'Analisis mendalam performa bisnis, profitabilitas margin, tren penjualan, dan estimasi laba kotor.',
            self::SALES_REPORTS => 'Rincian transaksi per periode, filter multi-cabang, grafik jam sibuk, dan performa kasir.',
            self::PRODUCT_REPORTS => 'Laporan produk terlaris (top selling), kontribusi margin keuntungan produk, dan produk lambat terjual.',
            self::STOCK_REPORTS => 'Laporan sisa stok, valuasi nilai aset inventori menggunakan FIFO, dan peringatan stok menipis.',
            self::CASHIER_REPORTS => 'Rekonsiliasi kas per kasir, tracking selisih fisik uang kas (over/short), dan rekap shift operasional.',
            self::PROMO_REPORTS => 'Evaluasi efektivitas promosi, total diskon yang terserap, dan dampak program promo terhadap omzet.',
            self::CUSTOMER_REPORTS => 'Analisis pertumbuhan pelanggan baru vs lama, frekuensi belanja, dan daftar pelanggan bernilai tertinggi.',
            self::REPORT_EXPORT => 'Kemampuan mengunduh dan mencetak seluruh data laporan dalam format PDF berstandar dan file Excel/CSV.',

            // Outlet & Operasional
            self::MULTI_OUTLET => 'Kemampuan mengelola banyak cabang usaha dalam satu akun terpusat dengan pergantian outlet cepat.',
            self::OPERATIONAL_HOURS => 'Pengaturan jadwal jam buka dan tutup outlet per hari kerja untuk operasional kasir.',
            self::RECEIPT_CUSTOMIZATION => 'Kustomisasi logo bisnis, header, catatan kaki nota, dan format cetak kertas struk belanja kasir.',
            self::TAX_AND_SERVICE_CHARGE => 'Konfigurasi persentase pajak (PPN/PB1), service charge, dan aturan pembulatan transaksi otomatis.',
            self::DEVICE_MANAGEMENT => 'Pairing mesin kasir POS via kode OTP, koneksi printer thermal Bluetooth/LAN, dan cash drawer.',
            self::CUSTOM_PAYMENT_METHODS => 'Penambahan opsi metode pembayaran kustom (transfer bank, EDC merchant, e-wallet, piutang).',

            // Karyawan & Keamanan
            self::EMPLOYEE_MANAGEMENT => 'Manajemen data staf/karyawan, kode PIN kasir cepat, dan penugasan karyawan ke cabang tertentu.',
            self::ROLE_PERMISSIONS => 'Pembatasan hak akses staf secara terperinci berbasis jabatan/role (Kasir, Supervisor, Manager).',
            self::UNLIMITED_USERS => 'Bebas menambahkan staf kasir dan admin tanpa batasan kuota jumlah pengguna.',
            self::AUDIT_LOGS => 'Pencatatan riwayat audit aktivitas user saat mengubah harga, menghapus data, dan tindakan sensitif.',

            // Integrasi & Platform
            self::PAYMENT_GATEWAY => 'Penerimaan pembayaran digital QRIS dinamis dan transfer otomatis via payment gateway terintegrasi.',
            self::POS_DEVICE_SYNC => 'Sinkronisasi real-time katalog produk, pelanggan, dan transaksi antara cloud server dan mesin POS.',
            self::DEVELOPER_API => 'Akses API publik dan integrasi eksternal untuk sinkronisasi sistem akuntansi pihak ketiga atau marketplace.',
        };
    }

    /**
     * Kategori grouping fitur.
     */
    public function group(): string
    {
        return match ($this) {
            self::POS_CASHIER,
            self::SHIFT_MANAGEMENT,
            self::CASH_DRAWER,
            self::SPLIT_PAYMENT,
            self::INVOICE_DEBT,
            self::VOID_REFUND => 'pos_and_transactions',

            self::PRODUCT_CATALOG,
            self::PRODUCT_CATEGORIES,
            self::PRODUCT_VARIANTS,
            self::PRODUCT_MODIFIERS,
            self::PRODUCT_BUNDLES,
            self::RECIPE_MANAGEMENT,
            self::PRODUCT_IMPORT_EXPORT => 'products_and_menu',

            self::INVENTORY_MANAGEMENT,
            self::RAW_MATERIALS,
            self::STOCK_MOVEMENTS,
            self::STOCK_ADJUSTMENTS,
            self::STOCK_FREEZE,
            self::STOCK_OPNAME,
            self::STOCK_TRANSFERS,
            self::SUPPLIER_MANAGEMENT,
            self::PURCHASE_ORDERS,
            self::INVENTORY_IMPORT_EXPORT => 'inventory_and_supply_chain',

            self::PROMO_MANAGEMENT,
            self::DISCOUNT_VOUCHERS => 'promotions_and_marketing',

            self::CUSTOMER_MANAGEMENT,
            self::CUSTOMER_LOYALTY,
            self::CUSTOMER_IMPORT_EXPORT => 'customers_and_crm',

            self::BASIC_REPORTS,
            self::ADVANCED_REPORTS,
            self::SALES_REPORTS,
            self::PRODUCT_REPORTS,
            self::STOCK_REPORTS,
            self::CASHIER_REPORTS,
            self::PROMO_REPORTS,
            self::CUSTOMER_REPORTS,
            self::REPORT_EXPORT => 'reports_and_analytics',

            self::MULTI_OUTLET,
            self::OPERATIONAL_HOURS,
            self::RECEIPT_CUSTOMIZATION,
            self::TAX_AND_SERVICE_CHARGE,
            self::DEVICE_MANAGEMENT,
            self::CUSTOM_PAYMENT_METHODS => 'outlets_and_operations',

            self::EMPLOYEE_MANAGEMENT,
            self::ROLE_PERMISSIONS,
            self::UNLIMITED_USERS,
            self::AUDIT_LOGS => 'employees_and_security',

            self::PAYMENT_GATEWAY,
            self::POS_DEVICE_SYNC,
            self::DEVELOPER_API => 'integrations_and_platform',
        };
    }

    /**
     * Label kelompok kategori fitur dalam Bahasa Indonesia.
     */
    public function groupLabel(): string
    {
        return match ($this->group()) {
            'pos_and_transactions' => 'Penjualan & Kasir',
            'products_and_menu' => 'Produk & Menu',
            'inventory_and_supply_chain' => 'Inventori & Rantai Pasok',
            'promotions_and_marketing' => 'Promosi & Pemasaran',
            'customers_and_crm' => 'Pelanggan & CRM',
            'reports_and_analytics' => 'Laporan & Analitik',
            'outlets_and_operations' => 'Outlet & Operasional',
            'employees_and_security' => 'Karyawan & Hak Akses',
            'integrations_and_platform' => 'Integrasi & Platform',
            default => 'Lainnya',
        };
    }

    /**
     * Seluruh nilai string dari fitur.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Array opsi [value => label] untuk form dropdown dan selector.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $feature) => [
                $feature->value => $feature->label(),
            ])
            ->toArray();
    }

    /**
     * Daftar fitur terkelompok berdasarkan grup kategori.
     *
     * @return array<string, array<int, array{value: string, label: string, description: string}>>
     */
    public static function grouped(): array
    {
        $result = [];

        foreach (self::cases() as $feature) {
            $groupName = $feature->groupLabel();
            $result[$groupName][] = [
                'value' => $feature->value,
                'label' => $feature->label(),
                'description' => $feature->description(),
            ];
        }

        return $result;
    }
}
