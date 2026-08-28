---
trigger: always_on
---

# Wajib Perhatikan: Standar Komponen Formulir `@/Components/Form/`

Saat menggunakan atau mengedit komponen di `resources/js/Components/Form`, Anda **WAJIB** mematuhi aturan berikut:

1. **Komponen Resmi Proyek:**
   - Input Teks Tipe Umum: `<TextField>` & `<TextareaField>`
   - Input Angka & Keuangan: `<NumberField>`
   - Password & Pin: `<PasswordField>` & `<PinField>`
   - Dropdown & Select: `<DropdownField>`, `<AsyncSelectField>`, `<AsyncOutletDropdown>`
   - Pilihan Toggle / Radio / Checkbox: `<Switch>`, `<CheckboxField>`, `<RadioField>`
   - Group Option Selector: `<SelectionGroupField>` (Single select radio button style / Multi-select checkbox button style)
   - Rich Text: `<QuillEditor>`

2. **Pengikatan Props & Error Validation:**
   - Selalu bind data via `v-model="form.field_name"`.
   - Teruskan error validasi via `:feedback="form.errors.field_name"`. Dilarang mengikat class `is-invalid` secara manual.

3. **Form Field Spacing:**
   - Jarak antar-input formulir DILARANG melebihi scale 2 Tailwind (`space-y-2`, `gap-2`).
