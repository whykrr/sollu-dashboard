---
name: sollu-backend
description: >-
    Backend development standards for Sollu App (Laravel 11.9+, PHP 8.3).
    MUST trigger whenever creating or editing Laravel controllers, Eloquent models (casts method, UUIDs),
    domain service classes (Single vs Split services), BaseInertiaFormRequest, DB migrations, query performance (N+1 limit 5s),
    API JSON responses (JsonResource), or controller response messages (ResourceMessage/FlashDataVariable constants).
    MUST use the sollu-db query tool to inspect the current/live database schema before or during any backend change that depends on database structure. Never assume the database schema matches migrations, documentation, or memory.
---

# Sollu Backend Rules (Laravel 11.9+)

## 🚨 Related Skills (Perfect Hook Matrix)
- **`sollu-unit-testing`**: MANDATORY 100% Mocking Unit Tests whenever creating or modifying Service classes.
- **`sollu-api-documentation`**: MANDATORY API doc update whenever request/response structures change.
- **`sollu-roles-permissions`**: MANDATORY RBAC checks via `$this->authorize` and `BaseInertiaFormRequest`.
- **`sollu-excel`**: Asynchronous Excel export/import background job standards.
- **`sollu-pdf`**: Generic header Blade PDF generation standards.
- **`sollu-code-quality`**: Pre-completion Pint linter (`vendor/bin/pint`) & DoD checklist.

## 0. Mandatory Database Verification & Diagnostics (Live Database Check & Laravel Boost)

- **MANDATORY BEFORE & DURING BACKEND CHANGES:** Setiap kali membuat atau memodifikasi file backend (Model, Controller, Form Request, Service, DB Migration, JsonResource), **WAJIB** melakukan verifikasi kondisi skema database asli terlebih dahulu.
    - **Primary Low-Level Catalog:** Gunakan MCP tool `sollu-db` (query SQL `information_schema` atau `pg_attribute`) untuk presisi level PostgreSQL (tipe data native, nullability, primary & foreign key constraints).
    - **Laravel-Level Inspection:** Alternatif atau pelengkap, gunakan MCP tool `laravel-boost` (`DatabaseSchema` dan `DatabaseQuery`) untuk membaca skema langsung dari sudut pandang Laravel DB connection.
- **Verifikasi Kolom & Data Type:** Pastikan nama kolom, tipe data, nulabilitas (`nullable`), default value, dan Foreign Key pada Model/FormRequest/Service **persis sama** dengan skema nyata di database.
- **Verifikasi Relasi (FK):** Cek keberadaan Foreign Key constraint di database sebelum menuliskan method relasi Eloquent (`belongsTo`, `hasMany`, dll) atau validasi `exists:table,id`.


## 1. Architecture & Controllers

- **Flow:** Controller → Action/Service → Repository (opsional) → Model.
- **Controller Pattern:** Hybrid approach:
    - _Resource-style (inline):_ CRUD sederhana dapat langsung ditulis di controller.
    - _Service-injected:_ Logika bisnis kompleks wajib di-offload ke Service Class via Constructor Injection.
- **Authorization:** Gunakan `$this->authorize('permission.name')` atau `Gate::authorize()`. Dilarang menggunakan middleware di `__construct()`.

## 2. Model Standards (Laravel 11)

- **Member Ordering:**
    1. `use` Traits (satu per baris, misal: `use HasFactory, HasUuids, SoftDeletes;`)
    2. Properti: `$fillable`, `$hidden`, `$sortable`, `$appends`
    3. Method `casts(): array` (Style Laravel 11 dengan panah `=>` rapi)
    4. Method Notifikasi Custom
    5. Relationships (Urutan: `BelongsTo` → `HasMany` → `BelongsToMany` → `HasOne`; return type explicit `: BelongsTo`)
    6. `scopeFilters()` & Scopes lainnya
    7. Custom Helpers / Methods
- **PHPDoc:** Selalu tambahkan `@property-read Collection|Outlet[] $outlets` untuk membantu Autocomplete IDE / Larstan.

## 3. Form Requests (`BaseInertiaFormRequest`)

- **Base Class:** Semua Form Request wajib menginduk ke `App\Http\Requests\BaseInertiaFormRequest`.
- **Naming:** `Get{Entity}Request`, `Store{Entity}Request`, `Update{Entity}Request`.
- **Authorization:** Kembalikan cek permission pada method `authorize()`.
- **Validation Rules:** Format rules dalam bentuk array dengan panah `=>` sejajar:
    ```php
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku'  => ['nullable', 'string', 'max:100'],
        ];
    }
    ```

## 4. Service Layer Standards

- **Single-File Service (≤500 baris, kompleksitas rendah):** Gabungan domain service (contoh: `app/Services/OutletService.php`) memuat method `create()`, `update()`, `delete()`.
- **Split-File Service (>500 baris atau kompleks):** Single-action class per file (contoh: `app/Services/Outlet/CreateOutletService.php`) dengan method utama `execute(array $data, User $user)`.
- **Database Transactions:** Bungkus setiap mutasi multi-tabel dalam `DB::transaction(function () { ... });`.
- **Audit Log:** Catat perubahaan data penting menggunakan `AuditLogService`.

## 5. Query Optimization & Performance Limits (Max 5s)

- **Waktu Eksekusi Query/Response:** Dilarang melebihi **5 detik**.
- **N+1 Query Prevention:** Selalu gunakan Eager Loading (`with()`) untuk query standar Eloquent. Dilarang memicu lazy loading di dalam perulangan (`foreach`) atau template.
- **Selective Column Loading (`select()`):** Hindari pemanggilan `SELECT *` secara membabi-buta pada query berat atau tabel dengan kolom besar (`TEXT`, `JSON`). Pilih hanya kolom yang dibutuhkan (`select(['id', 'name', 'status', ...])`), terutama saat me-load relasi via eager loading (`with(['relation:id,parent_id,name'])`).
- **Existence Checks (`exists()` / `doesntExist()`):** Gunakan `exists()` atau `doesntExist()` saat mengecek keberadaan data. Dilarang keras menggunakan `count() > 0` atau `first() !== null` hanya untuk pengecekan boolean eksistensi.
- **Batch Processing & Mutations:** Dilarang melakukan perulangan mutasi model (`foreach (...) { Model::create(...) }` atau `->save()`). Gunakan batch `insert()` atau `upsert()` untuk manipulasi data massal.
- **Index & Filtering Awareness:** Sebelum menambahkan klausa `where`, `orderBy`, atau `join` baru, periksa ketersediaan indeks pada kolom terkait menggunakan MCP tool `sollu-db`. Kolom pencarian, filter status, tenant ID, atau relasi yang sering digunakan wajib memiliki indeks di database.
- **DataTables & Pagination:**
    - Jangan load relasi berat pada `index()`; gunakan `withCount()` untuk jumlah data relasi.
    - Jika memerlukan _sorting_ atau _filtering_ pada kolom tabel relasi, gunakan `join()` atau `leftJoin()` di tingkat database untuk efisiensi memori.
    - **No Unbounded Queries:** Dilarang memanggil `get()` atau `all()` tanpa batasan (`limit` atau `paginate`) pada tabel yang berpotensi terus bertambah (transaksi, mutasi stok, audit log, dsb).
- **Offload Complex Detail & Secondary Data:** Sediakan endpoint API JSON (`JsonResource`) tersendiri untuk data detail kompleks (diakses via PopUpPage) atau data sekunder (opsi dropdown dinamis), dilarang di-load di Inertia `index()`.
- **Large Datasets:** Gunakan `chunk()`, `lazy()`, atau `cursor()` untuk pengolahan data dalam jumlah besar.

## 6. API JSON Response Standards

- **Key Format:** `snake_case`.
- **Status Codes:** Mengacu pada standar HTTP (200, 201, 400, 404, 422, 500). Tidak menggunakan wrapper custom `"success": true`.
- **Data & Meta:** Gunakan `JsonResource`. Bungkus koleksi data dalam `"data"` dan data paginasi dalam `"meta"`.
- **Numeric & Decimal Casting (`(float)` / `(double)`):** Seluruh nilai desimal dan numerik (harga, stok, persentase, bobot) pada `JsonResource` atau respon API WAJIB di-cast ke tipe angka murni `(float)` atau `(double)`. Dilarang mengirimkan string berformat desimal (contoh salah: `"10.50"`), wajib dikirim sebagai angka murni (contoh benar: `10.5`).
    ```php
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'base_price'    => (float) $this->amount,
            'current_stock' => (float) $this->current_stock,
        ];
    }
    ```
- **Validation Error (422):** Format default FormRequest (`"message"`, `"errors"`).

## 7. Controller Response Messages & Constants (MANDATORY)

- **DILARANG MENGGUNAKAN HARDCODED STRING:** Dilarang keras menuliskan string pesan respon manual langsung di Controller (contoh salah: `->with('success', 'Data berhasil dibuat')`).
- **WAJIB MENGGUNAKAN CONSTANT / TRANSLATION:** Seluruh _flash message_ respon Controller wajib merujuk pada Class Constant di `app/Constants/` atau Translation helper `__('key')` / `trans('key')`.

### Referensi Constant Resmi Proyek:

- `App\Constants\ResourceMessage::CREATE_SUCCESS` (`'Data berhasil dibuat!'`)
- `App\Constants\ResourceMessage::UPDATE_SUCCESS` (`'Data berhasil diperbarui!'`)
- `App\Constants\ResourceMessage::DELETE_SUCCESS` (`'Data dipindah ke sampah!'`)
- `App\Constants\ResourceMessage::RESTORE_SUCCESS` (`'Data berhasil di kembalikan!'`)
- `App\Constants\ResourceMessage::PURGE_SUCCESS` (`'Data berhasil di hapus!'`)
- `App\Constants\AuthorizationMessage::CANT_ACCESS_PAGE`
- `App\Constants\AuthorizationMessage::CANT_ACCESS_DATA`
- `App\Constants\FlashDataVariable::SUCCESS->value` (`'success'`)
- `App\Constants\FlashDataVariable::WARNING->value` (`'warning'`)
- `App\Constants\FlashDataVariable::FAILED->value` (`'failed'`)

### Contoh Penggunaan di Controller:

```php
use App\Constants\FlashDataVariable;
use App\Constants\ResourceMessage;

public function store(StoreOutletRequest $request)
{
    $this->outletService->create($request->validated());

    return redirect()->back()->with(
        FlashDataVariable::SUCCESS->value,
        ResourceMessage::CREATE_SUCCESS
    );
}
```

## 8. Backend Dead Code Removal Standards

Setiap kali melakukan modifikasi pada komponen backend (Controller, Model, Service, FormRequest, JsonResource, Migration, Routes):

1. **Clean Unused Statements (`use`):** Hapus semua baris `use App\Models\...` atau `use App\Services\...` yang tidak dipanggil di dalam file. Jalankan `vendor/bin/pint` sebelum menyelesaikan tugas.
2. **Remove Dead Methods & Helper Functions:** Jika suatu method di Service/Controller tidak lagi digunakan (karena refactoring/perubahan alur), hapus method tersebut beserta unit test-nya jika ada. Dilarang menyisakan method yatim tanpa caller.
3. **No Commented-Out PHP Code:** Jangan menyisakan blok logika PHP lama dalam bentuk komentar (`//`, `/* */`). Seluruh kode lama harus dihapus murni.
4. **Obsolete Routes & Requests:** Jika sebuah endpoint atau FormRequest tidak lagi dipakai oleh frontend/API client, hapus `FormRequest` class tersebut dan deklarasi rutenya di `routes/web.php` atau `routes/api.php`.

## 9. Laravel Boost MCP Tooling Guidelines

Gunakan MCP tools dari server `laravel-boost` untuk mempercepat siklus investigasi, debugging, dan dokumentasi:

- **`LastError` & `ReadLogEntries` (Direct Error Log):** Saat backend melempar error 500 atau exception saat testing / endpoint call, **WAJIB** gunakan tool `mcp_laravel-boost_LastError` atau `mcp_laravel-boost_ReadLogEntries` untuk segera menginspeksi stack trace tanpa perlu memanggil shell `tail storage/logs/laravel.log`.
- **`SearchDocs` (Official Laravel Documentation Search):** Jika membutuhkan referensi resmi Laravel 11 (misalnya sintaks API baru, casts, queued events, cache locks, benchmark helper), gunakan `mcp_laravel-boost_SearchDocs` untuk pencarian vektor langsung ke dokumentasi resmi Laravel.
- **`Tinker` (Safe Dynamic Execution):** Gunakan `mcp_laravel-boost_Tinker` untuk memverifikasi kalkulasi matematis, helper, mutator model, atau query builder sebelum di-commit ke dalam codebase.
- **`ApplicationInfo`:** Gunakan untuk mengecek status konfigurasi, service provider, dan environment Laravel.

## 10. Filesystem Operations: Native Tools vs Filesystem MCP

- **Primary / Default Tools (Antigravity Native):** Untuk membaca, membuat, dan mengedit file proyek (`view_file`, `replace_file_content`, `write_to_file`, `find_by_name`, `grep_search`), **WAJIB** menggunakan native tools bawaan Antigravity. Native tools terintegrasi langsung dengan IDE diff viewer, line tracking, sandboxing, dan token management.
- **Secondary MCP Tools (`filesystem`):** Gunakan MCP server `filesystem` (`read_multiple_files`, `directory_tree`, `move_file`, `get_file_info`) khusus untuk skenario berikut:
    - Membaca beberapa file secara serentak (`read_multiple_files`).
    - Membuat visualisasi struktur hierarki direktori lengkap (`directory_tree`).
    - Memindahkan/mengganti nama file (`move_file`) secara aman tanpa menjalankan command shell mentah.

