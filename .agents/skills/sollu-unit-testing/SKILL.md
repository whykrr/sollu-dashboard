---
name: sollu-unit-testing
description: >-
    MANDATORY STANDARDS for Unit Testing Service Layer in Sollu App.
    MUST trigger whenever creating, editing, or enhancing a Service class, or when writing unit tests.
    Strictly mandates 100% Mocking (no database interaction) and 100% Code Coverage for Service unit tests.
---

# Sollu App Service Layer Unit Testing Standards

## 1. Wajib Unit Test
Setiap perubahan, perbaikan bug, penambahan fitur (enhancement), atau pembuatan **Service class** baru, **WAJIB** disertai dengan pembuatan atau pembaruan **Unit Test** yang merepresentasikan logika tersebut.

## 2. Terisolasi dan Bebas Data Kotor (In-Memory Database)
Test Service Layer **DILARANG KERAS** menyentuh database fisik (MySQL/PostgreSQL) agar tidak menghasilkan data kotor.
- **WAJIB** menggunakan `sqlite:memory` yang diatur di `phpunit.xml`.
- **WAJIB** menggunakan trait `RefreshDatabase` pada setiap class test agar skema di-*reset* dalam memori, menjamin 100% isolasi per test.
- Dependensi eksternal (API pihak ketiga) tetap **WAJIB** di-*mock* menggunakan **Mockery**.
- Pendekatan ini mempertahankan *execution time* yang optimal (sangat cepat karena berjalan di RAM) tanpa *side-effect* ke database asli.

## 3. 100% Code Coverage & Scenario Testing
- Uji seluruh skenario (*Happy Path*, *Edge Cases*, dan *Error/Exception/Failure Paths*).
- Pastikan semua percabangan (`if/else`, `switch`, `try/catch`) tereksekusi di dalam suite test untuk memastikan coverage mencapai 100%.
- Gunakan `$this->expectException(...)` untuk memvalidasi exception yang dilempar oleh Service.

## 4. Struktur Direktori dan Penamaan
- Lokasi test harus menduplikasi struktur namespace asli.
  Contoh: `App\Services\App\Inventory\StockAdjustmentService` -> `tests/Unit/Services/App/Inventory/StockAdjustmentServiceTest.php`.
- Gunakan nama test method yang deskriptif dan menunjukkan *behaviour* yang diuji, misalnya:
  `public function test_it_can_create_stock_adjustment_successfully()` atau
  `public function test_it_throws_exception_when_stock_is_insufficient()`.

## 5. Mocking Eloquent dan Query Builder
Jika Service Anda melakukan chain query builder (misal: `Model::where()->with()->get()`), pastikan chain di-*mock* dengan sempurna, atau refactor logika query ke Repository. Jika tetap di Service, gunakan `Mockery::mock('alias:App\Models\YourModel')` untuk statis.

Contoh dasar Mocking Dependency:
```php
$this->dependencyMock = Mockery::mock(DependencyClass::class);
$this->dependencyMock->shouldReceive('methodName')
    ->once()
    ->with('args')
    ->andReturn($expectedValue);
```
