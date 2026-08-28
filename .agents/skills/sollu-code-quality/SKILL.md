---
name: sollu-code-quality
description: >-
  Code quality enforcement, formatting standards using Laravel Pint (vendor/bin/pint) and ESLint (npm run fix:eslint),
  verification procedures, strict development prohibitions, testing order, and Definition of Done for Sollu App.
  MUST trigger before finalizing deliverables, running code reviews, formatting files, or completing tasks.
---

# Sollu Code Quality & Verification Standards

## 🚨 Related Skills (Perfect Hook Matrix)
- **`sollu-backend`**: Pint formatting (`vendor/bin/pint`), thin controllers, and response constants.
- **`sollu-frontend`**: ESLint formatting (`npm run fix:eslint`) and Vite build verification (`npm run build`).
- **`sollu-unit-testing`**: Service unit test coverage and isolation.
- **`sollu-integration-testing`**: E2E visual & functional browser verification with `browsermcp`.

Standar kualitas kode, pengujian, pembersihan otomatis (*linter*), serta kriteria *Definition of Done* untuk aplikasi Sollu App.

## 1. Automated Code Formatting & Linting

Sebelum menyelesaikan tugas atau mengirimkan perubahan kode:
- **Backend (PHP/Laravel):** Jalankan Laravel Pint untuk memastikan format sesuai standar PSR-12 & Laravel style:
  ```bash
  vendor/bin/pint
  ```
- **Frontend (Vue/JS):** Jalankan ESLint untuk merapikan file `.vue` dan `.js`:
  ```bash
  npm run fix:eslint
  ```
- **Frontend Build Test:** Pastikan kompilasi bundler Vite berhasil tanpa error:
  ```bash
  npm run build
  ```

## 2. Testing Sequence & Verification Workflow

- **Testing Order:** ALWAYS test backend endpoints/unit logic FIRST, then proceed to test frontend UI components.
- **Pre-Code Inspection:** Read target files, dependencies, traits, services, and migrations completely before editing. Do NOT guess signatures or database column names.
- **Runtime Verification:** NEVER declare a task complete without running build/test commands and verifying the actual execution results.

## 3. Strict Prohibitions (Forbidden Actions)

1. **NO Monolithic Refactoring:** Do NOT perform large-scale refactoring or delete existing features outside the scope of current requirements.
2. **NO Destructive Migration Alters:** NEVER delete old migrations or alter production schema/permissions without explicit instructions.
3. **NO Soft Delete / Audit Log Removal:** NEVER delete audit log records (`AuditLogService`) or bypass soft-delete traits (`SoftDeletes`).
4. **NO Raw SQL Injection Risks:** Never bypass Eloquent with raw SQL string concatenation.
5. **NO Direct Role Hardcoding:** NEVER write `$user->role == 'admin'`. Always use Spatie permission checks (`$user->can('permission.name')`).
6. **NO Unoptimized Queries / N+1:** NEVER execute unbounded `Model::all()` or `Model::get()` on growing tables, never trigger lazy loading inside loops (N+1), never use `count() > 0` for existence checks, and never execute mutation loops where batch `insert()` / `upsert()` should be used.
7. **NO Deadcode Leftovers (Pembersihan Dead Code Wajib):** DILARANG meninggalkan kode mati (*dead code*) dalam bentuk apa pun (commented-out code, unused imports, orphaned methods/variables, obsolete files/routes). Seluruh kode mati WAJIB DIHAPUS pada sesi perubahan codebase yang sama.

## 4. 🧹 Dead Code Elimination Protocol (Aturan Pembersihan Kode Mati)

Setiap kali membuat atau memodifikasi file di codebase, WAJIB menerapkan protokol pembersihan berikut:

1. **NO Commented-Out Code:** Hapus semua kode lama yang dikomentari (`//`, `/* */`, `<!-- -->`, `{{-- --}}`). Jangan biarkan kode mati tersisa sebagai komentar (riwayat versi sudah aman tercatat di Git).
2. **Unused Imports & Dependencies:** Hapus semua `use` statement di PHP dan `import` statement di JS/Vue yang tidak lagi dirujuk setelah perubahan.
3. **Orphaned Methods & Variables:** Hapus helper method private, variabel local/reactive (`ref`, `computed`), konstanta, atau enum case yang tidak lagi memiliki pemanggil (*caller*).
4. **Orphaned Files & Obsolete Routes:** Jika suatu refactoring menggantikan Controller, Service, Request, Vue Component, atau Blade View lama, pastikan file lama dan pendaftarannya di `routes/web.php` / `routes/api.php` dihapus setelah dipastikan tidak ada dependensi tersisa.
5. **Redundant Styles:** Hapus `@utility` atau style khusus di `resources/css/app.css` yang sudah tidak dipakai oleh komponen manapun.

## 5. Definition of Done (DoD) Checklist

A feature or bugfix is considered **DONE** only when:
- [ ] Backend logic & endpoints tested and returning accurate HTTP status codes.
- [ ] Controller response messages use `App\Constants\*` (`ResourceMessage`, `FlashDataVariable`) or `lang/` translation files without any hardcoded strings.
- [ ] Query & Eloquent teroptimasi (Eager loading diterapkan untuk mencegah N+1, seleksi kolom spesifik, pengecekan eksistensi via `exists()`, batch insert/upsert, dan bebas dari unbounded queries).
- [ ] Frontend UI verified visually and functionally via `browsermcp` (navigasi URL, screenshot, snapshot, console log check), and layout aligns with design standards.
- [ ] Semua dead code (commented-out code, unused imports, orphaned methods/variables, obsolete files/routes) telah diverifikasi dan dihapus.
- [ ] Code formatted with `vendor/bin/pint` and `npm run fix:eslint`.
- [ ] `npm run build` executes cleanly with zero syntax or bundling errors.
- [ ] All permissions registered in `PermissionEnum.php` & `RolePermissionSeeder.php` (if applicable).
- [ ] Open assumptions or unverified items are explicitly documented to the user.
