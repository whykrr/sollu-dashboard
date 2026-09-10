---
name: sollu-enums
description: >-
  Standardized PHP Backed Enums as Single Source of Truth for condition validation, status management, feature plan gating, and type safety across Backend (Laravel 11) and Frontend (Vue 3 / Inertia.js 1.2).
  MUST trigger whenever validating conditions, checking statuses or types, adding new enums, populating dropdown options, casting model attributes, or reviewing code for magic string violations.
---

# Sollu Enums Standard: Single Source of Truth for Condition Validation

Panduan baku pengelolaan dan penggunaan **PHP Backed Enum** sebagai sumber kebenaran tunggal (*Single Source of Truth*) untuk seluruh validasi kondisi, tipe transaksi, status proses bisnis, role, dan fitur di Sollu App.

---

## 🚨 Related Skills & Rules (Perfect Hook Matrix)
- **`enums-rule`**: Aturan baku proyek untuk distribusi Enum via Inertia Shared Props ([.agents/rules/enums.md](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/.agents/rules/enums.md)).
- **`sollu-backend`**: Backend Eloquent model attribute casting (`casts()`), request validation (`Rule::enum()`), dan pattern matching (`match`).
- **`sollu-frontend`**: Penggunaan `$enums` di template Vue, `useEnum` di `<script setup>`, dan opsi dropdown formulir terstandarisasi.
- **`sollu-feature-plan`**: Otorisasi fitur paket langganan SaaS (`v-feature="$enums.FeatureEnum.KEY"` dan `$enums.FeatureEnum`).
- **`sollu-code-quality`**: Penegakan larangan magic strings dan verifikasi linter.

---

## 1. Prinsip Utama: Anti-Magic Strings

> [!WARNING]
> **DILARANG KERAS MENGGUNAKAN MAGIC STRINGS DI SELURUH CODEBASE!**
> Dilarang keras menuliskan string literal mentah (seperti `'draft'`, `'approved'`, `'pending'`, `'rejected'`, `'cash'`, `'promo_management'`) untuk validasi kondisi, perbandingan status, pengecekan fitur, atau styling badge jika representasi Enum-nya sudah ada atau seharusnya ada di backend.

| Area | ❌ DILARANG (Salah) | ✅ WAJIB (Benar) |
| :--- | :--- | :--- |
| **Vue Template `v-if`** | `v-if="item.status === 'draft'"` | `v-if="item.status === $enums.AdjustmentStatus.Draft"` |
| **Vue Feature Gating** | `v-feature="'promo_management'"` | `v-feature="$enums.FeatureEnum.PROMO_MANAGEMENT"` |
| **Vue Upsell Lock** | `v-feature.lock="'recipe_management'"` | `v-feature.lock="$enums.FeatureEnum.RECIPE_MANAGEMENT"` |
| **Vue Badge Class** | `:class="item.status === 'approved' ? 'badge-success' : 'badge-gray'"` | `:class="$enums.AdjustmentStatus._meta[item.status]?.color"` |
| **Vue Badge Text** | `{{ item.status === 'approved' ? 'Disetujui' : item.status }}` | `{{ $enums.AdjustmentStatus._meta[item.status]?.label }}` |
| **Vue Script Setup** | `if (item.status === 'draft')` | `if (item.status === enums.AdjustmentStatus.Draft)` via `useEnum()` |
| **Dropdown Options** | `:options="[{ value: 'draft', label: 'Draf' }]"` | `:options="getOptions('AdjustmentStatus')"` via `useEnum()` |
| **Backend Condition** | `if ($item->status === 'draft')` | `if ($item->status === AdjustmentStatus::Draft)` |
| **Backend Validation** | `'status' => 'in:draft,approved'` | `'status' => [Rule::enum(AdjustmentStatus::class)]` |
| **Backend Model Cast** | *Tanpa cast atau `'string'`* | `'status' => AdjustmentStatus::class` di method `casts()` |

---

## 2. Standar Frontend Vue 3 (Inertia Shared Data)

Enum backend diekspos secara otomatis ke frontend melalui middleware Inertia (`FrontendEnumProvider::all()`), sehingga frontend memiliki akses langsung ke konstanta, label, warna badge, dan opsi dropdown tanpa duplikasi kode.

### 2.1. Di Dalam Template Vue (`v-if`, `v-show`, `v-feature`, Class Binding)

Selalu gunakan variabel global `$enums`:

```html
<!-- 1. Kondisional Aksi & Tampilan -->
<button 
    v-if="adjustment.status === $enums.AdjustmentStatus.Draft"
    class="btn btn-main"
    @click="openEdit(adjustment)"
>
    Edit Draf
</button>

<!-- 2. Feature Plan Gating -->
<div v-feature="$enums.FeatureEnum.INVENTORY_MANAGEMENT">
    <button v-feature.lock="$enums.FeatureEnum.RECIPE_MANAGEMENT" class="btn btn-outline-main">
        Kelola Resep
    </button>
</div>

<!-- 3. Badge Dinamis Otomatis dari Backend Metadata -->
<span 
    class="badge" 
    :class="$enums.StockOpnameStatus._meta[item.status]?.color || 'badge-gray'"
>
    {{ $enums.StockOpnameStatus._meta[item.status]?.label || item.status }}
</span>
```

### 2.2. Di Dalam `<script setup>` via Composable `useEnum`

Impor dan gunakan `@/Composable/useEnum`:

```javascript
<script setup>
import { useEnum } from '@/Composable/useEnum'
import { usePlanFeature } from '@/Composable/usePlanFeature'

const { enums, getOptions, getLabel, getColor } = useEnum()
const { hasFeature, requireFeature } = usePlanFeature()

// 1. Pengecekan Kondisional Bisnis
const handleApprove = (adjustment) => {
    if (adjustment.status !== enums.AdjustmentStatus.PendingApproval) {
        return
    }
    // Lanjutkan aksi approval
}

// 2. Feature Plan Check
if (hasFeature(enums.FeatureEnum.PROMO_MANAGEMENT)) {
    // Tampilkan analitik promosi
}

// 3. Dropdown Form Options
const statusOptions = getOptions('AdjustmentStatus')
// Mengembalikan: [{ value: 'draft', label: 'Draf' }, { value: 'approved', label: 'Disetujui' }, ...]
</script>
```

### 2.3. Form Fields (`DropdownField` & `SelectionGroupField`)

Dilarang keras menyusun array opsi manual:

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
    placeholder="Semua Metode"
/>
```

---

## 3. Standar Backend Laravel 11 (PHP 8.3)

### 3.1. Eloquent Model Casting

Seluruh kolom tabel database yang merepresentasikan status, tipe, peran, atau kategori **WAJIB** di-cast ke class Enum terkait di method `casts()` model:

```php
namespace App\Models\Inventory;

use App\Enums\AdjustmentStatus;
use App\Enums\AdjustmentReason;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AdjustmentStatus::class,
            'reason' => AdjustmentReason::class,
            'approved_at' => 'datetime',
        ];
    }
}
```

### 3.2. Perbandingan Kondisional & Pattern Matching

Dengan attribute casting, attribute model menghasilkan instance objek Enum. Gunakan perbandingan identitas `===` langsung terhadap case Enum:

```php
// ✅ BENAR:
if ($adjustment->status === AdjustmentStatus::Draft) {
    // ...
}

// ✅ BENAR (Pattern matching komprehensif):
$canEdit = match ($adjustment->status) {
    AdjustmentStatus::Draft, AdjustmentStatus::Rejected => true,
    AdjustmentStatus::Approved, AdjustmentStatus::Voided => false,
};

// ❌ SALAH (Magic string):
if ($adjustment->status === 'draft') // Fatal bug / selalu false karena objek dibandingkan dengan string!
if ($adjustment->status->value === 'draft') // Magic string terlarang!
```

### 3.3. Request Validation

Gunakan `Illuminate\Validation\Rule::enum()` di FormRequest:

```php
use App\Enums\AdjustmentReason;
use App\Enums\AdjustmentStatus;
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'status' => ['required', Rule::enum(AdjustmentStatus::class)],
        'reason' => ['nullable', Rule::enum(AdjustmentReason::class)],
    ];
}
```

---

## 4. Alur Menambahkan Enum Baru

Jika terdapat status, tipe, atau kategori baru yang perlu dibuat:

1. **Definisikan di `app/Enums/{Name}.php`:**
   Gunakan PHP Backed Enum (tipe `string` atau `int`), sertakan method pembantu `label(): string`, `color(): string` (jika badge UI), `values(): array`, dan `options(): array`.
   ```php
   namespace App\Enums;

   enum OrderStatus: string
   {
       case Pending = 'pending';
       case Processing = 'processing';
       case Completed = 'completed';
       case Cancelled = 'cancelled';

       public function label(): string
       {
           return match ($this) {
               self::Pending => 'Menunggu Pembayaran',
               self::Processing => 'Sedang Diproses',
               self::Completed => 'Selesai',
               self::Cancelled => 'Dibatalkan',
           };
       }

       public function color(): string
       {
           return match ($this) {
               self::Pending => 'badge-warning',
               self::Processing => 'badge-info',
               self::Completed => 'badge-success',
               self::Cancelled => 'badge-danger',
           };
       }

       public static function values(): array
       {
           return array_column(self::cases(), 'value');
       }

       public static function options(): array
       {
           return collect(self::cases())
               ->mapWithKeys(fn (self $item) => [$item->value => $item->label()])
               ->toArray();
       }
   }
   ```

2. **Daftarkan di `FrontendEnumProvider.php`:**
   Tambahkan class Enum ke array `$frontendEnums` di `app/Support/Enums/FrontendEnumProvider.php`:
   ```php
   protected static array $frontendEnums = [
       // ...
       OrderStatus::class,
   ];
   ```

3. **Gunakan di Model, Route, dan Frontend:**
   - Model: daftarkan di `casts()`.
   - Frontend: langsung tersedia di `$enums.OrderStatus` dan `useEnum()`.

---

## 5. Checklist Verifikasi Anti-Magic Strings

Sebelum menyelesaikan tugas atau merge kode:
- [ ] Tidak ada string literal mentah status/tipe yang dibandingkan dengan `===` di template Vue (`v-if="status === '...'"`).
- [ ] Seluruh `v-feature` menggunakan `$enums.FeatureEnum.FEATURE_NAME`.
- [ ] Tidak ada hardcoded array opsi dropdown untuk tipe/status yang sudah memiliki Enum.
- [ ] Seluruh atribut status pada Eloquent model telah memiliki `casts()` ke Enum terkait.
- [ ] Validasi FormRequest menggunakan `Rule::enum(MyEnum::class)`.
- [ ] Format kode lolos `vendor/bin/pint` dan `npm run fix:eslint`.
