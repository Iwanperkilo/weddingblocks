# WeddingBlocks

> Plugin WordPress untuk membuat **undangan digital pernikahan** berbasis **Gutenberg & Full Site Editing (FSE)** dengan desain modern, interaktif, dan mudah dikustomisasi.

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPLv2+-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)](https://www.php.net/)
[![Version](https://img.shields.io/badge/version-1.0.0-success.svg)](#changelog)

## ✨ Fitur Utama

- 🎨 **11+ Custom Gutenberg Blocks** siap pakai (Cover, Countdown, Couple Info, Guestbook, Music Player, RSVP, dll)
- 📱 **Responsive & Mobile-first** — tampil sempurna di semua perangkat
- ⚡ **Full Site Editing (FSE)** ready — bisa dipakai di block theme
- 💌 **Form RSVP** dengan penyimpanan ke database
- 📖 **Guestbook** untuk ucapan & doa dari tamu
- 🎵 **Music Player** untuk backsound undangan
- 🧩 **Custom Post Type `undangan`** khusus pernikahan
- 🛠️ **Halaman admin** untuk mengelola data
- 🌍 **Text Domain** siap untuk internasionalisasi (i18n)
- 🪶 **Zero dependency** — ringan & cepat

## 📦 Block yang Tersedia

| Block | Deskripsi |
|-------|-----------|
| `cover` | Sampul / pembuka undangan dengan foto & musik |
| `couple-name` | Nama pengantin pria & wanita |
| `couple-title` | Sapaan (Putra/Putri dari ...) |
| `couple-parents` | Info orang tua |
| `couple-photo` | Galeri foto pasangan |
| `couple-info` | Biodata singkat |
| `event-info` | Detail acara (tanggal, lokasi, waktu) |
| `countdown` | Hitung mundur menuju hari H |
| `music-player` | Pemutar musik otomatis |
| `rsvp-form` | Form konfirmasi kehadiran |
| `guest-name` | Sapaan personal untuk tamu |
| `guestbook` | Ucapan & doa dari tamu |

## 🚀 Instalasi

### Dari GitHub (manual)

1. Download atau clone repo ini:
   ```bash
   git clone https://github.com/iwanperkilo/weddingblocks.git
   ```
2. Copy folder `weddingblocks` ke direktori `wp-content/plugins/` WordPress Anda.
3. Buka **Dashboard WP → Plugins → Installed Plugins**, lalu **Activate** `WeddingBlocks`.
4. Buka menu **Undangan** di sidebar admin untuk mulai membuat undangan baru.

### Dari WordPress.org *(segera)*

Cari **WeddingBlocks** di direktori plugin WordPress dan klik **Install Now**.

## 🛠️ Persyaratan Sistem

| Kebutuhan | Versi Minimum |
|-----------|---------------|
| WordPress | 6.0+ |
| PHP | 7.4+ |
| MySQL/MariaDB | 5.7+ / 10.3+ |
| Browser | Modern (Chrome, Firefox, Safari, Edge) |

## 📂 Struktur Plugin

```
weddingblocks/
├── weddingblocks.php      # File utama plugin (bootstrap)
├── includes/
│   ├── blocks.php         # Registrasi semua Gutenberg blocks
│   ├── cpt.php            # Custom Post Type: undangan
│   ├── db.php             # Skema tabel RSVP & guestbook
│   ├── rsvp-handler.php   # Logic form RSVP
│   ├── admin-page.php     # Halaman admin tambahan
│   └── blocks/            # Definisi tiap block (block.json, render.php, dll)
├── assets/
│   ├── css/               # Stylesheet editor & frontend
│   └── js/                # Scripts editor & frontend
├── templates/             # Template PHP & HTML untuk CPT
├── readme.txt             # Standar WordPress.org plugin directory
├── LICENSE                # GPLv2+
└── CHANGELOG.md           # Riwayat versi
```

## ⚙️ Development

Plugin ini menggunakan **WordPress block API** standar. Untuk kontribusi:

```bash
# Clone repo
git clone https://github.com/iwanperkilo/weddingblocks.git

# Masuk ke direktori plugin
cd weddingblocks

# Buat branch fitur baru
git checkout -b feature/nama-fitur
```

Standar kontribusi:
- Ikuti [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).
- Gunakan `text-domain: weddingblocks` untuk semua string yang bisa diterjemahkan.
- Test di WordPress 6.0+ & PHP 7.4+ sebelum membuka PR.

## 🤝 Kontribusi

Kontribusi dalam bentuk apapun sangat diterima — silakan **fork**, buat branch, dan buka **Pull Request**.

Untuk usulan fitur baru atau perubahan besar, **wajib** membuka **Issue** terlebih dahulu untuk mendiskusikan ide Anda sebelum membuat Pull Request.

## 📜 Lisensi

Plugin ini dilisensikan di bawah **GPL v2 atau versi yang lebih baru**.

Copyright (C) 2025 **Perkilo**

Lihat file [LICENSE](LICENSE) untuk detail lengkap.

## 👤 Author

- **Perkilo** — [github.com/iwanperkilo](https://github.com/iwanperkilo)
- 🌐 [Plugin URI](https://wordpress.org/plugins/weddingblocks/)

## 🆘 Support

- 🐛 **Bug report / feature request:** buka [GitHub Issue](../../issues)
- 💬 **Diskusi:** buka [GitHub Discussions](../../discussions)

---

> Dibuat dengan ❤️ untuk pasangan-pasangan Indonesia yang ingin membagikan kabar bahagia mereka dengan cara modern.
