---
name: sollu-frontend
description: >-
  Frontend UI development standards for Sollu App (Vue 3 Composition API, Inertia.js 1.2, Tailwind CSS v4).
  MUST trigger whenever creating or modifying Vue components, Inertia page layouts (MainPage), custom form fields (@/Components/Form/),
  form field spacing (max scale-2), PopUpPage side drawers (usePopUpStore), center modal dialogs (useModalStore),
  Teleport footers (#popUpFooter), or table filter patterns.
---

# Sollu Frontend Rules (Vue 3 / Inertia / Tailwind v4)

## 🚨 Related Skills & Rules (Perfect Hook Matrix)
- **`sollu-modular`**: Modular Monolith architecture, bounded contexts, and cross-module decoupling standards ([.agents/rules/modular-architecture.md](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/.agents/rules/modular-architecture.md)).
- **`sollu-enums`**: Single Source of Truth PHP Enum via Inertia Shared Props, template `$enums`, `useEnum` composable, and strict prohibition of magic strings in condition validation.
- **`sollu-feature-plan`**: SaaS Feature Plan validation (`v-feature`, `v-feature.lock`, `usePlanFeature`, `$enums.FeatureEnum`).
- **`sollu-integration-testing`**: MANDATORY visual & functional browser verification with `browsermcp`.
- **`sollu-roles-permissions`**: Frontend RBAC authorization using `@/Composable/useAuth` and `v-can`.
- **`sollu-code-quality`**: ESLint formatting (`npm run fix:eslint`) and Vite build verification (`npm run build`).

Standard pengembangan antarmuka (UI) Sollu App berbasis Vue 3 (Composition API `<script setup>`), Inertia.js 1.2, dan Tailwind CSS v4.

## 1. 🚨 Anti-Hallucination Core Rules

1. **NO RAW HTML FORMS & MANDATORY REUSABLE COMPONENTS:** Selalu gunakan komponen `@/Components/Form/` (`TextField`, `TextareaField`, `DropdownField`, `NumberField`, `Switch`, `CheckboxField`, `RadioField`, `SelectionGroupField`, `AsyncSelectField`, `AsyncOutletDropdown`) untuk setiap elemen formulir. DILARANG KERAS menggunakan tag `<input>`, `<select>`, atau `<textarea>` mentah!
2. **PROJECT-SPECIFIC TAILWIND STYLES:** Jika terpaksa membuat tombol kustom atau elemen form yang belum ada komponen reusablenya, WAJIB menggunakan utility class yang sudah didefinisikan di `app.css` proyek ini. Contoh: 
   - Untuk tombol gunakan class `btn`, `btn-main`, `btn-outline-main`, `btn-danger`.
   - Untuk input raw (hanya jika sangat terpaksa seperti `<input type="file">`) gunakan class `form`.
   - Untuk form grouping gunakan class `form-group`.
3. **NO HARDCODED PAGE LAYOUTS:** Selalu gunakan `<MainPage>` (`#header`, default slot, `#footer`).
4. **PRECISE PROPS:** Komponen form menggunakan `v-model`, `label`, `placeholder`, dan `feedback` (pesan error validasi). Dilarang mengikat `is-invalid` secara manual.
5. **NO TAILWIND CLUTTER:** Ekstrak kelompok class berulang (5+ class) ke `@utility` di `resources/css/app.css`.
6. **MANDATORY POPUPPAGE FOR SUB-PAGES & FORMS:** Seluruh alur kerja *Create*, *Edit*, *Detail*, dan *Sub-page* WAJIB menggunakan `<PopUpPage>` (side-panel drawer) atau `usePopUpStore()`. DILARANG menggunakan *full page redirect* (`router.get()`) untuk formulir sub-halaman.
7. **FORM SPACING LIMIT (MAX SCALE 2):** Jarak antar-input formulir (vertikal maupun horizontal) DILARANG melebihi scale 2 Tailwind (`space-y-2`, `space-x-2`, `gap-2`, `gap-y-2`, `gap-x-2`).
8. **STANDARISASI ON-DEMAND DATA LOADING (NO OVER-FETCHING DI INDEX):** Data detail entitas lengkap (isi PopUpPage / drawer view & edit) dan data sekunder (opsi dropdown formulir) WAJIB diambil secara *on-demand / async* via API internal (`axios.get`) saat drawer/modal dibuka. DILARANG KERAS memuat relasi berat (children, item array, log, permissions) atau daftar master lookup di props `index()` Inertia. Selalu gunakan state loading (`isLoading`, skeleton loader, atau spinner) saat memuat data on-demand.
9. **MANDATORY ENUM FOR CONDITIONS & FORM OPTIONS (NO MAGIC STRINGS):** DILARANG KERAS meng-hardcode string literal status/tipe pada evaluasi kondisi (`v-if="status === 'draft'"`, `case 'draft':`) maupun membuat array opsi dropdown manual jika ada Enum backend terkait. WAJIB gunakan `$enums.<EnumName>.<Case>` di template atau composable `useEnum()` (`enums.<EnumName>.<Case>`, `getOptions('EnumName')`). Rujuk [.agents/rules/enums.md](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/.agents/rules/enums.md).
10. **MANDATORY BROWSERMCP UI VERIFICATION:** Setiap pembuatan atau perubahan komponen Vue/halaman Inertia WAJIB diverifikasi secara integrasi visual dan fungsional menggunakan `browsermcp` (navigasi URL, screenshot, snapshot DOM, dan inspeksi console logs via skill `sollu-integration-testing`). DILARANG menyatakan tugas frontend selesai tanpa verifikasi `browsermcp`.

## 2. Component Structure (`<script setup>`)

- **Ordering:** `<template>` terlebih dahulu, kemudian `<script setup>`.
- **Import Order:** 1. Vue core (`ref`, `computed`) → 2. Inertia (`router`, `useForm`) → 3. Third-party (`lodash`, `FontAwesomeIcon`) → 4. Global components (`@/Components/`) → 5. Stores/Composables → 6. Local components (`./Components/`).
- **Script Setup Order:** `defineOptions` → `defineProps`/`defineEmits` → Stores/Composables → Reactive state (`ref`, `reactive`) → `computed` → Methods → Watchers → Lifecycle hooks.

## 3. PopUpPage vs Modal (Distingsi Ketat)

- **`<PopUpPage>` / `usePopUpStore()` (Side Drawer Kanan):** WAJIB untuk formulir input, tampilan detail, sub-halaman, dan alur langkah berikutnya.
  - **Teleport Footer Pattern:** Komponen di dalam `PopUpPage` dapat menggunakan `<Teleport v-if="isMounted" to="#popUpFooter">` untuk mengirim tombol aksi langsung ke footer sticky `PopUpPage`.
- **`<Modal>` / `useModalStore()` (Center Dialog):** STRICTLY khusus untuk konfirmasi singkat (Hapus, Archive, Alert Peringatan).

## 4. UI Components & Formatting Standards

- **Quantity Display (`HasQuantityFormatter`):** Selalu tampilkan kuantitas dari properti trait backend (`item.qty_formatted`, `item.qty_received_formatted`). Dilarang memformat angka kuantitas secara manual di frontend.
- **Partial Loading & Skeleton:** Selalu sertakan skeleton loader / spinner / teks `"Memuat..."` (`animate-pulse bg-gray-200 rounded`) saat menunggu fetch data async. Jangan biarkan UI kosong tanpa indikator loading.

## 5. Table Filter Pattern

- **Layout:** `flex items-center gap-2`, `<FilterSearch>`, tombol Filter (`faSliders`) untuk membuka `<FilterModal>`, dan badge filter aktif via `<FilterBadge>`.
- **Workflow & Debouncing:** Inisialisasi `filterForm` dari `props.filters`, watcher 500ms debounce pada `filterForm.search` yang memanggil `updateQuery()`.
- **`updateQuery`:** Merge `route().params` dengan filter aktif, konversi string kosong `''` menjadi `undefined`, reset `page: 1`, lalu panggil `router.get(location.pathname, query, { preserveState: true, preserveScroll: true })`.

## 6. SelectionGroupField (`@/Components/Form/SelectionGroupField.vue`)

Gunakan `SelectionGroupField` untuk grup tombol opsi pilihan (mendukung seleksi tunggal dan ganda):

```vue
<!-- Single Select (Radio Button Style) -->
<SelectionGroupField
    v-model="form.gender"
    label="Jenis Kelamin"
    :options="[{ value: 'male', label: 'Laki-laki' }, { value: 'female', label: 'Perempuan' }]"
/>

<!-- Multi Select (Checkbox Button Style with Select All) -->
<SelectionGroupField
    v-model="form.outlets"
    label="Pilih Outlet"
    :options="outlets"
    multiple
    show-select-all
/>
```

## 7. Condition Validation & Enum Standards ($enums & useEnum)

Mengacu pada [.agents/rules/enums.md](file:///Users/whykrr/Documents/Projects/Laravel/sollu-app/.agents/rules/enums.md), PHP Backed Enums adalah Single Source of Truth. DILARANG menggunakan string literal hardcoded untuk status atau tipe.

### 7.1. Di Template Vue (Global Property `$enums`)
- **Validasi Kondisi Tombol/Elemen:**
  ```html
  <button v-if="adjustment.status === $enums.AdjustmentStatus.Draft">Edit</button>
  ```
- **Badge Dinamis & Label dari Backend Metadata:**
  ```html
  <span :class="$enums.StockOpnameStatus._meta[item.status]?.color || 'badge-gray'">
      {{ $enums.StockOpnameStatus._meta[item.status]?.label || item.status }}
  </span>
  ```

### 7.2. Di `<script setup>` (Composable `@/Composable/useEnum`)
- **Pengecekan Logic & Opsi Dropdown:**
  ```javascript
  import { useEnum } from '@/Composable/useEnum'

  const { enums, getOptions, getLabel, getColor } = useEnum()

  if (item.status === enums.AdjustmentStatus.Draft) {
      // Logic khusus draft
  }

  // Opsi dropdown otomatis untuk DropdownField / SelectionGroupField / FilterModal:
  const statusOptions = getOptions('AdjustmentStatus')
  ```

## 8. Frontend Dead Code Removal Standards

Setiap kali melakukan modifikasi pada komponen Vue (`.vue`), file JavaScript (`.js`), atau style (`.css`):

1. **Clean Unused Imports:** Hapus semua `import` komponen, ikon, composable, atau helper yang tidak dipanggil dalam `<script setup>` atau `<template>`. Jalankan `npm run fix:eslint` untuk merapikan impor.
2. **Remove Unused Reactive State & Props/Emits:** Hapus variabel `ref`, `reactive`, `computed`, `defineProps`, atau `defineEmits` yang tidak lagi digunakan dalam render template atau logika method.
3. **No Commented-Out HTML/Vue Code:** Hapus komentar kode HTML/Vue (`<!-- ... -->`, `// ...`) yang ditinggalkan saat merevisi UI layout atau komponen form.
4. **Obsolete Utility CSS Cleanups:** Hapus aturan `@utility` di `resources/css/app.css` jika kelas CSS tersebut sudah tidak dirujuk oleh halaman/komponen manapun. Pastikan `npm run build` berhasil tanpa warning/error.

