<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Update {
    
    function __construct() {
		
		add_action( 'admin_init', array( $this, 'maybe_update_plugin' ) );
        add_action( 'admin_init', array( $this, 'maybe_show_message' ) );

	}


    /**
     * Update routine - runs if the plugin version has changed
     */
    public function maybe_update_plugin() {

        if( CF7MD_VER !== get_option('cf7md_options[plugin_ver]') ) {
            
            // Update the version stored in options
            update_option('cf7md_options[plugin_ver]', CF7MD_VER );

            // Anything else that needs to happen on update

        }

    }


    /**
     * Update routine - runs if the plugin update message has changed
     */
    public function maybe_show_message() {

        if( CF7MD_UPDATE_MESSAGE !== get_option('cf7md_options[plugin_update_message]') ) {
            
            // Update the version stored in options
            update_option('cf7md_options[plugin_update_message]', CF7MD_UPDATE_MESSAGE );

        }

    }
    
    
}

$cf7_material_design_update = new CF7_Material_Design_Update();