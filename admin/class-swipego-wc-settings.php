<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Swipego_WC_Settings {

    private $id = 'swipego_wc_settings';

    private $keys = array(
        'enabled',
        'title',
        'description',
        'api_key',
        'signature_key',
        'environment',
        'business_id',
        'business_name',
    );

    // Register hooks
    public function __construct() {

        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_ajax_swipego_wc_update_settings', array( $this, 'update_settings' ) );
        add_action( 'wp_ajax_swipego_wc_retrieve_api_credentials', array( $this, 'retrieve_api_credentials' ) );
        add_action( 'wp_ajax_swipego_wc_set_webhook', array( $this, 'set_webhook' ) );

        $this->init();

    }

    // Initialize settings
    private function init() {

        $settings = get_option( 'woocommerce_swipego_settings' );

        $defaults = array(
            'enabled'       => 'no',
            'title'         => __( 'Pay Using', 'swipego-wc' ),
            'description'   => __( 'Pay with Maybank2u, CIMB Clicks, Bank Islam, RHB, Hong Leong Bank, Bank Muamalat, Public Bank, Alliance Bank, Affin Bank, AmBank, Bank Rakyat, UOB, Standard Chartered, Boost, e-Wallet.' ),
            'api_key'       => '',
            'signature_key' => '',
            'environment'   => 'sandbox',
            'business_id'   => '',
            'business_name' => '',
        );

        if ( !$settings ) {
            update_option( 'woocommerce_swipego_settings', $defaults );
        }

    }

    // Register admin menu
    public function register_menu() {

        add_submenu_page(
            'swipego',
            __( 'Swipe – WooCommerce Settings', 'swipego-wc' ),
            __( 'WooCommerce', 'swipego-wc' ),
            'manage_options',
            $this->id,
            array( $this, 'view_page' )
        );

    }

    // Get the views of the settings page
    public function view_page() {

        $enabled       = swipego_wc_get_setting( 'enabled' );
        $title         = swipego_wc_get_setting( 'title' );
        $description   = swipego_wc_get_setting( 'description' );
        $api_key       = swipego_wc_get_setting( 'api_key' );
        $signature_key = swipego_wc_get_setting( 'signature_key' );
        $environment   = swipego_wc_get_setting( 'environment' );
        $business_id   = swipego_wc_get_setting( 'business_id' );
        $business_name = swipego_wc_get_setting( 'business_name' );

        $businesses = swipego_wc_get_businesses();
        $current_business = null;

        // Get current business
        if ( $business_id && $businesses ) {
            foreach ( $businesses as $item ) {
                if ( $item['id'] == $business_id ) {
                    $current_business = $item;
                    break;
                }
            }
        }
        
        if ($business_id && $current_business == null) {
            $current_business['name'] = $business_name;
        }

        ob_start();
        require_once( SWIPEGO_WC_PATH . 'admin/views/settings.php' );

        echo ob_get_clean();

    }

    // Enqueue styles & scripts
    public function enqueue_scripts( $hook ) {

        if ( $hook !== 'swipe_page_swipego_wc_settings' ) {
            return;
        }

        wp_enqueue_style( 'swipego-wc-admin', SWIPEGO_WC_URL . 'assets/css/admin.css', array(), SWIPEGO_WC_VERSION, 'all' );
        wp_enqueue_script( 'swipego-wc-admin', SWIPEGO_WC_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-validate', 'sweetalert2' ), SWIPEGO_WC_VERSION, true );

        wp_localize_script( 'swipego-admin', 'swipego_wc_update_settings', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'swipego_wc_update_settings_nonce' ),
        ) );

        wp_localize_script( 'swipego-admin', 'swipego_wc_retrieve_api_credentials', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'swipego_wc_retrieve_api_credentials_nonce' ),
        ) );

        wp_localize_script( 'swipego-admin', 'swipego_wc_set_webhook', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'swipego_wc_set_webhook_nonce' ),
        ) );

    }

    // Update WooCommerce settings
    public function update_settings() {

        check_ajax_referer( 'swipego_wc_update_settings_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'swipego_wc_update_settings_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'swipego' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'swipego' ),
            ), 400 );
        }

        $settings = get_option( 'woocommerce_swipego_settings' );

        // Go through each settings key, then check if there have POST request.
        // If have, update the settings value.
        foreach ( $this->keys as $key ) {
            if ( $key == 'description' ) {
                $value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( $_POST[ $key ] ) : null;
            } else {
                $value = isset( $_POST[ $key ] ) ? sanitize_text_field( $_POST[ $key ] ) : null;
            }

            if ( $value ) {
                $settings[ $key ] = $value;
            }
        }

        update_option( 'woocommerce_swipego_settings', $settings );

        wp_send_json_success();

    }

    // Retrieve API credentials from Swipe
    public function retrieve_api_credentials() {

        check_ajax_referer( 'swipego_wc_retrieve_api_credentials_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'swipego_wc_retrieve_api_credentials_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'swipego' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'swipego' ),
            ), 400 );
        }

        $business_id = swipego_wc_get_setting( 'business_id' );

        if ( !$business_id ) {
            wp_send_json_error( array(
                'message' => __( 'No business selected', 'swipego' ),
            ), 400 );
        }

        $business = swipego_wc_get_business( $business_id );

        if ( !$business ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid business', 'swipego' ),
            ), 400 );
        }

        // Update API credentials into the database ////////////////////////////

        $settings = get_option( 'woocommerce_swipego_settings' );

        if ( isset( $business['integration_id'] ) ) {
            $settings['integration_id'] = $business['integration_id'];
        }

        if ( isset( $business['api_key'] ) ) {
            $settings['api_key'] = $business['api_key'];
        }

        if ( isset( $business['signature_key'] ) ) {
            $settings['signature_key'] = $business['signature_key'];
        }

        update_option( 'woocommerce_swipego_settings', $settings );

        ////////////////////////////////////////////////////////////////////////

        wp_send_json_success( $business );

    }

    // Set WooCommerce webhook URL in Swipe
    public function set_webhook() {

        check_ajax_referer( 'swipego_wc_set_webhook_nonce', 'nonce' );

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

        if ( !wp_verify_nonce( $nonce, 'swipego_wc_set_webhook_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid nonce', 'swipego' ),
            ), 400 );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'No permission to update the settings', 'swipego' ),
            ), 400 );
        }

        $business_id = swipego_wc_get_setting( 'business_id' );
        $integration_id = swipego_wc_get_setting( 'integration_id' );

        if ( !$business_id ) {
            wp_send_json_error( array(
                'message' => __( 'No business selected', 'swipego' ),
            ), 400 );
        }

        if ( !$integration_id ) {
            wp_send_json_error( array(
                'message' => __( 'Missing integration ID for selected business', 'swipego' ),
            ), 400 );
        }

        try {

            $swipego = new Swipego_WC_API();
            $swipego->set_access_token( swipego_get_access_token() );

            // Get all webhooks because we need to delete existing webhook first
            // 1 = payment.created
            list( $code, $response ) = $swipego->get_webhooks( $business_id, $integration_id );

            $webhooks = isset( $response['data']['data'] ) ? $response['data']['data'] : array();

            if ( $webhooks ) {
                foreach ( $webhooks as $webhook ) {
                    if ( !isset( $webhook['_id'] ) ) {
                        continue;
                    }

                    // Delete existing webhook first
                    $swipego->delete_webhook( $business_id, $integration_id, $webhook['_id'], array( 'enabled' => true ) );
                }
            }

            $params = array(
                'name'    => 'payment.created',
                'url'     => WC()->api_request_url( 'swipego_wc_gateway' ),
                'enabled' => true,
            );

            list( $code, $response ) = $swipego->store_webhook( $business_id, $integration_id, $params );

            $errors = isset( $response['errors'] ) ? $response['errors'] : false;

            if ( $errors ) {
                foreach ( $errors as $error ) {
                    throw new Exception( $error[0] );
                }
            }

        } catch ( Exception $e ) {
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ), 400 );
        }

        wp_send_json_success( $business_id );

    }

}
new Swipego_WC_Settings();
