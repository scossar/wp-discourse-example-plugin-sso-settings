<?php

namespace WPDC\PluginExamples\SSO;

class SingleSignOn {

	protected $wpdc_sso_common = array(
		'wpdc-sso-secret' => '',
	);

	protected $wpdc_sso_provider = array(
		'wpdc-enable-sso-provider' => 0,
		'wpdc-login-path'          => '',
	);

	protected $wpdc_sso_client = array(
		'wpdc-enable-sso-client'     => 0,
		'wpdc-sync-client-email'     => 0,
		'wpdc-redirect-client-login' => 0,
	);

	protected $wpdc_option_groups = array(
		'wpdc_sso_common',
		'wpdc_sso_provider',
		'wpdc_sso_client',
	);

	public function __construct() {
		add_action( 'init', array( $this, 'initialize_plugin' ) );
		add_filter( 'discourse/utilities/options-array', array( $this, 'add_options' ) );
	}

	public function initialize_plugin() {
		foreach ( $this->wpdc_option_groups as $group ) {
			add_option( $group, $this->$group );
		}
	}

	public function add_options( $wpdc_options ) {
		static $merged_options = [];

		if ( empty( $merged_options ) ) {
			foreach ( $this->wpdc_option_groups as $group ) {
				$option         = get_option( $group );
				$merged_options = array_merge( $merged_options, $option );
			}

			$merged_options = array_merge( $wpdc_options, $merged_options );
		}

		return $merged_options;
	}

}