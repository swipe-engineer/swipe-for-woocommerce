<?php
if ( !defined( 'ABSPATH' ) ) exit;

class Swipego_WC {

    // Load dependencies
    public function __construct() {

        // Libraries
        require_once( SWIPEGO_WC_PATH . 'libraries/swipego/class-swipego.php' );
        
        if ( swipego_is_plugin_activated( 'woocommerce/woocommerce.php' ) ) {
            
            // Functions
            require_once( SWIPEGO_WC_PATH . 'includes/functions.php' );
    
            // Admin
            require_once( SWIPEGO_WC_PATH . 'admin/class-swipego-wc-admin.php' );

            // API
            require_once( SWIPEGO_WC_PATH . 'libraries/swipego/includes/abstracts/abstract-swipego-client.php' );
            require_once( SWIPEGO_WC_PATH . 'libraries/swipego/includes/class-swipego-api.php' );
            require_once( SWIPEGO_WC_PATH . 'includes/class-swipego-wc-api.php' );

            if ( swipego_is_logged_in() || swipego_get_integration() ) {
                
                // Initialize payment gateway
                require_once( SWIPEGO_WC_PATH . 'includes/class-swipego-wc-init.php' );
                
            }
            
            if ( swipego_is_logged_in() ) {
                
                // Settings
                require_once( SWIPEGO_WC_PATH . 'admin/class-swipego-wc-settings.php' );
                
            }
        }

        

    }

}
new Swipego_WC();
