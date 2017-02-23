<?php
/**
 * Plugin Name: WP Discourse Single Sign On
 * Version: 0.1
 * Author: scossar
 */

namespace WPDC\PluginExamples\SSO;

use \WPDiscourse\Admin\OptionsPage as OptionsPage;
use \WPDiscourse\Admin\OptionInput as InputHelper;


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
		}
	}
}