---
trigger: always_on
---

# Wajib Perhatikan: Standar Komponen Tabel & Filter Data

Saat menggunakan atau mengedit komponen di `resources/js/Components/Tables`, Anda **WAJIB** mematuhi aturan berikut:

1. **Komponen Tabel:** Gunakan `<Table>` untuk merender tabel data standar, dan `<Pagination>` untuk navigasi halaman.
2. **Pencarian Live & Debounce Filter:**
   - Gunakan watcher 500ms debounce pada `filterForm.search` yang memanggil `updateQuery()`.
   - `updateQuery()` melakukan merge `route().params` dengan filter aktif, mengonversi string kosong `''` menjadi `undefined`, me-reset `page: 1`, dan mengeksekusi `router.get(location.pathname, query, { preserveState: true, preserveScroll: true })`.
