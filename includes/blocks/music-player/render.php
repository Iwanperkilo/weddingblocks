<?php

/**
 * Server-side rendering for the Music Player block.
 *
 * @package WeddingBlocks
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals

if (! defined('ABSPATH')) {
    exit;
}

$music_url = ! empty($attributes['musicUrl']) ? esc_url($attributes['musicUrl']) : '';

if (empty($music_url)) {
    return;
}

$loop_music      = ! isset($attributes['loopMusic']) || ! empty($attributes['loopMusic']);
$button_position = (isset($attributes['buttonPosition']) && 'left' === $attributes['buttonPosition']) ? 'left' : 'right';
$button_color    = ! empty($attributes['buttonColor']) ? weddingblocks_sanitize_color($attributes['buttonColor']) : '#b5a46d';
$button_bg_color = ! empty($attributes['buttonBgColor']) ? weddingblocks_sanitize_color($attributes['buttonBgColor']) : 'rgba(255, 255, 255, 0.85)';

$button_class = 'weddingblocks-music-btn paused';
if ('left' === $button_position) {
    $button_class .= ' weddingblocks-music-btn--left';
}
?>
<div class="weddingblocks-music-container" data-music-url="<?php echo esc_attr($music_url); ?>">
    <audio id="weddingblocks-audio" <?php echo $loop_music ? 'loop' : ''; ?> preload="auto">
        <source src="<?php echo esc_attr($music_url); ?>" type="audio/mpeg">
    </audio>
    <button id="weddingblocks-music-toggle" class="<?php echo esc_attr($button_class); ?>" aria-label="Toggle Music" style="display: none; --wb-music-color: <?php echo esc_attr($button_color); ?>; --wb-music-bg-color: <?php echo esc_attr($button_bg_color); ?>;">
        <svg class="icon-play" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
            <path d="M8 5v14l11-7z" />
        </svg>
        <svg class="icon-pause" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" style="display:none;">
            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
        </svg>
    </button>
</div>