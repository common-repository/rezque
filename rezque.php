<?php
/*
Plugin Name: Rezque Appointments
Plugin URI: https://www.rezque.com
Description: Book appointments from one to many users with custom settings from time zones, buffers, appointment time, start times, and more!
Version: 1.0.3
Author: Boopis Media
Author URI: https://boopis.com/

    Copyright: © 2016 Boopis Media (email : info@boopis.com)
    License: GNU General Public License v3.0
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'REZQUE' ) ) {

    define( 'REZQUE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
    define( 'REZQUE_URL', plugin_dir_url( __FILE__ ) );

    class REZQUE {
        public function __construct() {
            add_action( 'admin_menu', array(&$this, 'add_menu'), 11 );
            add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
        } 

        public function add_menu() { 
            if ( !isset($GLOBALS['admin_page_hooks']['rezque_settings']) ) {
                add_menu_page('Rezque Settings', 'Rezque', 'manage_options', 'rezque_settings', array(&$this, 'plugin_settings_page')); 
            }
        }

        public function plugin_settings_page() { 
            if(!current_user_can('manage_options')) { 
                wp_die(__('You do not have sufficient permissions to access this page.')); 
            }
            include(sprintf("%s/templates/settings.php", dirname(__FILE__))); 
        }

        public function plugin_action_links( $links, $file ) {
            if ( $file == plugin_basename( __FILE__ ) )
                $links[] = '<a href="admin.php?page=rezque_settings">' . __( 'Settings' , 'rezque_settings') . '</a>';

            return $links;
        }       

    }

    function rezque_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
       if ( $args && is_array( $args ) ) {
           extract( $args );
       }

       $located = rezque_locate_template( $template_name, $template_path, $default_path );

       if ( ! file_exists( $located ) ) {
           _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
           return;
       }

       include( $located );

   }

   function rezque_locate_template( $template_name, $template_path = '', $default_path = '' ) {

     // Look within passed path within the theme - this is priority
       $template = locate_template(
           array(
               trailingslashit( $template_path ) . $template_name,
               $template_name
               )
           );

     // Get default template
       if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
           $template = $default_path . $template_name;
       }

     // Return what we found
       return apply_filters( 'rezque_locate_template', $template, $template_name, $template_path );
   }

    function rezque_handler ( $atts ) {
        rezque_get_template( 'rezque.php', 
            array( 'calendar_id' => $atts["id"],
                   'home_label' => isset($atts["home_label"]) ? $atts["home_label"] : null ,
                   'office_label' => isset($atts["office_label"]) ? $atts["office_label"] : null,
                 ), '', REZQUE_PATH . '/templates/' 
        );
    }

    // finally instantiate our plugin class and add it to the set of globals
    $GLOBALS['rezque'] = new REZQUE();
}

add_shortcode( 'rezque', 'rezque_handler' );
add_filter('widget_text', 'do_shortcode');
