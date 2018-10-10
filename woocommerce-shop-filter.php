<?php
/*
Plugin Name: WooCommerce Shop Filter
Plugin URI: https://www.pickplugins.com/item/woocommerce-shop-filter
Description: woocommerce-shop-filter
Version: 1.0.0
Author: PickPlugins
Author URI: http://pickplugins.com
Text Domain: woocommerce-shop-filter
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WCShopFilter{
	
	public function __construct(){

		define('wcShopFilter_plugin_url', plugins_url('/', __FILE__)  );
		define('wcShopFilter_plugin_dir', plugin_dir_path( __FILE__ ) );

        require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php');


		add_action( 'wp_enqueue_scripts', array( $this, 'wcShopFilter_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wcShopFilter_admin_scripts' ) );
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));


		add_filter('widget_text', 'do_shortcode');
	}
	

	
	public function load_textdomain() {


        $locale = apply_filters( 'plugin_locale', get_locale(), 'wc-shop-filter' );
        load_textdomain('wc-shop-filter', WP_LANG_DIR .'/wc-shop-filter/wc-shop-filter-'. $locale .'.mo' );

        load_plugin_textdomain( 'wc-shop-filter', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

		}
	
	
	public function wcShopFilter_install(){
		
		do_action( 'wcShopFilter_action_install' );
		
		}		
		
	public function wcShopFilter_uninstall(){
		
		do_action( 'wcShopFilter_action_uninstall' );
		}		
		
	public function wcShopFilter_deactivation(){
		
		do_action( 'wcShopFilter_action_deactivation' );
		}
	
	
	public function wcShopFilter_front_scripts(){


		wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_style('wcShopFilter', plugins_url( 'assets/frontend/css/style.css', __FILE__ ));
        wp_enqueue_style('jquery-ui', plugins_url( 'assets/global/css/jquery-ui.css', __FILE__ ));


		}

	public function wcShopFilter_admin_scripts(){
		


		}

	}

new WCShopFilter();
