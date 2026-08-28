---
trigger: always_on
---

# Wajib Perhatikan: Standar Dialog Modal Konfirmasi

Saat menggunakan atau mengedit modal di `resources/js/Components/Modals` atau `resources/js/Components/Notifications`, Anda **WAJIB** mematuhi aturan berikut:

1. **Center Modal Dialog (`<Modal>` / `useModalStore()`):**
   - HANYA digunakan untuk konfirmasi tindakan singkat (misal: Konfirmasi Hapus Data, Archive, Alert Peringatan).
   - DILARANG menggunakan Modal Center untuk formulir input yang kompleks atau memiliki lebih dari 3 bidang input. Gunakan `<PopUpPage>` (side drawer) untuk formulir.
2. **Toast Notification (`useToastStore()`):**
   - Gunakan `useToastStore()` untuk menampilkan notifikasi mengambang (sukses, error, info) setelah aksi pengguna selesai.
