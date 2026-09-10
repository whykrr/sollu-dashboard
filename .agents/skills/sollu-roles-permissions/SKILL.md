---
name: sollu-roles-permissions
description: >-
  Role-Based Access Control (RBAC), Spatie Permission management, PermissionEnum, RoleEnum, RolePermissionSeeder,
  authorization checks ($this->authorize), BaseInertiaFormRequest authorization, and useAuth composables in Sollu App.
  MUST trigger whenever creating or modifying permissions, roles, access controls, or authorization checks.
---

# Sollu Roles & Permissions Standard (RBAC)

## 🚨 Related Skills (Perfect Hook Matrix)
- **`sollu-feature-plan`**: SaaS Feature Plan validation (`v-feature`, `usePlanFeature`, `$enums.FeatureEnum`). Dilarang mencampuradukkan RBAC permission dengan feature plan!
- **`sollu-backend`**: Authorization in FormRequests (`BaseInertiaFormRequest`) and Controllers (`$this->authorize`).
- **`sollu-frontend`**: Frontend UI permission checks using `@/Composable/useAuth` and `v-can`.
- **`sollu-code-quality`**: Strict prohibition against hardcoding roles or string comparisons.

Aturan pengelolaan Hak Akses (Role-Based Access Control / RBAC) menggunakan package `spatie/laravel-permission` pada Sollu App.


## 1. Multi-Tenant Role Isolation (Tenant-Scoped)

Berbeda dengan permission yang bersifat global (`PermissionEnum`), **Role** (Peran) pada Sollu App dipisahkan (isolated) per Bisnis (`business_id`). Hal ini dikonfigurasi menggunakan mode `teams => true` pada `spatie/laravel-permission` di mana `team_foreign_key` adalah `business_id`.

- Setiap bisnis akan dibuatkan **3 Default Roles** bawaan secara otomatis: `owner`, `manager`, `cashier`.
- Saat mengassign role atau mengecek izin, sistem otomatis mengevaluasi dalam lingkup `business_id` user yang sedang login berkat `setPermissionsTeamId($user->business_id)` di `AppServiceProvider.php` listener `Authenticated`.
- **DILARANG** melakukan seeding Role secara global di `RolePermissionSeeder.php`. Pembuatan role untuk sebuah bisnis dilakukan melalui `App\Services\App\Role\RoleProvisioningService`.

## 2. Permission Registration Workflow

Setiap kali fitur baru memerlukan otorisasi atau hak akses baru:
1. **Daftarkan Key Permission** di Enum `app/Enums/PermissionEnum.php` (Gunakan dot-notation, e.g. `SETTINGS_OUTLET_INDEX = 'settings.outlets.index'`).
2. **Assign ke Default Role** (owner/manager/cashier) di `RoleProvisioningService.php` (bukan di seeder!).
3. **Jalankan Artisan Seeder** hanya untuk memperbarui global permission:
   ```bash
   php artisan db:seed --class="Database\Seeders\Production\RolePermissionSeeder"
   ```
4. Jika ada perubahan struktur role default di production, instruksikan pengguna untuk menjalankan Normalisasi:
   ```bash
   php artisan sollu:normalize-rbac
   ```

## 2. Backend Authorization Rules

- **Controllers:** Wajib menggunakan `$this->authorize('permission.name')` atau `Illuminate\Support\Facades\Gate::authorize('permission.name')` di dalam method controller.
- **DILARANG** menggunakan `$this->middleware('permission:...')` di dalam constructor `__construct()` controller.
- **Form Requests:** Wajib mengembalikan boolean check permission pada method `authorize()` di class turunan `App\Http\Requests\BaseInertiaFormRequest`.
- **DILARANG HARDCODE ROLE:** Dilarang mengecek `$user->role === 'admin'` secara langsung. Selalu cek permission via `$user->can('permission.name')` atau `$user->hasPermissionTo(...)`.

## 3. Frontend Authorization (`useAuth`)

Di sisi Vue 3 (Composition API), otorisasi dilakukan menggunakan composable `@/Composable/useAuth`:

```js
import { useAuth } from '@/Composable/useAuth';

const { can, canAny, canAll, hasRole, isOwner } = useAuth();

// Contoh penggunaan di template atau script:
if (can('settings.outlets.create')) {
    // izinkan aksi
}
```

- **DILARANG** mengakses `usePage().props.auth` secara langsung jika helper `useAuth()` tersedia.
