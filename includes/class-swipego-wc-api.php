<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Swipego_WC_API extends Swipego_API_WC {

    // Initialize API
    public function __construct() {

        $this->set_api_key( swipego_wc_get_setting( 'api_key' ) );
        $this->set_signature_key(swipego_wc_get_setting( 'signature_key' ) );
        $this->set_business_id(swipego_wc_get_setting( 'business_id' ) );
        $this->set_business_name(swipego_wc_get_setting( 'business_name' ) );
        $this->set_environment( swipego_wc_get_setting( 'environment', 'sandbox' ) );
        $this->set_debug( swipego_wc_get_setting( 'debug' ) ? true : false );

    }

    // Log a message in WooCommerce logs
    protected function log( $message ) {

        if ( $this->debug ) {
            swipego_wc_logger( $message );
        }

    }

}
