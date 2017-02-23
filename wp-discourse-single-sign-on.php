<?php
/**
 * Plugin Name: WP Discourse Single Sign On
 * Version: 0.1
 * Author: scossar
 */

namespace WPDC\PluginExamples\SSO;

use \WPDiscourse\Admin\OptionsPage as OptionsPage;
use \WPDiscourse\Admin\OptionInput as InputHelper;

define( 'WPDC_SSO_URL', plugins_url( '/wp-discourse-single-sign-on' ) );


add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );
function init() {
	if ( class_exists( '\WPDiscourse\Discourse\Discourse' ) ) {
		require_once( __DIR__ . '/lib/single-sign-on.php' );

		new SingleSignOn();

		if ( is_admin() ) {
			require_once( __DIR__ . '/admin/admin.php' );

			$options_page = OptionsPage::get_instance();
			$input_helper = InputHelper::get_instance();

			new Admin\Options( $options_page, $input_helper );

			add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_scripts' );
		}
	}
}

function admin_scripts() {
	wp_register_style( 'wpdc_sso_admin',WPDC_SSO_URL . '/admin/css/wpdc-admin-styles.css' );
	write_log('WPDISCOURSE_URL', WPDC_SSO_URL );
	wp_enqueue_style( 'wpdc_sso_admin' );
}