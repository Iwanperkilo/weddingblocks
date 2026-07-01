/**
 * Frontend Interactive Scripts for WeddingBlocks.
 */
document.addEventListener( 'DOMContentLoaded', function() {
    
    // Lock scroll initially if cover is present.
    var cover = document.getElementById( 'weddingblocks-cover' );
    if ( cover ) {
        // Move cover to the top of body to bypass any FSE layout constraints on desktop
        document.body.insertBefore( cover, document.body.firstChild );
        document.body.classList.add( 'weddingblocks-locked' );
    }

    // --- 1. COVER OPEN & AUDIO AUTOPLAY ---
    var openBtn = document.getElementById( 'weddingblocks-open-btn' );
    var audio = document.getElementById( 'weddingblocks-audio' );
    var musicToggle = document.getElementById( 'weddingblocks-music-toggle' );

    if ( openBtn && cover ) {
        openBtn.addEventListener( 'click', function() {
            // Fade out/slide up cover.
            cover.classList.add( 'opened' );
            document.body.classList.remove( 'weddingblocks-locked' );

            // Play music.
            if ( audio ) {
                audio.play().then( function() {
                    if ( musicToggle ) {
                        musicToggle.style.display = 'flex';
                        musicToggle.classList.remove( 'paused' );
                        musicToggle.classList.add( 'playing' );
                        
                        var playIcon = musicToggle.querySelector( '.icon-play' );
                        var pauseIcon = musicToggle.querySelector( '.icon-pause' );
                        if ( playIcon ) playIcon.style.display = 'none';
                        if ( pauseIcon ) pauseIcon.style.display = 'block';
                    }
                } ).catch( function( error ) {
                    console.log( 'Autoplay music blocked or failed:', error );
                    // Still show toggle to let user play manually.
                    if ( musicToggle ) {
                        musicToggle.style.display = 'flex';
                    }
                } );
            }
        } );
    }

    // --- 2. FLOATING MUSIC TOGGLE ---
    if ( musicToggle && audio ) {
        musicToggle.addEventListener( 'click', function() {
            var playIcon = musicToggle.querySelector( '.icon-play' );
            var pauseIcon = musicToggle.querySelector( '.icon-pause' );

            if ( audio.paused ) {
                audio.play();
                musicToggle.classList.remove( 'paused' );
                musicToggle.classList.add( 'playing' );
                if ( playIcon ) playIcon.style.display = 'none';
                if ( pauseIcon ) pauseIcon.style.display = 'block';
            } else {
                audio.pause();
                musicToggle.classList.remove( 'playing' );
                musicToggle.classList.add( 'paused' );
                if ( playIcon ) playIcon.style.display = 'block';
                if ( pauseIcon ) pauseIcon.style.display = 'none';
            }
        } );
    }

    // --- 3. COUNTDOWN TIMER ---
    var countdowns = document.querySelectorAll( '.weddingblocks-countdown' );
    countdowns.forEach( function( countdown ) {
        var targetStr = countdown.getAttribute( 'data-target' );
        if ( ! targetStr ) return;

        // Parse target date. Format expected: "YYYY-MM-DDTHH:MM" or standard ISO.
        var targetDate = new Date( targetStr ).getTime();

        var daysVal = countdown.querySelector( '.days' );
        var hoursVal = countdown.querySelector( '.hours' );
        var minutesVal = countdown.querySelector( '.minutes' );
        var secondsVal = countdown.querySelector( '.seconds' );

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = targetDate - now;

            if ( distance < 0 ) {
                if ( daysVal ) daysVal.innerText = '00';
                if ( hoursVal ) hoursVal.innerText = '00';
                if ( minutesVal ) minutesVal.innerText = '00';
                if ( secondsVal ) secondsVal.innerText = '00';
                return;
            }

            var days = Math.floor( distance / ( 1000 * 60 * 60 * 24 ) );
            var hours = Math.floor( ( distance % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 ) );
            var minutes = Math.floor( ( distance % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 ) );
            var seconds = Math.floor( ( distance % ( 1000 * 60 ) ) / 1000 );

            if ( daysVal ) daysVal.innerText = String( days ).padStart( 2, '0' );
            if ( hoursVal ) hoursVal.innerText = String( hours ).padStart( 2, '0' );
            if ( minutesVal ) minutesVal.innerText = String( minutes ).padStart( 2, '0' );
            if ( secondsVal ) secondsVal.innerText = String( seconds ).padStart( 2, '0' );
        }

        updateCountdown();
        setInterval( updateCountdown, 1000 );
    } );

    // --- 4. RSVP FORM INTERACTIVITY (SHOW/HIDE PAX) ---
    var rsvpAttendance = document.getElementById( 'rsvp-attendance' );
    var rsvpGuestsGroup = document.getElementById( 'rsvp-guests-group' );
    if ( rsvpAttendance && rsvpGuestsGroup ) {
        rsvpAttendance.addEventListener( 'change', function() {
            if ( this.value === 'hadir' ) {
                rsvpGuestsGroup.style.display = 'block';
            } else {
                rsvpGuestsGroup.style.display = 'none';
            }
        } );
    }

    // --- 5. AJAX RSVP FORM SUBMISSION ---
    var rsvpForm = document.getElementById( 'weddingblocks-rsvp-form' );
    if ( rsvpForm ) {
        rsvpForm.addEventListener( 'submit', function( e ) {
            e.preventDefault();

            var submitBtn = document.getElementById( 'rsvp-submit-btn' );
            var btnText = submitBtn.querySelector( '.btn-text' );
            var btnSpinner = submitBtn.querySelector( '.btn-spinner' );
            var alertBox = document.getElementById( 'rsvp-message-alert' );

            // Hide/Show Loader
            if ( btnText ) btnText.style.opacity = '0.3';
            if ( btnSpinner ) btnSpinner.style.display = 'inline-block';
            submitBtn.disabled = true;

            alertBox.style.display = 'none';
            alertBox.className = 'rsvp-alert';

            // Gather Data
            var postId = rsvpForm.getAttribute( 'data-post-id' );
            var guestName = document.getElementById( 'rsvp-name' ).value;
            var attendance = document.getElementById( 'rsvp-attendance' ).value;
            var guestsCount = document.getElementById( 'rsvp-guests' ).value;
            var message = document.getElementById( 'rsvp-message' ).value;

            var payload = {
                post_id: postId,
                guest_name: guestName,
                attendance: attendance,
                guests_count: guestsCount,
                message: message
            };

            // Post to WP REST API
            fetch( weddingblocks_api.rest_url + '/rsvp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': weddingblocks_api.nonce
                },
                body: JSON.stringify( payload )
            } )
            .then( function( response ) {
                return response.json().then( function( data ) {
                    return { status: response.status, data: data };
                } );
            } )
            .then( function( res ) {
                // Restore button states
                if ( btnText ) btnText.style.opacity = '1';
                if ( btnSpinner ) btnSpinner.style.display = 'none';
                submitBtn.disabled = false;

                if ( res.status === 200 && res.data.success ) {
                    // Success
                    alertBox.innerText = res.data.message;
                    alertBox.classList.add( 'success' );
                    alertBox.style.display = 'block';

                    // Reset form fields but keep name
                    document.getElementById( 'rsvp-attendance' ).value = '';
                    document.getElementById( 'rsvp-guests' ).value = 1;
                    document.getElementById( 'rsvp-message' ).value = '';
                    if ( rsvpGuestsGroup ) rsvpGuestsGroup.style.display = 'none';

                    // Append dynamically to Guestbook
                    var guestbookList = document.getElementById( 'weddingblocks-guestbook-list' );
                    var emptyPlaceholder = document.getElementById( 'guestbook-empty-placeholder' );

                    if ( guestbookList ) {
                        if ( emptyPlaceholder ) {
                            emptyPlaceholder.remove();
                        }

                        // Create new item card
                        var newItem = document.createElement( 'div' );
                        newItem.className = 'guestbook-item new-item';

                        var badgeClass = '';
                        var badgeLabel = '';
                        switch ( res.data.data.attendance ) {
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
                                '<h5 class="guest-name">' + escapeHtml( res.data.data.guest_name ) + '</h5>' +
                                '<span class="guest-status ' + badgeClass + '">' + badgeLabel + '</span>' +
                            '</div>' +
                            '<p class="guest-message">' + escapeHtml( res.data.data.message ).replace(/\n/g, '<br>') + '</p>' +
                            '<span class="guest-time">' + res.data.data.formatted_time + '</span>';

                        newItem.innerHTML = itemContent;
                        guestbookList.insertBefore( newItem, guestbookList.firstChild );
                        
                        // Scroll to the new comment
                        newItem.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
                    }

                } else {
                    // Error
                    alertBox.innerText = res.data.message || 'Gagal mengirim konfirmasi.';
                    alertBox.classList.add( 'error' );
                    alertBox.style.display = 'block';
                }
            } )
            .catch( function( error ) {
                if ( btnText ) btnText.style.opacity = '1';
                if ( btnSpinner ) btnSpinner.style.display = 'none';
                submitBtn.disabled = false;

                alertBox.innerText = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                alertBox.classList.add( 'error' );
                alertBox.style.display = 'block';
                console.error( 'RSVP error:', error );
            } );
        } );
    }

    // Helper: Escapes HTML strings to prevent XSS.
    function escapeHtml( str ) {
        var div = document.createElement( 'div' );
        div.appendChild( document.createTextNode( str ) );
        return div.innerHTML;
    }
} );
