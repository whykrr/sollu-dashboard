---
trigger: always_on
---

# Wajib Perhatikan: Standar Eloquent Model

Saat bekerja di `app/Models`, Anda **WAJIB** menerapkan standar dari skill `sollu-backend` dan `sollu-code-quality`:

1. **Mandatory Live Database Verification:** Sebelum/saat mengedit model, **WAJIB** gunakan tool MCP `sollu-db` untuk memeriksa skema nyata database (`information_schema`). Jangan pernah mengira-ngira nama kolom atau tipe data.
2. **Casts Method (Laravel 11):** Gunakan method `casts(): array` (bukan `$casts` array property).
3. **Member Ordering:** Urutan member: Trait -> Properties -> `casts()` -> Relations -> Scopes -> Helpers.
4. **UUID:** Gunakan trait `HasUuids` jika tabel menggunakan UUID primary key.
