# Changelog

Semua perubahan penting pada plugin **WeddingBlocks** akan didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
dan plugin ini menganut [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.4.0] - 2026-08-17

## Added (Ditambahkan)

- **Kompatibilitas WordPress 7.1** — Diuji dengan WordPress 7.1 (iframed editor, pembaruan `@wordpress/components`, dan persistent toolbar). Tidak diperlukan perubahan kode; `Tested up to` pada readme.txt diperbarui ke 7.1.
- **Pilih font untuk block `couple-title` (WB Nama Cover)** — kontrol **"Jenis Font Nama Cover"** di panel Tipografi editor: Playfair Display, Great Vibes, Montserrat, Georgia, System, Sans-serif, Monospace (semua tanpa webfont tambahan — memakai font yang sudah dibundel/kemampuan sistem), plus otomatis menyertakan font yang didaftarkan tema (`theme.json`) dan font dari **Font Library WordPress** (nilai CSS mentahnya disimpan dan dirender WordPress di frontend). Preview editor berubah langsung dan output `<h1>` memakai `font-family ... !important` agar konsisten dengan cover theme.css. Menggantikan fitur eksperimental di plugin Pro yang telah dipindah ke sini (nilai lama `wbproFontFamily` tetap dikenali).

## [1.3.0] - 2026-08-XX

## Added (Ditambahkan)

- **Continuous / Attention Animations** — Lapisan animasi kedua yang independen untuk efek berulang: Sway, Float, Pulse, Wobble, dan Shake. Block bisa memakai entrance animation dan efek berkelanjutan sekaligus tanpa bertabrakan (efek berkelanjutan menunggu entrance animation selesai dulu).
- Kontrol **Kecepatan** dan **Intensitas** untuk semua animasi berkelanjutan, plus **Titik Goyang** (pivot point) untuk Sway, Wobble, dan Pulse.
- Animasi berkelanjutan berhenti otomatis saat block keluar layar dan dimatikan penuh di bawah `prefers-reduced-motion` (konsisten dengan perilaku accessibility entrance animation).
- **Decorative Layer & Decorative Wrapper** — Dua block baru untuk membungkus area konten dengan elemen dekoratif melayang (kupu-kupu, burung, salju) tanpa memengaruhi layout di bawahnya.
- **Extensibility** — Titik hook action/filter internal pada registrasi block, penyimpanan RSVP, dan tabel RSVP admin sebagai fondasi untuk add-on.
- **RSVP Form** — Field tersembunyi opsional `guest-token` yang diteruskan pada submit RSVP bila ada di URL. Fondasi untuk add-on yang akan menautkan RSVP ke daftar tamu yang diimpor.
- **Design Tokens & Theme-Independent Styling** — Undangan kini punya token warna dan tipografi bawaan sendiri (h1-h4, paragraph) yang tidak bergantung pada tema aktif, tetap bisa diedit per-undangan (panel "Tema Warna") dan per-block. Warna brand dimigrasi ke CSS custom properties agar perubahan tema mengalir konsisten ke semua block.
- **Self-Hosted Local Fonts** — Montserrat, Playfair Display, dan Great Vibes dibundel lokal (woff2, subset latin) agar tipografi plugin konsisten tanpa bergantung CDN eksternal (paling benar menurut praktik WordPress.org).

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

## [1.2.0] - 2026-07-17

### Added (Ditambahkan)
- **Animation System**: Sistem animasi terpusat berbasis CSS + IntersectionObserver tanpa dependency eksternal.
  - 5 jenis entrance animation: `fadeUp`, `fadeIn`, `slideLeft`, `slideRight`, `zoomIn`
  - Setiap block mendukung attribute `animationStyle`, `animationDuration`, `animationDelay`
  - Helper terpusat `weddingblocks_get_animation_attrs()` di `includes/helpers.php`
- **Continuous animations**: Cover button breathe, cover content entrance, countdown tick per detik.
- **Interaction animations**: Avatar hover zoom, foto mempelai hover zoom, button ripple effect.
- **State animations**: RSVP alert entrance, guestbook new item entrance.
- **Accessibility**: Semua animasi dimatikan otomatis via `prefers-reduced-motion: reduce`.
- **Fallback**: Browser tanpa IntersectionObserver tetap menampilkan konten normal.

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
