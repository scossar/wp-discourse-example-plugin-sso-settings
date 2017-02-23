<?php

namespace WPDC\PluginExamples\SSO\Admin;

use WPDiscourse\Utilities\Utilities as DiscourseUtilities;

class Options {
	protected $options;
	protected $options_page;
	protected $input_helper;

	public function __construct( $options_page, $input_helper ) {
		$this->options_page = $options_page;
		$this->input_helper = $input_helper;

		add_action( 'admin_init', array( $this, 'plugin_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_sso_submenu_page' ) );
		add_action( 'discourse/admin/options-page/append-settings-tabs', array(
			$this,
			'top_level_settings_tab'
		), 10, 2 );
		add_action( 'discourse/admin/options-page/after-settings-tabs', array(
			$this,
			'second_level_tabbed_menu'
		), 10, 2 );
		add_action( 'discourse/admin/options-page/after-tab-switch', array( $this, 'sso_settings_fields' ) );
	}

	public function plugin_settings() {
		$this->options = DiscourseUtilities::get_options();

		add_settings_section( 'wpdc_common_sso_settings_section', __( 'Common Settings', 'wpdc' ), array(
			$this,
			'common_settings_details',
		), 'wpdc_sso_common' );

		add_settings_field( 'wpdc_sso_secret', __( 'SSO Secret Key', 'wpdc' ), array(
			$this,
			'sso_secret_input',
		), 'wpdc_sso_common', 'wpdc_common_sso_settings_section' );

		register_setting( 'wpdc_sso_common', 'wpdc_sso_common', array( $this, 'validate_options' ) );

		add_settings_section( 'wpdc_sso_provider_section', __( 'SSO Provider Settings', 'wpdc' ), array(
			$this,
			'sso_provider_settings_details',
		), 'wpdc_sso_provider' );

		add_settings_field( 'wpdc_sso_provider_enabled', __( 'Enable SSO Provider', 'wpdc' ), array(
			$this,
			'provider_enabled_checkbox',
		), 'wpdc_sso_provider', 'wpdc_sso_provider_section' );

		add_settings_field( 'wpdc_sso_login_path', __( 'Login Path', 'wpdc' ), array(
			$this,
			'login_path_text_input',
		), 'wpdc_sso_provider', 'wpdc_sso_provider_section' );

		register_setting( 'wpdc_sso_provider', 'wpdc_sso_provider', array( $this, 'validate_options' ) );
	}

	public function add_sso_submenu_page() {
		add_submenu_page(
			'wp_discourse_options',
			__( 'Single Sign On', 'wpdc' ),
			__( 'Single Sign On', 'wpdc' ),
			'manage_options',
			'wpdc_sso_options',
			array( $this, 'sso_options_tab' )
		);
	}

	public function sso_options_tab() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->options_page->options_pages_display( 'wpdc_sso_options' );
		}
	}

	public function top_level_settings_tab( $tab, $parent ) {
		$active = 'wpdc_sso_options' === $tab || 'wpdc_sso_options' === $parent;
		?>
        <a href="?page=wp_discourse_options&tab=wpdc_sso_options"
           class="nav-tab <?php echo $active ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Single Sign On', 'wpdc' ); ?>
        </a>
		<?php
	}

	public function second_level_tabbed_menu( $tab, $parent_tab ) {
		if ( 'wpdc_sso_options' === $tab || 'wpdc_sso_options' === $parent_tab ) {
			?>
            <h3 class="nav-tab-wrapper">
                <a href="?page=wp_discourse_options&tab=sso_common&parent_tab=wpdc_sso_options"
                   class="nav-tab <?php echo 'sso_common' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Common Options', 'wpdc' ); ?>
                </a>
                <a href="?page=wp_discourse_options&tab=sso_client&parent_tab=wpdc_sso_options"
                   class="nav-tab <?php echo 'sso_client' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'SSO Client', 'wpdc' ); ?>
                </a>
                <a href="?page=wp_discourse_options&tab=sso_provider&parent_tab=wpdc_sso_options"
                   class="nav-tab <?php echo 'sso_provider' === $tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'SSO Provider', 'wpdc' ); ?>
                </a>
            </h3>
			<?php
		}
	}

	// SSO Common
	public function sso_secret_input() {
		$this->input_helper->text_input( 'wpdc-sso-secret', 'wpdc_sso_common', __( 'SSO Secret', 'wpdc' ) );
	}

	public function provider_enabled_checkbox() {
		$this->input_helper->checkbox_input( 'wpdc-enable-sso-provider', 'wpdc_sso_provider', __( 'Enable WordPress to function as the SSO provider for Discourse', 'wpdc' ) );
	}

	// SSO Provider
	public function login_path_text_input() {
		$this->input_helper->text_input( 'wpdc-login-path', 'wpdc_sso_provider', __( 'The path to your WordPress login page.', 'wpdc' ) );
	}

	public function sso_settings_fields( $tab ) {
		if ( 'sso_common' === $tab ) {
			settings_fields( 'wpdc_sso_common' );
			do_settings_sections( 'wpdc_sso_common' );
		}

		if ( 'sso_provider' === $tab ) {
			settings_fields( 'wpdc_sso_provider' );
			do_settings_sections( 'wpdc_sso_provider' );
		}
	}

	public function common_settings_details() {
		?>
        <p>The SSO secret key is shared between your WordPress site and your Discourse forum.</p>
		<?php
	}

	public function sso_provider_settings_details() {
		?>
        <p>Use your WordPress site as the SSO provider for your forum.</p>
		<?php
	}

	public function validate_options( $inputs ) {
		$output = [];
		foreach ( $inputs as $key => $value ) {
			$output[ $key ] = $value;
		}

		return $output;
	}

}