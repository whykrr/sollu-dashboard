---
trigger: always_on
---

# Wajib Perhatikan: Standar Frontend Vue 3 / Inertia / Tailwind v4

Saat bekerja di `resources/js`, Anda **WAJIB** menerapkan standar dari skill `sollu-frontend`, `sollu-integration-testing`, `sollu-roles-permissions`, dan `sollu-code-quality`:

1. **Komponen Form Standar:** Dilarang menggunakan raw HTML form inputs. Wajib gunakan `@/Components/Form/` (`TextField`, `DropdownField`, `SelectionGroupField`, `Switch`, dll).
2. **Layout & Drawer Mandatori:** Selalu gunakan `<MainPage>` untuk halaman utama, dan `<PopUpPage>` / `usePopUpStore()` untuk drawer formulir/sub-page (Create/Edit/Detail).
3. **Modal Konfirmasi:** Gunakan `<Modal>` / `useModalStore()` HANYA untuk dialog konfirmasi hapus/arsip singkat.
4. **Form Spacing Limit:** Spacing antar-input formulir DILARANG melebihi scale 2 (`space-y-2`, `gap-2`).
5. **Otorisasi Frontend:** Gunakan composable `@/Composable/useAuth` (`can('permission.name')`).
 <!-- 6. **Verifikasi Browser MCP:** Wajib verifikasi perubahan UI secara visual & fungsional dengan `browsermcp` (navigasi URL, DOM snapshot, console log check via `sollu-integration-testing`). -->
6. **Linter & Build:** Wajib jalankan `npm run fix:eslint` dan `npm run build` sebelum menyelesaikan tugas.
7. **Remove Dead Code** jika terdapat perubahan atau improvement pada file vue, pastikan kode yang tidak digunakan dihapus.
