---
trigger: always_on
---

# Wajib Perhatikan: Standar Komponen Layout UI `@/Components/UI/`

Saat menggunakan atau mengedit komponen di `resources/js/Components/UI`, Anda **WAJIB** mematuhi aturan berikut:

1. **`<MainPage>` Wrapper:**
   - Gunakan untuk halaman utama / index dashboard.
   - Sediakan slot `#header` untuk judul halaman & tombol aksi utama.
   - Sediakan slot `#footer` untuk pagination tabel.

2. **`<PopUpPage>` / `usePopUpStore()` Side Panel Drawer:**
   - Mandatory untuk formulir *Create*, *Edit*, *Detail*, dan *Sub-page*.
   - Dilarang membuat halaman baru penuh (*full page redirect*) untuk formulir sub-halaman.
   - Tombol simpan/batal di dalam PopUpPage dikirim ke footer sticky menggunakan `<Teleport v-if="isMounted" to="#popUpFooter">`.

3. **Data Loading (Skeleton Loader):**
   - Saat memuat data async di dalam PopUpPage, tampilkan skeleton loader / teks `"Memuat..."` (`animate-pulse bg-gray-200 rounded`). Jangan membiarkan UI kosong.
