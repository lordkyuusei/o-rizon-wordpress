<?php

/*
Plugin Name: Material Admin Theme
Plugin URI:
Description: Clean, eye-pleasing theme
Author: chmln
Version: 1.3.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function material_style() {
    wp_enqueue_style('material-theme', plugins_url('material.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'material_style');
add_action('login_enqueue_scripts', 'material_style');

?>
