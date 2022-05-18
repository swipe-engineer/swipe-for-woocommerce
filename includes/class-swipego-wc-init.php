<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Swipego_WC_Init {

    private $gateway_class = 'Swipego_WC_Gateway';

    // Register hooks
    public function __construct() {

        add_action( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
        add_action( 'init', array( $this, 'load_dependencies' ) );

    }

    // Register Swipe as WooCommerce payment method
    public function register_gateway( $methods ) {

        global $current_screen;

        $current_screen_id = isset( $current_screen->id ) ? $current_screen->id : false;

        // This is to hide the payment method in the WooCommerce settings page
        if ( !is_admin() && $current_screen_id !== 'woocommerce_page_wc-settings' ) {
            $methods[] = $this->gateway_class;
        }

        return $methods;

    }

    // Load required files
    public function load_dependencies() {

        if ( !class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        require_once( SWIPEGO_WC_PATH . 'includes/class-swipego-wc-gateway.php' );

    }

}
new Swipego_WC_Init();
