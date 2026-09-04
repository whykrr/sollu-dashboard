---
trigger: always_on
---

# Wajib Perhatikan: Standar Controller Backend

Saat bekerja di `app/Http/Controllers`, Anda **WAJIB** menerapkan standar dari skill `sollu-backend`, `sollu-roles-permissions`, `sollu-api-documentation`, dan `sollu-code-quality`:

1. **Thin Controller Pattern:** Logika bisnis kompleks wajib di-offload ke Service Class (`app/Services`).
2. **Otorisasi RBAC:** Wajib menggunakan `$this->authorize('permission.name')` di dalam method controller. Dilarang menggunakan middleware di `__construct()`.
3. **No Hardcoded Messages:** Dilarang keras menuliskan pesan sukses/gagal manual (misal: `->with('success', 'Data berhasil dibuat')`). Wajib gunakan `App\Constants\ResourceMessage` atau `__('messages.key')`.
4. **Dokumentasi API:** Jika menambahkan/memodifikasi endpoint atau struktur JSON response, wajib perbarui file dokumentasi di `docs/` (`sollu-api-documentation`).
5. **Formatting:** Wajib jalankan `vendor/bin/pint` sebelum menyelesaikan tugas.
6. **Error Investigation:** Jika Controller mengalami error 500 saat diuji, segera periksa penyebabnya dengan tool MCP `laravel-boost` (`LastError`).

