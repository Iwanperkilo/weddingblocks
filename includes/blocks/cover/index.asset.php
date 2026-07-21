<?php
if (! defined('ABSPATH')) {
    exit;
}

return array(
    'dependencies' => array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'),
    'version'      => filemtime(__DIR__ . '/index.js'),
);
