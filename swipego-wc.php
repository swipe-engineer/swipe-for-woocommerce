<?php
/**
 * Plugin Name:       Swipe for WooCommerce
 * Description:       Swipe payment integration for WooCommerce.
 * Version:           1.0.3
 * Requires at least: 4.6
 * Requires PHP:      7.0
 * Author:            Fintech Worldwide Sdn. Bhd.
 * Author URI:        https://swipego.io/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'Swipego_WC' ) ) return;

define( 'SWIPEGO_WC_FILE', __FILE__ );
define( 'SWIPEGO_WC_URL', plugin_dir_url( SWIPEGO_WC_FILE ) );
define( 'SWIPEGO_WC_PATH', plugin_dir_path( SWIPEGO_WC_FILE ) );
define( 'SWIPEGO_WC_BASENAME', plugin_basename( SWIPEGO_WC_FILE ) );
define( 'SWIPEGO_WC_VERSION', '1.0.3' );

// Plugin core class
require( SWIPEGO_WC_PATH . 'includes/class-swipego-wc.php' );
