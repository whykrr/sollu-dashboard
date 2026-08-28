---
trigger: always_on
---

# Wajib Perhatikan: Standar Domain Service & Unit Testing

Saat bekerja di `app/Services`, Anda **WAJIB** menerapkan standar dari skill `sollu-unit-testing`, `sollu-backend`, dan `sollu-code-quality`:

1. **Mandatory Service Unit Test:** Setiap kali membuat, mengubah, atau memperbarui logika Service Class, **WAJIB** membuat atau memperbarui Unit Test di `tests/Unit/Services/...`.
2. **100% Pure Mocking (No DB):** Test Service Layer **DILARANG KERAS** menyentuh database fisik. Wajib gunakan Mockery / `sqlite:memory` dan `RefreshDatabase`.
3. **100% Code Coverage:** Pastikan seluruh skenario (happy path, error path, exception) teruji 100%.
4. **Service Architecture:** Single-file Service (<= 500 baris) vs Split-file Single-Action Service (> 500 baris dengan method `execute()`).
5. **Database Transactions:** Bungkus mutasi multi-tabel dalam `DB::transaction(function () { ... });`.
6. **Formatting & Testing:** Wajib jalankan `vendor/bin/pint` dan `vendor/bin/phpunit`.
