---
name: sollu-feature-plan
description: >-
  Feature plan validation, subscription tier entitlements, FeatureEnum, PlanEnum, CheckPlanFeature middleware,
  v-feature directives, and usePlanFeature composable in Sollu App.
  MUST trigger whenever creating or modifying subscription plan features, feature gating, v-feature checks, or upgrade modals.
---

# Sollu Feature Plan Standard (SaaS Entitlement)

## 🚨 Related Skills (Perfect Hook Matrix)
- **`sollu-roles-permissions`**: User-level RBAC authorization (`v-can`, `useAuth`, `PermissionEnum`).
- **`sollu-frontend`**: UI components, navigation, and drawer standards.
- **`sollu-backend`**: Middleware registration, Laravel 11 controller patterns, and `SummaryUser`.
- **`sollu-code-quality`**: ESLint formatting and verification.

Standar pengelolaan dan validasi fitur paket langganan (SaaS Subscription Entitlement) pada Sollu App.

---

## 1. Perbedaan Otorisasi: RBAC vs Feature Plan

| Parameter | RBAC Permission | Feature Plan |
| :--- | :--- | :--- |
| **Entitas** | Pengguna / User (Jabatan) | Bisnis / Merchant (Paket Langganan) |
| **Backend Guard** | `$this->authorize('perm.name')` | `middleware('plan.feature:feature_name')` |
| **Inertia Prop** | `props.auth.permissions` | `props.auth.features` |
| **Vue Directive** | `v-can="'perm.name'"` | `v-feature="'feature_name'"` |
| **Vue Composable** | `useAuth()` (`can()`, `canAny()`) | `usePlanFeature()` (`hasFeature()`, `requireFeature()`) |
| **Handling UI** | Elemen dihapus dari DOM | Dihapus dari DOM atau ditahan dengan modal upgrade (`v-feature.lock`) |

---

## 2. Backend Feature Registration Workflow

Setiap kali modul fitur baru memerlukan pembatasan berdasarkan paket langganan:

1. **Daftarkan Key Fitur:**
   Tambahkan case di Enum `app/Enums/FeatureEnum.php`:
   ```php
   case RECIPE_MANAGEMENT = 'recipe_management';
   ```

2. **Petakan Hak Paket Langganan:**
   Buka `app/Enums/PlanEnum.php` dan tambahkan case tersebut ke `systemFeatures()` paket yang berhak (e.g. `PRO` atau `ULTIMATE`):
   ```php
   self::ULTIMATE => array_merge(self::PRO->systemFeatures(), [
       FeatureEnum::RECIPE_MANAGEMENT,
   ]),
   ```

3. **Pasang Middleware pada Route:**
   Gunakan middleware `plan.feature` di file route (misal `routes/app/recipes.php`):
   ```php
   use App\Enums\FeatureEnum;

   Route::prefix('recipes')
       ->middleware('plan.feature:' . FeatureEnum::RECIPE_MANAGEMENT->value)
       ->group(function () {
           Route::resource('recipes', RecipeController::class);
       });
   ```

4. **Response Otomatis Jika Terkunci:**
   - Request AJAX/JSON: mengembalikan HTTP 403 dengan payload `is_feature_locked: true`.
   - Request Inertia/Web: redirect back dengan session flash `feature_locked`, yang secara otomatis ditangkap oleh `AppLayout.vue` untuk memunculkan `FeatureLockedModal.vue`.

---

## 3. Frontend Validation Standards (`v-feature` & `usePlanFeature`)

### 3.1. Directive Global `v-feature`

Terdaftar di `resources/js/access-handle.js`. Gunakan untuk pembatasan langsung di template Vue:

```html
<!-- 1. Single Feature: Hapus elemen jika tidak punya fitur -->
<button v-feature="$enums.FeatureEnum.PROMO_MANAGEMENT" class="btn btn-main">
    Buat Promo
</button>

<!-- 2. Multiple Features (OR): Tampil jika minimal 1 fitur aktif -->
<div v-feature="[$enums.FeatureEnum.PROMO_MANAGEMENT, $enums.FeatureEnum.CUSTOMER_LOYALTY]">
    ...
</div>

<!-- 3. Multiple Features (AND): Tampil jika SEMUA fitur aktif -->
<div v-feature.all="[$enums.FeatureEnum.INVENTORY_MANAGEMENT, $enums.FeatureEnum.RECIPE_MANAGEMENT]">
    ...
</div>

<!-- 4. Lock Modifier (SaaS Upsell Overlay): Elemen tetap tampil dengan efek redup (opacity-65 pointer-events-none), disematkan tombol overlay responsif di samping kanan ("Langganan" atau "Tingkatkan Paket"), dan intersep klik memunculkan FeatureLockedModal -->
<div v-feature.lock="$enums.FeatureEnum.RECIPE_MANAGEMENT" class="card">
    <div class="card-header">Kelola Resep</div>
    <div class="card-body">Konten resep...</div>
</div>
```

### 3.2. Composable `usePlanFeature` & `useEnum`

Gunakan `@/Composable/usePlanFeature` bersama `@/Composable/useEnum` saat validasi diperlukan di dalam fungsi JavaScript atau `<script setup>` (FeatureEnum disediakan oleh Inertia Shared Props):

```js
import { usePlanFeature } from '@/Composable/usePlanFeature';
import { useEnum } from '@/Composable/useEnum';

const {
    hasFeature,
    hasAnyFeature,
    hasAllFeatures,
    requireFeature,
    features,
    subscription,
} = usePlanFeature();
const { enums } = useEnum();

// Contoh 1: Boolean condition
if (hasFeature(enums.FeatureEnum.PROMO_MANAGEMENT)) {
    // Jalankan kalkulasi atau tampilkan opsi khusus
}

// Contoh 2: Intercept aksi dan picu modal terkunci jika belum berhak
const handleAddRecipe = () => {
    if (!requireFeature(enums.FeatureEnum.RECIPE_MANAGEMENT)) {
        return; // Modal terbuka otomatis via useModalStore
    }

    popUpStore.open(...);
};
```

### 3.3. Menu Navigasi Sidebar (`NavigationNode.vue`)

Daftarkan properti `feature` atau `features` di item navigasi `resources/js/Composable/Sidebar/*.js`:

```js
{
    type: 'item',
    url: route('promotions.index'),
    label: 'Manajemen Promo',
    permissions: ['promotions.view'], // Validasi RBAC via v-can
    feature: 'promo_management',      // Validasi Tiering via v-feature
    activeRoute: 'promotions.',
}
```

---

## 4. Strict Prohibitions
- **DILARANG** mengecek nama paket string mentah (e.g. `subscription.plan.name === 'Paket Pro'`). Selalu cek kapabilitas fiturnya melalui `hasFeature(...)` atau `v-feature`.
- **DILARANG** membuat permission Spatie untuk membatasi paket berbayar (e.g. DILARANG membuat permission `feature.promo`).
- **DILARANG** menggunakan `usePage().props.auth.features` secara manual langsung di dalam template/komponen jika directive `v-feature` atau composable `usePlanFeature` dapat digunakan.
