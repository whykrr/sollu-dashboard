---
trigger: always_on
---

# Wajib Perhatikan: Standar Unit Test Service Layer

Karena Anda sedang bekerja di direktori `app/Services`, Anda **WAJIB** menerapkan standar unit test baru dari `sollu-unit-testing` jika Anda menambah atau mengubah logika Service.

1. **JANGAN GUNAKAN DATABASE** (Gunakan Mockery).
2. **PASTIKAN 100% COVERAGE** untuk logika yang diubah.
3. Perbarui `tests/Unit/Services/...` sesuai file yang Anda sentuh.

Selalu aktifkan skill `sollu-unit-testing` saat diminta bantuan terkait layanan ini!
