<?php
/**
 * Server-side rendering for the Music Player block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$music_url = ! empty( $attributes['musicUrl'] ) ? esc_url( $attributes['musicUrl'] ) : 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
?>
<div class="weddingblocks-music-container" data-music-url="<?php echo esc_attr( $music_url ); ?>">
    <audio id="weddingblocks-audio" loop preload="auto">
        <source src="<?php echo esc_attr( $music_url ); ?>" type="audio/mpeg">
    </audio>
    <button id="weddingblocks-music-toggle" class="weddingblocks-music-btn paused" aria-label="Toggle Music" style="display: none;">
        <svg class="icon-play" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M8 5v14l11-7z"/>
        </svg>
        <svg class="icon-pause" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="display:none;">
            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
        </svg>
    </button>
</div>
