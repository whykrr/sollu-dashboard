---
trigger: always_on
---

# Wajib Perhatikan: Standar Unit Test

Karena Anda sedang bekerja di direktori `tests/Unit`, Anda **WAJIB** menerapkan standar dari `sollu-unit-testing`.

1. **ISOLASI IN-MEMORY:** Dilarang keras menggunakan database fisik. Wajib menggunakan `RefreshDatabase` dengan `sqlite:memory` agar test cepat dan bersih.
2. Pastikan file test Service diletakkan pada folder yang mencerminkan namespace asli (misal: `tests/Unit/Services/...`).
3. Selalu periksa coverage code untuk memastikan 100% logika tercover. Mocks (seperti Mockery) hanya untuk service eksternal/class pendukung.
