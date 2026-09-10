---
trigger: always_on
---

# Project Rule: Standarisasi PHP Enum sebagai Single Source of Truth Frontend via Inertia

Dokumen ini adalah aturan baku bagi pengembang dan agen cerdas dalam mengimplementasikan, membagikan, dan menggunakan **Enum** sebagai **Single Source of Truth** antara Backend (Laravel 11) dan Frontend (Vue 3 / Inertia.js 1.2) di Sollu App.

---

## 1. Prinsip Single Source of Truth (Backend as Master)

Di Sollu App, seluruh status proses bisnis, tipe transaksi, jenis entitas, peran, dan kategori **WAJIB** berpusat pada PHP Backed Enum di backend (`app/Enums/*.php`).

> [!WARNING]
> **DILARANG KERAS MENGGUNAKAN MAGIC STRINGS DI FRONTEND!**
> Dilarang menuliskan string literal mentah secara manual di template Vue maupun `<script setup>` untuk validasi kondisi status/tipe yang memiliki representasi Enum di backend (contoh salah: `v-if="item.status === 'draft'"`, `case 'draft':`, `'badge-gray': item.status === 'draft'`).
> Selalu rujuk nilai Enum dari **Inertia Shared Props** melalui `$enums` (di template) atau composable `useEnum()` (di script setup).

---

## 2. Mekanisme Distribusi Otomatis (Inertia Shared Data)

Enum didistribusikan secara otomatis dari backend ke frontend menggunakan fitur bawaan **Inertia Shared Props** (`share()`), tanpa memerlukan API fetch manual atau tool code-generation eksternal.

### 2.1. Registry Backend (`FrontendEnumProvider`)
Seluruh Enum yang dikonsumsi oleh antarmuka pengguna didaftarkan pada [FrontendEnumProvider.php](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/app/Support/Enums/FrontendEnumProvider.php):

```php
// app/Support/Enums/FrontendEnumProvider.php
protected static array $frontendEnums = [
    AdjustmentStatus::class,
    StockOpnameStatus::class,
    PromoStatus::class,
    CustomerGender::class,
    PaymentMethodType::class,
    FeatureEnum::class,
    RoleEnum::class,
    // ...
];
```

### 2.2. Injeksi Otomatis via Middleware Inertia
Prop `enums` dibagikan di [HandleAppInertiaRequests.php](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/app/Http/Middleware/HandleAppInertiaRequests.php) dan [HandleCockpitInertiaRequests.php](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/app/Http/Middleware/HandleCockpitInertiaRequests.php):

```php
'enums' => fn () => FrontendEnumProvider::all(),
```

### 2.3. Struktur Data Terstandarisasi di Frontend
Setiap Enum yang dikirim ke frontend memiliki format berikut:
```json
{
  "AdjustmentStatus": {
    "Draft": "draft",
    "Approved": "approved",
    "Rejected": "rejected",
    "Voided": "voided",
    "_meta": {
      "draft": { "label": "Draf" },
      "approved": { "label": "Disetujui" }
    },
    "_options": [
      { "value": "draft", "label": "Draf" },
      { "value": "approved", "label": "Disetujui" }
    ]
  }
}
```
> [!NOTE]
> Case mapping mendukung PascalCase (`AdjustmentStatus.Draft`) maupun UPPERCASE (`AdjustmentStatus.DRAFT`) untuk mencegah bug inkonsistensi casing.

---

## 3. Standar Penggunaan di Frontend Vue 3

### 3.1. Validasi Kondisi di Template (`v-if`, `v-show`, class binding)

Gunakan variabel global `$enums` (atau `$page.props.enums`):

```html
<!-- 1. Kondisional Aksi Tombol -->
<button 
    v-if="item.status === $enums.AdjustmentStatus.Draft"
    class="btn btn-main"
    @click="editItem(item)"
>
    Edit Draf
</button>

<!-- 2. Feature Plan Gating via $enums.FeatureEnum -->
<div v-feature="$enums.FeatureEnum.INVENTORY_MANAGEMENT">
    <button v-feature.lock="$enums.FeatureEnum.RECIPE_MANAGEMENT" class="btn btn-outline-main">
        Kelola Resep
    </button>
</div>

<!-- 3. Styling Badge Dinamis dari Metadata Backend -->
<span 
    class="badge"
    :class="$enums.StockOpnameStatus._meta[item.status]?.color || 'badge-gray'"
>
    {{ $enums.StockOpnameStatus._meta[item.status]?.label || item.status }}
</span>
```

### 3.2. Logika di `<script setup>` via Composable `useEnum`

Impor dan gunakan composable `@/Composable/useEnum`:

```javascript
<script setup>
import { useEnum } from '@/Composable/useEnum'

const { enums, getOptions, getLabel, getColor } = useEnum()

// 1. Pengecekan Kondisional Logic
const handleProcess = (adjustment) => {
    if (adjustment.status !== enums.AdjustmentStatus.Draft) {
        return
    }
    // Lanjutkan proses draft
}

// 2. Opsi Dropdown Filter Otomatis (Tanpa Magic Array)
const statusFilterOptions = getOptions('AdjustmentStatus')
// Hasil: [{ value: 'draft', label: 'Draf' }, { value: 'approved', label: 'Disetujui' }, ...]
</script>
```

### 3.3. Komponen Form (`DropdownField` & `SelectionGroupField`)

DILARANG menuliskan opsi hardcoded manual seperti `[{ value: 'draft', label: 'Draf' }]` jika Enum-nya sudah ada. Gunakan `getOptions()` dari `useEnum()`:

```vue
<SelectionGroupField
    v-model="form.gender"
    label="Jenis Kelamin"
    :options="getOptions('CustomerGender')"
/>

<DropdownField
    v-model="filter.payment_method"
    label="Metode Pembayaran"
    :options="getOptions('PaymentMethodType')"
/>
```

---

## 4. Alur Menambahkan / Memodifikasi Enum Baru

Saat membuat atau mengubah status/tipe baru:

1. **Definisikan di Backend:**
   Buat/perbarui Backed Enum di `app/Enums/{Name}.php`:
   ```php
   enum OrderStatus: string
   {
       case Pending = 'pending';
       case Paid = 'paid';
       case Cancelled = 'cancelled';

       public function label(): string
       {
           return match ($this) {
               self::Pending => 'Menunggu Pembayaran',
               self::Paid => 'Sudah Dibayar',
               self::Cancelled => 'Dibatalkan',
           };
       }

       public function color(): string
       {
           return match ($this) {
               self::Pending => 'badge-warning',
               self::Paid => 'badge-success',
               self::Cancelled => 'badge-danger',
           };
       }
   }
   ```
2. **Daftarkan di Provider:**
   Tambahkan class enum ke array `$frontendEnums` di `App\Support\Enums\FrontendEnumProvider.php`.
3. **Gunakan di Frontend:**
   Enum langsung otomatis tersedia di `$enums.OrderStatus` dan `useEnum()`.
