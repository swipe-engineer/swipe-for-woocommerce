<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Swipego_WC_Admin {

    // Register hooks
    public function __construct() {

        add_action( 'plugin_action_links_' . SWIPEGO_WC_BASENAME, array( $this, 'register_settings_link' ) );
        add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );

    }

    // Register plugin settings link
    public function register_settings_link( $links ) {

        $url = admin_url( 'admin.php?page=swipego' );
        $label = esc_html__( 'Settings', 'swipego-wc' );

        $settings_link = sprintf( '<a href="%s">%s</a>', $url, $label );
        array_unshift( $links, $settings_link );

        return $links;

    }

    // Show notice if WooCommerce not installed
    public function woocommerce_notice() {

        if ( !swipego_is_plugin_activated( 'woocommerce/woocommerce.php' ) ) {
            swipego_wc_notice( __( 'WooCommerce needs to be installed and activated.', 'swipego-wc' ), 'error' );
        }

    }

}
new Swipego_WC_Admin();
