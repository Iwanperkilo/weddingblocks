# Changelog

Semua perubahan penting pada plugin **WeddingBlocks** akan didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
dan plugin ini menganut [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] - 2025-01-XX

### 🎉 Rilis Awal

#### Added (Ditambahkan)
- 11+ custom Gutenberg blocks:
  - `cover` — Sampul pembuka undangan
  - `couple-name` — Nama pengantin pria & wanita
  - `couple-title` — Sapaan (Putra/Putri dari ...)
  - `couple-parents` — Info orang tua
  - `couple-photo` — Galeri foto pasangan
  - `couple-info` — Biodata singkat
  - `event-info` — Detail acara (tanggal, lokasi, waktu)
  - `countdown` — Hitung mundur menuju hari H
  - `music-player` — Pemutar musik otomatis
  - `guest-name` — Sapaan personal untuk tamu
  - `rsvp-form` — Form konfirmasi kehadiran
  - `guestbook` — Ucapan & doa dari tamu
- Custom Post Type `undangan` khusus pernikahan
- Halaman admin untuk mengelola data RSVP & guestbook
- Schema database otomatis untuk RSVP & guestbook (auto-install pada aktivasi)
- Template undangan:
  - `templates/classic-undangan.php` (untuk classic theme)
  - `templates/single-undangan.html` (untuk block theme / FSE)
- Asset frontend & editor:
  - `assets/css/blocks-frontend.css`
  - `assets/css/blocks-editor.css`
  - `assets/css/atomic-blocks.css`
  - `assets/css/editor-preview.css`
  - `assets/js/blocks-frontend.js`
  - `assets/js/blocks-editor.js`
- Dukungan penuh **Full Site Editing (FSE)**
- Text domain `weddingblocks` (siap i18n)
- Zero dependency eksternal

#### Security
- Pengecekan `ABSPATH` untuk mencegah akses langsung ke file PHP
- Validasi nonce pada form submission
- Sanitasi & escape data saat output

#### Documentation
- README.md lengkap dengan badge, instalasi, dan kontribusi
- readme.txt standar WordPress.org plugin directory
- CHANGELOG.md (file ini)

---

## [1.1.0] - 2026-07-16

### Added (Ditambahkan)
- Blok Event Info yang lengkap dengan berbagai variasi layout dan kustomisasi gaya editor.
- Opsi kustomisasi tambahan pada blok Music Player.
- Kolom jumlah tamu (*guest count*) pada form RSVP.
- Pengaturan alignment (perataan), ukuran font, dan toggle prefiks pada blok Guest Name.
- Berkas helper `includes/helpers.php` untuk memisahkan fungsi-fungsi umum.

### Changed (Diubah)
- Dukungan Editor Template & FSE: Blok sekarang dapat ditambahkan langsung ke dalam template editor WordPress, tidak terbatas hanya pada postingan Custom Post Type "Undangan".
- Migrasi tata letak blok couple dan event info ke CSS Grid dan container queries untuk responsivitas yang lebih baik.
- Penyederhanaan CSS frontend dan peningkatan transisi animasi.
- Fitur auto-contrast pada warna teks blok Cover dan opsi boxed cover kustom.
- Penguncian body pada cover kini menyimpan dan memulihkan posisi scroll (`window.scrollY`) dengan benar.

---

## [Unreleased]

### Planned
- Template tambahan (modern, elegant, rustic)
- Integrasi Google Maps
- Export data RSVP ke CSV
- Tema visual tambahan
- Multi-bahasa (EN/ID)
- Custom font picker di block
- Dukungan QR Code untuk undangan digital

---

[1.1.0]: #110---2026-07-16
[1.0.0]: #100---2025-01-xx
[Unreleased]: #unreleased
