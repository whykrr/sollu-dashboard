---
trigger: always_on
---

# Project Rule: Standarisasi Validasi Feature Plan (SaaS Entitlement)

Dokumen ini adalah aturan baku bagi pengembang dan agen cerdas dalam mengimplementasikan dan memvalidasi **Feature Plan** (fitur berdasarkan tingkatan paket langganan) di Sollu App.

---

## 1. Prinsip Otorisasi Ganda (Dual-Layer Authorization)

Di Sollu App, otorisasi memiliki 2 lapisan independen:

1. **RBAC Permission (Lapisan Pengguna / User):**
   - Menjawab: *"Apakah user ini berhak melakukan aksi ini sesuai jabatannya?"*
   - Backend: `PermissionEnum`, `$this->authorize('permission.name')`, `$user->can()`.
   - Frontend: `v-can="'permission.name'"`, `useAuth()`.
   - Perilaku UI: Elemen disembunyikan/dihapus dari DOM.

2. **Feature Plan (Lapisan Bisnis / Tenant / Subscription):**
   - Menjawab: *"Apakah bisnis ini berlangganan paket yang mencakup fitur ini?"*
   - Backend: `FeatureEnum`, `PlanEnum`, `middleware('plan.feature:' . FeatureEnum::X->value)`, `$user->business->hasFeature(FeatureEnum::X)`.
   - Frontend: `v-feature="'feature_name'"`, `usePlanFeature()`.
   - Perilaku UI: Elemen disembunyikan (default) ATAU ditampilkan terkunci dengan modal upsell (`v-feature.lock`).

> [!WARNING]
> **DILARANG KERAS** memeriksa fitur paket menggunakan permission string (contoh salah: `can('feature.promo')`). Selalu gunakan `FeatureEnum` di backend dan `v-feature` / `usePlanFeature` di frontend!

---

## 2. Standar Frontend Vue 3 (Directives & Composables)

### 2.1. Directive `v-feature` ([resources/js/access-handle.js](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/resources/js/access-handle.js))

Directive global ini terdaftar di `access-handle.js` dan tersedia di seluruh template Vue.

```html
<!-- 1. Single Feature (Default: Menghapus elemen dari DOM jika fitur tidak aktif) -->
<button v-feature="$enums.FeatureEnum.PROMO_MANAGEMENT">Buat Promo Baru</button>

<!-- 2. Multiple Features (OR: Tampil jika salah satu fitur aktif) -->
<div v-feature="[$enums.FeatureEnum.PROMO_MANAGEMENT, $enums.FeatureEnum.CUSTOMER_LOYALTY]">...</div>

<!-- 3. Multiple Features (AND: Tampil HANYA jika SEMUA fitur aktif) -->
<div v-feature.all="[$enums.FeatureEnum.INVENTORY_MANAGEMENT, $enums.FeatureEnum.RECIPE_MANAGEMENT]">...</div>

<!-- 4. Lock / Modal Modifier (SaaS Upsell Overlay) -->
<!-- Elemen tetap tampil dengan efek redup (opacity-65 pointer-events-none), dan secara otomatis disematkan tombol overlay di samping kanan: -->
<!-- - Tombol bertuliskan "Langganan" jika bisnis berada pada status Trial/Free -->
<!-- - Tombol bertuliskan "Tingkatkan Paket" jika bisnis sudah memiliki subscription aktif -->
<!-- - Klik pada tombol overlay / elemen langsung membuka FeatureLockedModal -->
<div v-feature.lock="$enums.FeatureEnum.RECIPE_MANAGEMENT" class="card">
    <h3>Kelola Resep</h3>
    <p>Deskripsi resep...</p>
</div>
```

### 2.2. Composable `usePlanFeature` ([resources/js/Composable/usePlanFeature.js](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/resources/js/Composable/usePlanFeature.js)) & Composable `useEnum`

Gunakan `usePlanFeature` bersama `useEnum` saat logika membutuhkan pengecekan di dalam `<script setup>` (FeatureEnum di-share secara otomatis via Inertia Shared Props):

```js
import { usePlanFeature } from '@/Composable/usePlanFeature'
import { useEnum } from '@/Composable/useEnum'

const { hasFeature, hasAnyFeature, hasAllFeatures, requireFeature, features, subscription } = usePlanFeature()
const { enums } = useEnum()

// Pengecekan kondisional
if (hasFeature(enums.FeatureEnum.PROMO_MANAGEMENT)) {
    // lakukan sesuatu
}

// Intersep aksi dan buka modal upsell jika fitur terkunci
const onOpenRecipePage = () => {
    if (!requireFeature(enums.FeatureEnum.RECIPE_MANAGEMENT)) {
        return // modal terbuka otomatis
    }
    router.visit(route('recipes.index'))
}
```

### 2.3. Menu Navigasi Sidebar ([NavigationNode.vue](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/resources/js/Components/Layout/Sidebar/NavigationNode.vue))

Konfigurasi item navigasi di `resources/js/Composable/Sidebar/*.js` mendukung pembatasan ganda:
```js
{
    type: 'item',
    url: route('promotions.index'),
    label: 'Manajemen Promo',
    permissions: ['promotions.view'], // Dicek via v-can
    feature: 'promo_management',      // Dicek via v-feature
    activeRoute: 'promotions.',
}
```

---

## 3. Standar Backend Laravel 11

1. **Daftarkan Fitur:** Tambahkan case baru di `app/Enums/FeatureEnum.php`.
2. **Petakan ke Paket:** Tentukan paket mana yang mendapatkan fitur tersebut di `app/Enums/PlanEnum.php` (method `systemFeatures()`).
3. **Lindungi Route:** Pasang middleware `plan.feature`:
   ```php
   Route::prefix('recipes')
       ->middleware('plan.feature:' . FeatureEnum::RECIPE_MANAGEMENT->value)
       ->group(...);
   ```
4. **Data Tersedia Otomatis:** Props `props.auth.features` dikirim secara global ke Inertia via `SummaryUser.php`.
