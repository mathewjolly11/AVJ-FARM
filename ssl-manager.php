<?php
/**
 * Plugin Name: SSL Manager
 * Description: Automatically handles SSL activation, installation and renewal.
 * Version:     1.0.0
 * Author:      Namecheap, Inc.
 * Author URI:  https://namecheap.com
 * License:     GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('DEBUG', isset($_GET['debug']));

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('error_reporting', E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/ssl-manager.install.log');
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('error_reporting', 0);
}

/**
 * Enqueue the plugin styles.
 */
function auto_ssl_enqueue_styles() {
    wp_enqueue_style(
        'ssl-manager-styles',
        plugin_dir_url(__FILE__) . 'assets/style.css',
        array(),
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'auto_ssl_enqueue_styles');

/**
 * Add the plugin admin menu.
 */
function auto_ssl_plugin_menu() {
    add_menu_page(
        'SSL Manager',
        'SSL Manager',
        'manage_options',
        'ssl-manager',
        'auto_ssl_render_admin_page',
        'none',
        100
    );
    if (DEBUG) {
        add_submenu_page(
            'ssl-manager',
            'Report',
            'Report',
            'manage_options',
            'ssl-manager-report',
            'auto_ssl_render_report_page'
        );
    }
}
add_action( 'admin_menu', 'auto_ssl_plugin_menu' );

/**
 * Render the main admin page.
 */
function auto_ssl_render_admin_page() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/admin-page.php';
}

/**
 * Render the Report page.
 */
function auto_ssl_render_report_page() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/report-page.php';
}
