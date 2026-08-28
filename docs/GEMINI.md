---
trigger: always_on
---

# Wajib Perhatikan: Standar Dokumentasi API

Saat bekerja di `docs/`, Anda **WAJIB** menerapkan standar dari skill `sollu-api-documentation`:

1. **Perbarui Postman Collection & OpenAPI:** Pastikan perubahan endpoint, request body, query parameters, validation rules, dan JSON response structures direfleksikan di `docs/postman_collection.json` dan `docs/openapi.yaml`.
2. **Contoh Response:** Selalu perbarui contoh balasan JSON (*example response*) di Postman/Swagger sesuai tipe data dan casting numerik murni (`(float)` / `(double)`).
