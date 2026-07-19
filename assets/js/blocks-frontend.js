/**
 * Frontend Interactive Scripts for WeddingBlocks.
 */
document.addEventListener('DOMContentLoaded', function () {
    var entranceAnimationsStarted = false;

    function parseCountdownTarget(targetStr) {
        if (!targetStr) {
            return null;
        }

        var normalized = String(targetStr).trim();
        if (!normalized) {
            return null;
        }

        // Support both "YYYY-MM-DDTHH:MM" and "YYYY-MM-DD HH:MM".
        if (normalized.indexOf('T') === -1 && /^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/.test(normalized)) {
            normalized = normalized.replace(/\s+/, 'T');
        }

        // If the author only entered a date, default to 09:00 WIB rather than
        // feeding an incomplete value to Date() and producing NaN/NA in the UI.
        if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) {
            normalized += 'T09:00';
        }

        var parsed = new Date(normalized);
        if (isNaN(parsed.getTime())) {
            return null;
        }

        return parsed.getTime();
    }

    function initCoverAnimations() {
        var coverNodes = document.querySelectorAll('#weddingblocks-cover[data-wb-anim], #weddingblocks-cover [data-wb-anim]');
        coverNodes.forEach(function (el) {
            var duration = el.getAttribute('data-wb-duration') || 600;
            var delay = el.getAttribute('data-wb-delay') || 0;
            el.style.setProperty('--wb-anim-duration', duration + 'ms');
            el.style.setProperty('--wb-anim-delay', delay + 'ms');
            el.classList.add('wb-anim--' + el.getAttribute('data-wb-anim'), 'wb-animated');
        });
    }

    function initEntranceAnimations() {
        if (entranceAnimationsStarted) {
            return;
        }
        entranceAnimationsStarted = true;

        var nodes = document.querySelectorAll('[data-wb-anim]');
        if (!nodes.length) {
            return;
        }

        if ('IntersectionObserver' in window) {
            var animObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var el = entry.target;
                    var duration = el.getAttribute('data-wb-duration') || 600;
                    var delay = el.getAttribute('data-wb-delay') || 0;
                    el.style.setProperty('--wb-anim-duration', duration + 'ms');
                    el.style.setProperty('--wb-anim-delay', delay + 'ms');
                    el.classList.add('wb-anim--' + el.getAttribute('data-wb-anim'), 'wb-animated');
                    animObserver.unobserve(el);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            nodes.forEach(function (el) {
                if (el.classList.contains('wb-animated')) {
                    return;
                }
                animObserver.observe(el);
            });
        } else {
            // Fallback: tampilkan semua elemen tanpa animasi.
            nodes.forEach(function (el) {
                el.style.opacity = '1';
            });
        }
    }

    // Lock scroll initially if cover is present.
    var cover = document.getElementById('weddingblocks-cover');
    var lockedScrollY = 0;
    if (cover) {
        // Move cover to the top of body to bypass any FSE layout constraints on desktop
        document.body.insertBefore(cover, document.body.firstChild);

        // Remember scroll position before switching body to position:fixed,
        // otherwise the page silently jumps back to the top once unlocked.
        lockedScrollY = window.scrollY || window.pageYOffset || 0;
        document.body.style.top = '-' + lockedScrollY + 'px';
        document.body.classList.add('weddingblocks-locked');
    }

    // --- 1. COVER OPEN & AUDIO AUTOPLAY ---
    var openBtn = document.getElementById('weddingblocks-open-btn');
    var audio = document.getElementById('weddingblocks-audio');
    var musicToggle = document.getElementById('weddingblocks-music-toggle');

    if (openBtn && cover) {
        openBtn.addEventListener('click', function () {
            // Fade out/slide up cover.
            cover.classList.add('opened');
            document.body.classList.remove('weddingblocks-locked');
            document.body.style.top = '';
            window.scrollTo(0, lockedScrollY);
            window.dispatchEvent(new Event('weddingblocks:cover-opened'));

            // Play music.
            if (audio) {
                audio.play().then(function () {
                    if (musicToggle) {
                        musicToggle.style.display = 'flex';
                        musicToggle.classList.remove('paused');
                        musicToggle.classList.add('playing');

                        var playIcon = musicToggle.querySelector('.icon-play');
                        var pauseIcon = musicToggle.querySelector('.icon-pause');
                        if (playIcon) playIcon.style.display = 'none';
                        if (pauseIcon) pauseIcon.style.display = 'block';
                    }
                }).catch(function (error) {
                    console.log('Autoplay music blocked or failed:', error);
                    // Still show toggle to let user play manually.
                    if (musicToggle) {
                        musicToggle.style.display = 'flex';
                    }
                });
            }
        });
    }

    // --- 2. FLOATING MUSIC TOGGLE ---
    if (musicToggle && audio) {
        musicToggle.addEventListener('click', function () {
            var playIcon = musicToggle.querySelector('.icon-play');
            var pauseIcon = musicToggle.querySelector('.icon-pause');

            if (audio.paused) {
                audio.play();
                musicToggle.classList.remove('paused');
                musicToggle.classList.add('playing');
                if (playIcon) playIcon.style.display = 'none';
                if (pauseIcon) pauseIcon.style.display = 'block';
            } else {
                audio.pause();
                musicToggle.classList.remove('playing');
                musicToggle.classList.add('paused');
                if (playIcon) playIcon.style.display = 'block';
                if (pauseIcon) pauseIcon.style.display = 'none';
            }
        });
    }

    // --- 3. COUNTDOWN TIMER ---
    var countdowns = document.querySelectorAll('.weddingblocks-countdown');
    countdowns.forEach(function (countdown) {
        var targetStr = countdown.getAttribute('data-target');
        var targetDate = parseCountdownTarget(targetStr);

        var daysVal = countdown.querySelector('.days');
        var hoursVal = countdown.querySelector('.hours');
        var minutesVal = countdown.querySelector('.minutes');
        var secondsVal = countdown.querySelector('.seconds');

        if (targetDate === null) {
            if (daysVal) daysVal.innerText = '00';
            if (hoursVal) hoursVal.innerText = '00';
            if (minutesVal) minutesVal.innerText = '00';
            if (secondsVal) secondsVal.innerText = '00';
            return;
        }

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = targetDate - now;

            if (distance < 0) {
                if (daysVal) daysVal.innerText = '00';
                if (hoursVal) hoursVal.innerText = '00';
                if (minutesVal) minutesVal.innerText = '00';
                if (secondsVal) secondsVal.innerText = '00';
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            function setVal(el, val) {
                if (!el) return;
            var formatted = String(val).padStart(2, '0');
            if (formatted === 'NaN' || !isFinite(val)) {
                formatted = '00';
            }
            if (el.innerText !== formatted) {
                el.classList.remove('wb-tick');
                // Force reflow agar animasi bisa diulang
                void el.offsetWidth;
                el.classList.add('wb-tick');
                }
                el.innerText = formatted;
            }

            setVal(daysVal, days);
            setVal(hoursVal, hours);
            setVal(minutesVal, minutes);
            setVal(secondsVal, seconds);
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });

    // --- 4. RSVP FORM INTERACTIVITY (SHOW/HIDE PAX) ---
    var rsvpAttendance = document.getElementById('rsvp-attendance');
    var rsvpGuestsGroup = document.getElementById('rsvp-guests-group');
    // rsvpGuestsGroup may not exist at all if "showGuestsField" is turned off
    // for this block instance, so every reference below is null-guarded.
    if (rsvpAttendance && rsvpGuestsGroup) {
        rsvpAttendance.addEventListener('change', function () {
            if (this.value === 'hadir') {
                rsvpGuestsGroup.style.display = 'block';
            } else {
                rsvpGuestsGroup.style.display = 'none';
            }
        });
    }

    // --- 5. AJAX RSVP FORM SUBMISSION ---
    var rsvpForm = document.getElementById('weddingblocks-rsvp-form');
    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitBtn = document.getElementById('rsvp-submit-btn');
            var btnText = submitBtn.querySelector('.btn-text');
            var btnSpinner = submitBtn.querySelector('.btn-spinner');
            var alertBox = document.getElementById('rsvp-message-alert');

            // Hide/Show Loader
            if (btnText) btnText.style.opacity = '0.3';
            if (btnSpinner) btnSpinner.style.display = 'inline-block';
            submitBtn.disabled = true;

            alertBox.style.display = 'none';
            alertBox.className = 'rsvp-alert';

            // Gather Data
            var postId = rsvpForm.getAttribute('data-post-id');
            var guestName = document.getElementById('rsvp-name').value;
            var attendance = document.getElementById('rsvp-attendance').value;
            var guestsField = document.getElementById('rsvp-guests');
            var guestsCount = guestsField ? guestsField.value : 1;
            var message = document.getElementById('rsvp-message').value;

            // Custom success/error messages set via block attributes (data-* on the form).
            // Falls back to whatever the REST API responds with if not set.
            var customSuccessMessage = rsvpForm.getAttribute('data-success-message');
            var customErrorMessage = rsvpForm.getAttribute('data-error-message');

            var payload = {
                post_id: postId,
                guest_name: guestName,
                attendance: attendance,
                guests_count: guestsCount,
                message: message,
                max_guests: rsvpForm.getAttribute('data-max-guests') || 10
            };

            // Post to WP REST API
            fetch(weddingblocks_api.rest_url + '/rsvp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': weddingblocks_api.nonce
                },
                body: JSON.stringify(payload)
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { status: response.status, data: data };
                    });
                })
                .then(function (res) {
                    // Restore button states
                    if (btnText) btnText.style.opacity = '1';
                    if (btnSpinner) btnSpinner.style.display = 'none';
                    submitBtn.disabled = false;

                    if (res.status === 200 && res.data.success) {
                        // Success
                        alertBox.innerText = customSuccessMessage || res.data.message;
                        alertBox.classList.add('success');
                        alertBox.style.display = 'block';

                        // Reset form fields but keep name
                        document.getElementById('rsvp-attendance').value = '';
                        if (guestsField) guestsField.value = 1;
                        document.getElementById('rsvp-message').value = '';
                        if (rsvpGuestsGroup) rsvpGuestsGroup.style.display = 'none';

                        // Append dynamically to Guestbook
                        var guestbookList = document.getElementById('weddingblocks-guestbook-list');
                        var emptyPlaceholder = document.getElementById('guestbook-empty-placeholder');

                        if (guestbookList) {
                            if (emptyPlaceholder) {
                                emptyPlaceholder.remove();
                            }

                            // Create new item card
                            var newItem = document.createElement('div');
                            newItem.className = 'guestbook-item new-item';

                            var badgeClass = '';
                            var badgeLabel = '';
                            switch (res.data.data.attendance) {
                                case 'hadir':
                                    badgeClass = 'badge-hadir';
                                    badgeLabel = 'Hadir';
                                    break;
                                case 'tidak_hadir':
                                    badgeClass = 'badge-tidak-hadir';
                                    badgeLabel = 'Tidak Hadir';
                                    break;
                                case 'ragu_ragu':
                                    badgeClass = 'badge-ragu';
                                    badgeLabel = 'Ragu-ragu';
                                    break;
                            }

                            var itemContent =
                                '<div class="guestbook-header">' +
                                '<h5 class="guest-name">' + escapeHtml(res.data.data.guest_name) + '</h5>' +
                                '<span class="guest-status ' + badgeClass + '">' + badgeLabel + '</span>' +
                                '</div>' +
                                '<p class="guest-message">' + escapeHtml(res.data.data.message).replace(/\n/g, '<br>') + '</p>' +
                                '<span class="guest-time">' + res.data.data.formatted_time + '</span>';

                            newItem.innerHTML = itemContent;
                            guestbookList.insertBefore(newItem, guestbookList.firstChild);

                            // Scroll to the new comment
                            newItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }

                    } else {
                        // Error
                        alertBox.innerText = customErrorMessage || res.data.message || 'Gagal mengirim konfirmasi.';
                        alertBox.classList.add('error');
                        alertBox.style.display = 'block';
                    }
                })
                .catch(function (error) {
                    if (btnText) btnText.style.opacity = '1';
                    if (btnSpinner) btnSpinner.style.display = 'none';
                    submitBtn.disabled = false;

                    alertBox.innerText = customErrorMessage || 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                    alertBox.classList.add('error');
                    alertBox.style.display = 'block';
                    console.error('RSVP error:', error);
                });
        });
    }

    // Helper: Escapes HTML strings to prevent XSS.
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // --- 6. SCROLL-TRIGGERED ENTRANCE ANIMATIONS ---
    if (!cover) {
        initEntranceAnimations();
    } else {
        initCoverAnimations();
    }

    window.addEventListener('weddingblocks:cover-opened', initEntranceAnimations);
});
