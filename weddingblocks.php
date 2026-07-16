<?php
/**
 * Plugin Name: WeddingBlocks
 * Plugin URI: https://wordpress.org/plugins/weddingblocks/
 * Description: A digital invitation plugin based on Gutenberg & Full Site Editing (FSE) with a modern, interactive, and easily customizable design.
 * Version: 1.1.0
 * Author: Perkilo
 * Author URI: https://github.com/iwanperkilo
 * License: GPLv2 or later
 * Text Domain: weddingblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define Constants.
define( 'WEDDINGBLOCKS_VERSION', '1.1.0' );
define( 'WEDDINGBLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WEDDINGBLOCKS_URL', plugin_dir_url( __FILE__ ) );

// Load Sub-modules.
require_once WEDDINGBLOCKS_PATH . 'includes/helpers.php';
require_once WEDDINGBLOCKS_PATH . 'includes/db.php';
require_once WEDDINGBLOCKS_PATH . 'includes/cpt.php';
require_once WEDDINGBLOCKS_PATH . 'includes/rsvp-handler.php';
require_once WEDDINGBLOCKS_PATH . 'includes/admin-page.php';
require_once WEDDINGBLOCKS_PATH . 'includes/blocks.php';

// Plugin Activation.
register_activation_hook( __FILE__, 'weddingblocks_activate' );
function weddingblocks_activate() {
    // Install or update database.
    if ( function_exists( 'weddingblocks_db_install' ) ) {
        weddingblocks_db_install();
    }
    
    // Flush rewrite rules for CPT.
    if ( function_exists( 'weddingblocks_register_cpt' ) ) {
        weddingblocks_register_cpt();
    }
    flush_rewrite_rules();
}

// Plugin Deactivation.
register_deactivation_hook( __FILE__, 'weddingblocks_deactivate' );
function weddingblocks_deactivate() {
    flush_rewrite_rules();
}
