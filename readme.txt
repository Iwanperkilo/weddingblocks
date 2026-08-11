=== WeddingBlocks ===
Contributors: iwanperkilo
Tags: wedding, invitation, gutenberg, fse, blocks
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://wordpress.org/plugins/weddingblocks/
Author URI: https://github.com/iwanperkilo

A Gutenberg & Full Site Editing (FSE) based digital wedding invitation plugin with a modern, interactive, and easily customizable design.

== Description ==

**WeddingBlocks** is a WordPress plugin that helps you create beautiful **digital wedding invitations** quickly and easily using the modern Gutenberg block system.

This plugin is perfect for:
* Couples who want to build their own wedding website/invitation
* Web designers / developers offering digital invitation services
* Wedding organizers looking for ready-to-use template layouts

= Key Features =

* 🎨 Over 10 **custom Gutenberg blocks** ready to use
* 📱 **Responsive design** optimized for all screens
* 💌 **RSVP Form** with direct database logging
* 📖 **Guestbook** for guest greetings & wishes
* 🎵 **Music Player** for automated backsound audio
* 🧩 **Full Site Editing (FSE)** support
* ⚡ Lightweight, fast, and **zero external dependencies**
* 🌍 i18n ready (Fully translatable)

= Available Blocks =

* **Cover** — Welcoming cover screen for the invitation
* **Couple Name** — Groom & Bride names
* **Couple Title** — Custom text/greetings (e.g. Putra/Putri dari ...)
* **Couple Parents** — Parents information
* **Couple Photo** — Gallery or single photo of the couple
* **Couple Info** — Brief biography
* **Event Info** — Wedding event schedule & location details
* **Countdown** — Countdown clock to the wedding day
* **Music Player** — Backsound soundtrack player
* **Guest Name** — Personalized guest name display from URL query
* **RSVP Form** — Attendance confirmation form
* **Guestbook** — Wishes, messages, and greetings stream

= Who Is This Plugin For? =

This plugin is created to help couples share their happy news in a modern, elegant way. With a block-based visual interface, anyone can create stunning digital invitations without any coding skills.

== Installation ==

1. Upload the `weddingblocks` directory to the `/wp-content/plugins/` directory, or install it directly via the WordPress admin dashboard.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Open the **Undangan** menu in your admin sidebar to start creating invitations.
4. Use the custom WeddingBlocks in the Gutenberg editor to build your invitation page.

== Frequently Asked Questions ==

= Is this plugin free? =

Yes, WeddingBlocks is 100% free and open-source under the GPL v2 or later license.

= Do I need a block / FSE theme? =

No. While the plugin works on classic themes, we recommend using a block theme (such as Twenty Twenty-Four) to get the best experience and utilize FSE templates.

= How do I add music? =

Add the **Music Player** block to your page, then upload your MP3 audio file in the block settings panel.

= Will RSVP responses be saved? =

Yes, all RSVP data is securely saved in your WordPress database and can be managed from the admin dashboard under **Undangan → RSVP**.

= Can I translate this plugin? =

Absolutely! The plugin is fully translatable and uses the `weddingblocks` text domain.

== Screenshots ==

1. Invitation cover with fade animation
2. Countdown clock to the big day
3. Event details (date, time, venue address)
4. Guest RSVP confirmation form
5. Guestbook wishes stream
6. Mobile preview

== Changelog ==

= 1.3.0 =
* Continuous / Attention Animations - Added a second, independent animation layer for looping effects: Sway, Float, Pulse, Wobble, and Shake.
* Each block can combine an entrance animation and a continuous effect at the same time without conflicting (the continuous effect waits for the entrance animation to finish first).
* Added Speed and Intensity controls for every continuous animation, plus a Pivot Point (Titik Goyang) control for Sway, Wobble, and Pulse to change where the motion is anchored.
* Continuous animations pause automatically when a block scrolls out of view, and are fully disabled under prefers-reduced-motion, matching the existing entrance-animation accessibility behavior.
* Decorative Layer & Decorative Wrapper - Two new blocks for wrapping any content area with floating decorative elements (butterflies, birds, snow) without affecting the underlying layout.
* Extensibility - Added internal action/filter hook points across block registration, RSVP saving, and the admin RSVP table, laying the groundwork for future add-ons.
* RSVP Form - Added an optional hidden guest-token field, passed through to the RSVP submission when present in the URL. No effect on its own; lays the groundwork for an upcoming add-on to link RSVP submissions back to an imported guest list.
* Design Tokens & Theme-Independent Styling - The invitation now ships with its own default color tokens and typography (h1-h4, paragraph) that no longer depend on the active theme, while remaining editable per-invitation (via a new "Tema Warna" panel) and per-block. Brand colors were migrated to CSS custom properties so a theme color change cascades consistently across all blocks.
* Self-Hosted Local Fonts - Montserrat, Playfair Display, and Great Vibes are now bundled locally (woff2, latin subset) so the plugin's typography renders consistently without relying on any external CDN, matching WordPress.org best practices.

= 1.2.0 =
* Animation System - Added a centralized animation system with 5 entrance animation styles (fadeUp, fadeIn, slideLeft, slideRight, zoomIn).
* Scroll-triggered animations powered by native IntersectionObserver with zero external dependencies.
* Added animationStyle, animationDuration, and animationDelay controls to all custom blocks.
* Extended animation support to core WordPress blocks (Heading, Paragraph, Image, and Group).
* Added continuous animations for cover button breathe effect, cover content entrance, and countdown ticking.
* Added interactive visual feedback including avatar hover zoom and button ripple effects.
* Added state animations for RSVP alerts and guestbook item entrances.
* Accessibility - Automatically disables all animations when prefers-reduced-motion is enabled.

= 1.1.0 =
* FSE & Template Editor - Blocks can now be used in the WordPress template editor (previously only restricted to "undangan" CPT).
* Comprehensive Settings - Added advanced control panel options for each block.
* CSS & Transition improvements - Simplified block frontend styles and enhanced transitions.
* Music Player - Added customization options to the Music Player block.
* Guest Name - Added prefix toggle, text alignment, and font size options.
* Event Info - Implemented the Event Info block with layout variations and custom editor styles.
* Layout enhancements - Migrated couple and event info layouts to CSS Grid and container queries.
* Cover block improvements - Auto-contrast text color, boxed cover width variant, and admin bar overlap prevention.
* RSVP & Guestbook - Added guest count field, optimized frontend code, and restored scroll position when cover opens.

= 1.0.0 =
* 🎉 Initial release of WeddingBlocks
* 11+ custom Gutenberg blocks (cover, countdown, couple info, event info, music player, RSVP, guestbook, etc.)
* Custom Post Type "undangan"
* Admin dashboard to manage RSVPs & guestbook
* Pre-designed invitation template (classic + FSE)
* Full support for Full Site Editing (FSE)
* Zero external dependencies

== Upgrade Notice ==

= 1.3.0 =
This version adds continuous/looping animations (Sway, Float, Pulse, Wobble, Shake) and new Decorative Layer/Wrapper blocks. Safe to upgrade, no breaking changes.

= 1.1.0 =
This version adds template editor support, advanced block controls, grid layouts, and custom cover options. Upgrading is highly recommended.

= 1.0.0 =
Initial release of WeddingBlocks. No upgrades required.

== Credits ==

Made with ❤️ by **Perkilo** ([github.com/iwanperkilo](https://github.com/iwanperkilo)) for the wedding community.

Bundled fonts (Montserrat, Playfair Display, and Great Vibes) are licensed under the SIL Open Font License 1.1 — see `assets/fonts/OFL.txt` for the license text and copyright notices. The plugin itself is licensed under GPL v2 or later.