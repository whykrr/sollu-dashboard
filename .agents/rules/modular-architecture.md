# Project Rule: Standarisasi Arsitektur Modular & Desain Mandiri Antar-Modul (Modular Monolith)

Dokumen ini adalah aturan baku bagi seluruh pengembang dan agen AI di Sollu App untuk memastikan setiap pembuatan fitur baru, penulisan kode, dan refactoring selalu menerapkan pendekatan **Modular Monolith** dengan prinsip **Tinggi Kohesi, Rendah Ketergantungan (*High Cohesion, Low Coupling*)**, batas konteks yang tegas (*Bounded Contexts*), serta dapat diskalakan secara independen per modul (*Scalable per Module*).

---

## 1. Prinsip Utama: Modular Monolith & Bounded Contexts

Sollu App dibangun sebagai aplikasi tunggal (*monolith*) yang secara arsitektural terbagi ke dalam modul-modul mandiri (*bounded contexts*). Setiap modul merepresentasikan area bisnis spesifik yang memiliki data, logika bisnis, dan antarmuka tersendiri.

> [!IMPORTANT]
> **HUKUM OTONOMI MODUL:**
> Setiap modul harus dapat dipahami, diuji, dan direfaktor secara independen tanpa menimbulkan efek domino (*ripple effects*) atau merusak modul lain. Jika Modul A dihapus atau diganti, Modul B tidak boleh mengalami *fatal syntax/runtime crash*.

### Katalog Domain & Batas Modul Resmi Sollu App:
1. **`Inventory`**: Bahan baku, stok opname, penyesuaian (*adjustment*), transfer stok, pesanan pembelian (*PO*), pemasok (*supplier*), pembekuan outlet (*freeze*), dan kalkulasi HPP (*cost layer/FIFO/Average*).
2. **`Master`**: Katalog produk, kategori, modifier, resep produk (*BOM*), varian, dan konfigurasi master dasar.
3. **`Sales` / `Transaction`**: Transaksi kasir/POS, item penjualan, pembayaran kasir, invoice penjualan, dan shift kasir (*cashier drawer*).
4. **`Promotion`**: Diskon penjualan, voucher, aturan promo otomatis, dan kalkulasi potongan harga.
5. **`Customer`**: Profil pelanggan, riwayat transaksi pelanggan, dan loyalitas/tier poin.
6. **`Employee`**: Pengguna karyawan, peran & wewenang (*Spatie RBAC*), dan penugasan outlet.
7. **`Outlet`**: Profil outlet, jam operasional, registrasi perangkat POS (*device*), dan pengaturan outlet.
8. **`Reports`**: Agregasi data laporan analitik (*read-only*) penjualan, produk, stok, kasir, dan promo.
9. **`Subscription` & `Billing`**: Paket langganan SaaS, faktur perpanjangan, kuota fitur (*feature plan*), dan verifikasi pembayaran merchant.
10. **`Cockpit`**: Panel operasional internal superadmin Sollu (terpisah dari aplikasi merchant).

---

## 2. 🚨 Larangan Keras (Strict Prohibitions / Anti-Patterns)

1. **DILARANG Direct Cross-Domain Mutation:**
   Modul dilarang keras melakukan operasi tulis langsung (`insert`, `update`, `delete`, `Model::create`, `->save()`) ke tabel atau Model milik modul lain!
   *Contoh Salah:* `TransactionService` langsung mengeksekusi `InventoryBalance::updateOrCreate(...)` atau `InventoryMovement::create(...)`.
   *Solusi Benar:* Gunakan **Domain Event** (`TransactionCreated` ditangkap oleh listener modul `Inventory`) atau panggil **Public Service Contract** yang disediakan modul `Inventory`.
2. **DILARANG Cross-Module Private UI Import di Frontend:**
   File Vue di dalam `resources/js/Pages/App/{ModuleA}` dilarang keras meng-import komponen form/drawer/modal privat dari folder `resources/js/Pages/App/{ModuleB}/Components/`!
   *Solusi Benar:* Jika suatu komponen visual dibutuhkan oleh 2 atau lebih modul, komponen tersebut **WAJIB** dipindahkan ke `@/Components/` (folder bersama).
3. **DILARANG Menciptakan "God Service":**
   Dilarang membuat satu Service Class raksasa yang menangani logika dari banyak domain sekaligus. Setiap Service harus fokus pada satu tanggung jawab dalam domainnya.
4. **DILARANG Mengoper Mutated Eloquent Model Antar-Modul:**
   Hindari mengoper instance Eloquent Model yang memiliki state kotor (*dirty state*) atau relasi terbuka tak terkendali ke modul lain. Operlah **DTO (Data Transfer Object)**, ID primitif (*UUID*), atau *pure array*.
5. **DILARANG Menggabungkan Definisi Route Antar-Modul:**
   Route web dan internal API merchant wajib dikelompokkan ke dalam file spesifik modul di `routes/app/{module}.php`. Dilarang menuliskan route baru dari berbagai modul secara acak di `routes/app.php`.

---

## 3. Protokol Komunikasi Antar-Modul

Untuk mencegah modul saling bergantung secara langsung (*tight coupling*), komunikasi antar-modul **HANYA** diperbolehkan melalui 3 pola standar berikut:

### Pola A: Domain Events & Event Listeners (Rekomendasi Utama untuk Side Effects)
Gunakan saat suatu aksi di Modul A memicu efek samping di Modul B, C, atau D tanpa Modul A perlu tahu implementasi internalnya.
- **Publisher (Modul Asal):** Hanya memancarkan event dengan data esensial (*immutable payload*).
  ```php
  // app/Events/Transaction/TransactionCompleted.php
  namespace App\Events\Transaction;

  use App\Models\Sales\Transaction;
  use Illuminate\Foundation\Events\Dispatchable;
  use Illuminate\Queue\SerializesModels;

  class TransactionCompleted
  {
      use Dispatchable, SerializesModels;

      public function __construct(public readonly Transaction $transaction) {}
  }
  ```
- **Subscriber (Modul Penerima):** Modul penerima mendengarkan event dan mengelola datanya sendiri di domainnya.
  ```php
  // app/Listeners/Inventory/DeductInventoryStockListener.php
  namespace App\Listeners\Inventory;

  use App\Events\Transaction\TransactionCompleted;
  use App\Services\App\Inventory\StockDeductionService;

  class DeductInventoryStockListener
  {
      public function __construct(protected StockDeductionService $stockDeductionService) {}

      public function handle(TransactionCompleted $event): void
      {
          $this->stockDeductionService->deductForTransaction($event->transaction);
      }
  }
  ```

### Pola B: Public Service Contract / Facade (Panggilan Sinkron)
Gunakan jika Modul A membutuhkan kalkulasi atau validasi data seketika dari Modul B sebelum Modul A dapat menyelesaikan aksinya (misal: menghitung diskon promo sebelum transaksi disimpan).
- Modul penyedia menyediakan Service publik yang bertindak sebagai gerbang (*entry point*) modul.
- Output dari Service publik harus berupa struktur data yang jelas (primitif numerik, array terformat, atau DTO), BUKAN query builder mentah.

### Pola C: Read-Model Aggregation (Khusus Modul Reports & Overview)
Modul pelaporan diperbolehkan membaca data lintas tabel secara *read-only* dengan batasan performa ketat:
- Wajib menggunakan query teroptimasi (eager loading spesifik kolom, index database, limit waktu eksekusi < 5 detik).
- Modul pelaporan **DILARANG** melakukan mutasi data apa pun ke tabel domain yang dibacanya.

---

## 4. Standar Anatomi Direktori Modular

Setiap modul harus mengikuti konvensi penempatan file berikut:

### Backend Structure (`app/`):
```
app/
├── Http/
│   ├── Controllers/App/{Module}/    # Controller khusus modul
│   └── Requests/{Module}/           # Form Requests khusus modul
├── Services/App/{Module}/           # Business logic & domain services
├── Events/{Module}/                 # Domain events yang dipancarkan modul ini
├── Listeners/{Module}/              # Listener milik modul ini yang mendengarkan event luar
├── Models/{Module}/                 # Model Eloquent & scopes milik modul
└── Services/Shared/                 # Cross-cutting utils (hanya jika benar-benar generic)
routes/
└── app/
    └── {module}.php                 # Seluruh routing web & internal API modul ini
tests/
└── Unit/Services/{Module}/          # Unit test terisolasi 100% Mocking untuk modul ini
```

### Frontend Structure (`resources/js/`):
```
resources/js/
├── Pages/App/{Module}/
│   ├── Index.vue                    # Halaman utama modul
│   ├── Components/                  # Komponen PRIVAT milik modul ini
│   │   ├── {Feature}FormPopUp.vue   # Drawer form input
│   │   ├── {Feature}DetailPopUp.vue # Drawer detail
│   │   ├── Filter.vue               # Filter modal khusus tabel ini
│   │   └── SubSection.vue           # Bagian UI spesifik modul
│   └── Composable/                  # Vue composable privat modul (jika diperlukan)
└── Components/                      # Komponen BERSAMA (dipakai >= 2 modul berbeda)
    ├── Form/                        # TextField, DropdownField, SelectionGroupField
    ├── Modals/                      # BaseModal, ConfirmationDialog
    └── Tables/                      # FilterSearch, FilterBadge
```

---

## 5. Checklist Verifikasi Modularitas (Definition of Done)

Sebelum menyelesaikan penulisan fitur atau refactoring kode:

- [ ] **Batasan Domain:** Apakah file baru (Controller, Service, Request, Model) sudah berada di dalam namespace modul yang tepat?
- [ ] **Bebas Direct Mutation:** Apakah ada kode yang memodifikasi tabel modul lain secara langsung? (Jika ada, ubah ke Domain Event atau panggil Public Service modul terkait).
- [ ] **Isolasi UI Frontend:** Apakah ada komponen di `Pages/App/{ModuleA}` yang di-import oleh `{ModuleB}`? (Jika ada, pindahkan ke `resources/js/Components/`).
- [ ] **Routing Terisolasi:** Apakah route baru sudah ditempatkan di `routes/app/{module}.php`?
- [ ] **Unit Test Mandiri:** Apakah Unit Test Service di `tests/Unit/Services/{Module}/` dapat berjalan mandiri dengan 100% Mocking tanpa bergantung pada state database modul lain?
- [ ] **Formatting:** Sudah menjalankan `vendor/bin/pint` dan `npm run fix:eslint`.
