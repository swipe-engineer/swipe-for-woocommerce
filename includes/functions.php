<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Get plugin setting by key
function swipego_wc_get_setting( $key, $default = null ) {

    $settings = get_option( 'woocommerce_swipego_settings' );

    if ( isset( $settings[ $key ] ) && !empty( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }

    return $default;

}

// Display notice
function swipego_wc_notice( $message, $type = 'success' ) {

    $plugin = esc_html__( 'Swipe for WooCommerce', 'swipego-wc' );

    printf( '<div class="notice notice-%1$s"><p><strong>%2$s:</strong> %3$s</p></div>', esc_attr( $type ), $plugin, $message );

}

// Log a message in WooCommerce logs
function swipego_wc_logger( $message ) {

    if ( !function_exists( 'wc_get_logger' ) ) {
        return false;
    }

    return wc_get_logger()->add( 'swipego-wc', $message );

}

// Get approved businesses from Swipe
function swipego_wc_get_businesses() {

    try {

        $swipego = new Swipego_WC_API();
        $swipego->set_access_token( swipego_get_access_token() );

        list( $code, $response ) = $swipego->get_approved_businesses();

        $data = isset( $response['data'] ) ? $response['data'] : false;

        $businesses = array();

        if ( is_array( $data ) ) {

            foreach ( $data as $item ) {

                $business_id = isset( $item['id'] ) ? sanitize_text_field( $item['id'] ) : null;

                if ( !$business_id ) {
                    continue;
                }

                $businesses[ $business_id ] = array(
                    'id'             => $business_id,
                    'name'           => isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : null,
                    'integration_id' => isset( $item['integration']['id'] ) ? sanitize_text_field( $item['integration']['id'] ) : null,
                    'api_key'        => isset( $item['integration']['api_key'] ) ? sanitize_text_field( $item['integration']['api_key'] ) : null,
                    'signature_key'  => isset( $item['integration']['signature_key'] ) ? sanitize_text_field( $item['integration']['signature_key'] ) : null,
                );
            }
        }

        return $businesses;

    } catch ( Exception $e ) {
        return false;
    }

}

// Get business information from Swipe by its ID
function swipego_wc_get_business( $business_id ) {
    $businesses = swipego_wc_get_businesses();
    return isset( $businesses[ $business_id ] ) ? $businesses[ $business_id ] : false;
}
