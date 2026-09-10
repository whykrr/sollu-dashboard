---
name: sollu-modular
description: >-
  Modular Monolith architecture, domain boundary enforcement, and cross-module decoupling standards for Sollu App.
  MUST trigger whenever designing new features, writing code, refactoring domain logic, creating or modifying
  controllers, services, events, listeners, routes, or frontend page structures to enforce high cohesion,
  loose coupling, independent module scalability, and prevent cross-domain direct mutations or component leaks.
---

# Sollu Modular Architecture & Decoupling Standards

## 🚨 Related Skills & Rules (Perfect Hook Matrix)
- **`modular-architecture.md`**: Project rule for strict bounded contexts, cross-module boundaries, and prohibition of direct cross-table mutations ([.agents/rules/modular-architecture.md](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/.agents/rules/modular-architecture.md)).
- **`sollu-core-architecture`**: Core system architecture, technology stack, and directory conventions.
- **`sollu-backend`**: Thin controller pattern, domain service layer, and Eloquent query optimizations.
- **`sollu-frontend`**: Vue 3 Composition API, PopUpPage drawers, and component placement standards.
- **`sollu-unit-testing`**: MANDATORY 100% Mocking Unit Tests for Service Layer in `tests/Unit/Services/{Module}/`.
- **`sollu-enums`**: Single Source of Truth PHP Enums across backend and frontend.
- **`sollu-feature-plan`**: SaaS plan feature gating per module (`v-feature`, `FeatureEnum`).
- **`sollu-code-quality`**: Pint and ESLint linters and Definition of Done verification.

Standar baku arsitektur **Modular Monolith** di Sollu App untuk memastikan setiap modul memiliki otonomi tinggi (*high cohesion*), batas domain yang tegas (*bounded contexts*), tidak saling bergantung secara kaku (*loose coupling*), dan dapat diskalakan secara independen (*scalable per module*).

---

## 1. Domain & Bounded Context Directory Matrix

Setiap fitur dalam Sollu App harus ditempatkan ke dalam salah satu Bounded Context resmi:

| Bounded Context | Backend Controller & Service | Eloquent Models | Routes | Frontend Inertia Pages |
| :--- | :--- | :--- | :--- | :--- |
| **`Inventory`** | `App\Http\Controllers\App\Inventory\`<br>`App\Services\App\Inventory\` | `App\Models\Inventory\` | `routes/app/inventories.php` | `resources/js/Pages/App/Inventory/` |
| **`Master`** | `App\Http\Controllers\App\Master\`<br>`App\Services\App\Master\` | `App\Models\Master\` | `routes/app/masters.php` | `resources/js/Pages/App/Master/` |
| **`Sales` / `Transaction`** | `App\Http\Controllers\App\Transaction\`<br>`App\Services\App\Transaction\` | `App\Models\Sales\` | `routes/app/transactions.php` | `resources/js/Pages/App/Transaction/` |
| **`Promotion`** | `App\Http\Controllers\App\PromotionController`<br>`App\Services\App\PromoService` | `App\Models\Promo*` | `routes/app/promotions.php` | `resources/js/Pages/App/Promotion/` |
| **`Customer`** | `App\Http\Controllers\App\CustomerController`<br>`App\Services\App\CustomerService` | `App\Models\Master\Customer` | `routes/app/customers.php` | `resources/js/Pages/App/Customer/` |
| **`Employee`** | `App\Http\Controllers\App\EmployeeController`<br>`App\Services\App\EmployeeService` | `App\Models\User`, `Role`, `Permission` | `routes/app/employees.php` | `resources/js/Pages/App/Employee/` |
| **`Outlet` & `Settings`** | `App\Http\Controllers\App\Settings\`<br>`App\Services\App\Outlet\` | `App\Models\Outlet*`, `OutletSetting` | `routes/app/settings.php` | `resources/js/Pages/App/Settings/` |
| **`Reports`** | `App\Http\Controllers\App\Reports\`<br>`App\Services\App\Reports\` | Read-only queries | `routes/app/reports.php` | `resources/js/Pages/App/Reports/` |
| **`Subscription`** | `App\Services\App\SubscriptionService` | `App\Models\Subscription*`, `Invoice` | `routes/app/settings.php` | `resources/js/Pages/App/Settings/Subscription/` |
| **`Cockpit`** | `App\Http\Controllers\Cockpit\`<br>`App\Services\Cockpit\` | `App\Models\Cockpit*` | `routes/cockpit.php` | `resources/js/Pages/Cockpit/` |

---

## 2. Decoupling Patterns: Protokol Komunikasi Antar-Modul

### Pola 1: Event-Driven Side-Effects (Asynchronous / Synchronous Listeners)
> [!TIP]
> Gunakan ketika Modul A selesai melakukan aksinya dan ingin memberitahu sistem tanpa peduli siapa yang merespon (misal: Transaksi Selesai → Potong Stok, Tambah Poin Member, Catat Log).

```php
// 1. Modul Sales mendefinisikan & menembakkan Event
namespace App\Events\Transaction;

use App\Models\Sales\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Transaction $transaction) {}
}

// Di TransactionService:
event(new TransactionCompleted($transaction));
```

```php
// 2. Modul Inventory menangani side effect secara mandiri di domainnya
namespace App\Listeners\Inventory;

use App\Events\Transaction\TransactionCompleted;
use App\Services\App\Inventory\StockDeductionService;

class DeductStockOnTransactionListener
{
    public function __construct(protected StockDeductionService $stockDeductionService) {}

    public function handle(TransactionCompleted $event): void
    {
        $this->stockDeductionService->deductForTransaction($event->transaction);
    }
}
```

### Pola 2: Public Service Contract / Facade (Panggilan Sinkron)
> [!IMPORTANT]
> Jika Modul A membutuhkan kalkulasi atau validasi dari Modul B sebelum menyelesaikan aksinya (misal: Menghitung diskon promo saat checkout).

- **Aturan Input/Output:** Modul B menyediakan Service publik. Parameter dan return value harus bertipe data jelas (DTO, float, boolean, atau pure array).
- **Dilarang keras:** Mengembalikan query builder yang belum dieksekusi atau model Eloquent yang dapat di-mutate sembarangan oleh Modul A.

```php
namespace App\Services\App\Promotion;

class PromoEvaluationService
{
    /**
     * Hitung diskon yang berlaku untuk item transaksi secara terisolasi.
     * Return: array terstruktur ['discount_amount' => float, 'promo_id' => ?string]
     */
    public function evaluateItemDiscount(string $inventoryItemId, float $price, float $qty, string $outletId): array
    {
        // Seluruh logika promo terenkapsulasi di dalam modul Promotion
        // Modul Sales cukup memanggil method ini tanpa perlu tahu tabel 'promos'
        return [
            'discount_amount' => 15000.0,
            'promo_id'        => 'uuid-promo',
        ];
    }
}
```

---

## 3. Panduan Langkah-demi-Langkah Pembuatan Fitur Baru (Modular-First)

Saat membuat fitur baru, ikuti urutan berikut:

1. **Identifikasi Bounded Context:** Tentukan modul mana yang menjadi pemilik utama (*owner*) data & alur kerja ini.
2. **Isolasi Routing:** Tambahkan route hanya pada `routes/app/{module}.php`. Pastikan nama route konsisten berawalan `{module}.{feature}.*`.
3. **Form Request & Controller Terisolasi:** Buat di `App\Http\Requests\{Module}\` dan `App\Http\Controllers\App\{Module}\`. Controller tetap *thin*, hanya memvalidasi hak akses dan memanggil service.
4. **Service Terenkapsulasi:** Buat di `App\Services\App\{Module}\`. Jika service melebihi 500 baris, gunakan *Split-File Single Action Pattern* (`Create...Service.php`, `Update...Service.php`).
5. **Komunikasi Keluar:** Jika fitur membutuhkan data modul lain, gunakan Public Service modul tersebut. Jika fitur menimbulkan dampak samping ke modul lain, pancarkan Domain Event.
6. **Frontend Halaman & Komponen Privat:**
   - Halaman utama di `resources/js/Pages/App/{Module}/Index.vue`.
   - Form drawer di `resources/js/Pages/App/{Module}/Components/{Feature}FormPopUp.vue`.
   - Detail drawer di `resources/js/Pages/App/{Module}/Components/{Feature}DetailPopUp.vue`.
   - Filter modal di `resources/js/Pages/App/{Module}/Components/Filter.vue`.
7. **Unit Test Service Mandiri (100% Mocking):** Buat unit test di `tests/Unit/Services/{Module}/...` yang menguji seluruh branch logic tanpa koneksi ke database.

---

## 4. Refactoring Playbook: Memutus Ketergantungan Erat (Decoupling)

Jika menemukan kode lama yang saling bergantung erat (*tightly coupled*), lakukan refactoring dengan langkah-langkah berikut:

### Kasus 1: Service Modul A Menulis Langsung ke Tabel Modul B
- **Masalah:** `TransactionService` memanggil `InventoryBalance::updateOrCreate(...)` langsung di dalam kodenya.
- **Refactoring:**
  1. Buat event `App\Events\Transaction\TransactionCompleted`.
  2. Pindahkan logika pemotongan stok ke `App\Services\App\Inventory\StockDeductionService`.
  3. Buat listener `App\Listeners\Inventory\DeductStockOnTransactionListener` yang memanggil service tersebut.
  4. Hapus dependensi `InventoryBalance` dan `InventoryMovement` dari `TransactionService`.

### Kasus 2: Service Modul A Memeriksa Logika Bisnis Modul B Manual
- **Masalah:** `TransactionService` melakukan query tabel `promos`, memvalidasi tanggal promo, dan menghitung persentase diskon secara manual.
- **Refactoring:**
  1. Ekstrak logika tersebut ke `App\Services\App\Promotion\PromoEvaluationService`.
  2. Inject `PromoEvaluationService` ke `TransactionService`.
  3. Panggil method `evaluateItemDiscount()` yang mengembalikan angka diskon murni.

### Kasus 3: Frontend Meng-import Komponen Antar Halaman Berbeda
- **Masalah:** `Pages/App/Sales/Index.vue` meng-import `./Components/CustomerModal.vue` dari `Pages/App/Customer/Components/`.
- **Refactoring:**
  1. Evaluasi apakah modal tersebut bersifat umum atau spesifik.
  2. Jika dipakai lintas modul, pindahkan ke `resources/js/Components/Modals/CustomerSelectModal.vue` atau gunakan async search API via `@/Components/Form/AsyncSelectField.vue`.
  3. Update import path di kedua halaman agar merujuk ke `@/Components/...`.

---

## 5. Checklist Verifikasi Modularitas (Definition of Done)

Sebelum menyatakan tugas pembuatan fitur atau refactoring selesai:

- [ ] **Struktur Direktori:** Tidak ada file yang tersimpan di luar namespace modulnya.
- [ ] **Zero Cross-Table Writes:** Tidak ada mutasi langsung ke tabel/model milik modul lain.
- [ ] **Event-Driven Side Effects:** Efek samping lintas modul di-decouple menggunakan Domain Events & Listeners.
- [ ] **Frontend UI Isolation:** Tidak ada import privat antar-folder `Pages/App/{ModuleA}` dan `{ModuleB}`.
- [ ] **Route Isolation:** Route tersimpan rapi di `routes/app/{module}.php`.
- [ ] **Testing:** Unit test service di `tests/Unit/Services/{Module}/` lulus dengan 100% Mocking.
- [ ] **Linting:** Berhasil menjalankan `vendor/bin/pint` dan `npm run fix:eslint`.
