---
trigger: always_on
---

# Rule: Sollu App Core (Enums & Feature Plan)

## A. PHP Enums as Single Source of Truth

**1. Prinsip Utama (No Magic Strings)**
Backend (`app/Enums/*.php`) adalah master. DILARANG menggunakan string literal/hardcode di Frontend (Vue) untuk memvalidasi status/tipe (misal: `v-if="status === 'draft'"`).

**2. Distribusi (Inertia)**
Enum didistribusikan otomatis via Inertia Shared Props (`$enums`). Daftarkan enum baru ke array `$frontendEnums` di `app/Support/Enums/FrontendEnumProvider.php`.

**3. Standar Frontend**

- **Template:** Gunakan `$enums.NamaEnum.Kasus`. Contoh: `v-if="item.status === $enums.AdjustmentStatus.Draft"`
- **Script Setup:** Gunakan composable: `const { enums, getOptions, getLabel, getColor } = useEnum()`.
- **Form/Dropdown:** Gunakan `getOptions('NamaEnum')` untuk properti `:options` komponen `DropdownField` / `SelectionGroupField`. Jangan hardcode array opsi!

---

## B. Validasi Feature Plan (SaaS Entitlement)

**1. Dual-Layer Auth**

- **User RBAC:** Hak akses jabatan (`PermissionEnum`, `v-can`). UI: Sembunyikan elemen.
- **Tenant Feature:** Kuota/paket bisnis (`FeatureEnum`, `v-feature`). UI: Tampilkan elemen terkunci (upsell). DILARANG pakai fungsi permission (RBAC) untuk cek fitur paket.

**2. Standar Frontend**

- **Directive:** Gunakan `v-feature="$enums.FeatureEnum.NAME"` atau `v-feature.all=[...]`
- **Tampilan Terkunci:** WAJIB bungkus dengan komponen `<FeatureLock :feature="...">` atau gunakan `<FeatureLockOverlay>`. Hindari `v-feature.lock` manual agar tidak layout thrashing.
- **Script Setup:** Gunakan `const { hasFeature, requireFeature } = usePlanFeature()` bersama `useEnum()`.

**3. Standar Backend**

- Definisikan di `app/Enums/FeatureEnum.php`, lalu mapping paketnya di `app/Enums/PlanEnum.php` (`systemFeatures()`).
- Proteksi route dengan `middleware('plan.feature:' . FeatureEnum::NAME->value)`.
