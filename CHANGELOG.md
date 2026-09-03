# Changelog

Semua perubahan penting pada plugin **WeddingBlocks** akan didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
dan plugin ini menganut [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.5.1] - 2026-09-03

### Fixed & Improved (Diperbaiki & Ditingkatkan)
- **Separator font inheritance** — Menghapus font-family hardcoded dari `.weddingblocks-separator` di CSS editor maupun frontend. Separator sekarang mewarisi font-family dari `.weddingblocks-cover-title` agar konsisten dengan pilihan "Jenis Font Nama Cover".
- **Cover wrapper layout** — Menyesuaikan minimum width dan box-sizing pada wrapper cover untuk meningkatkan stabilitas tampilan di berbagai perangkat.
- **CSS formatting & responsive layout** — Memformat ulang deklarasi CSS untuk keterbacaan yang lebih baik dan meningkatkan responsivitas layout untuk couple columns, avatars, dan komponen lainnya.

## [1.5.0] - 2026-08-28

### Added (Ditambahkan)
- **Dynamic Typography & Font Library Integration** — Refactor block `couple-name`, `couple-parents`, dan `couple-info` agar mendukung pemilihan font dinamis dari WordPress Font Library dan pengaturan tema.
- **Flexible Font System** — Mengganti pemetaan font hardcoded dengan sistem fleksibel yang memungkinkan block mewarisi tipografi tema secara default atau memakai font family kustom.
- **CSS Variables** — CSS diperbarui untuk memakai CSS variables pada font body, heading, dan aksen.
- **Font Resolution Logic** — Logika resolusi font family diimplementasikan pada block couple-info, couple-name, dan couple-parents.
- **Editor Integration** — Integrasi `wp.data` untuk mengambil daftar font family yang tersedia dari pengaturan editor.

### Fixed & Improved (Diperbaiki & Ditingkatkan)
- **Sanitasi** — Ditambahkan sanitasi untuk string font family kustom pada template render PHP.
- **Block Attributes** — Atribut block diperbarui memakai default `'default'`, bukan lagi hardcoded `'playfair'`.

## [1.4.1] - 2026-08-23

### Added (Ditambahkan)
- **Extensibility Hooks untuk RSVP Query** — Filter `weddingblocks_rsvp_search_fields` untuk memperluas pencarian database dan `weddingblocks_rsvp_enable_status_filter` untuk filter status modular.
- **Filter UI & Tampilan Admin** — Filter `weddingblocks_show_rsvp_row_actions` untuk mengontrol visibilitas baris aksi tombol Hapus, serta filter `weddingblocks_rsvp_extra_columns` untuk penyesuaian lebar tabel kosong secara otomatis.
- **Action Hook Pembersihan Cache** — Action `weddingblocks_clear_rsvps_count_cache` untuk memudahkan add-on menyinkronkan pembersihan cache hitungan RSVP.

### Fixed & Improved (Diperbaiki & Ditingkatkan)
- **Optimasi Database Query** — Query database pada `weddingblocks_get_rsvps()` dan `weddingblocks_get_rsvps_count()` kini 100% mandiri, bebas dari potensi error kolom `phone` atau `status` jika add-on tidak aktif.
- **RSVP Count Cache Invalidation** — Perbaikan invalidasi cache jumlah RSVP agar langsung terhapus saat data RSVP baru masuk atau dihapus, mencegah data *stale* pada persistent object cache.
- **Dasbor Admin RSVP** — Pemulihan tombol aksi "Hapus" bawaan dan penambahan badge styling kehadiran bawaan yang rapi dan mandiri tanpa memerlukan CSS eksternal.

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

[1.5.1]: #151---2026-09-03
[1.5.0]: #150---2026-08-28
[1.1.0]: #110---2026-07-16
[1.0.0]: #100---2025-01-xx
[Unreleased]: #unreleased
