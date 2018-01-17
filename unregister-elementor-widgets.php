<?php
/**
 * Plugin Name: Unregister Elementor Widgets
 * Description: Helps you unregister widgets for non administrators of your site.
 * Plugin URI: https://designsbynore.com/
 * Author: Zulfikar Nore
 * Version: 1.0.0
 * Author URI: https://designsbynore.com/
 *
 * Text Domain: elementor-quick-start
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'UNREGISTER_ELEMENTOR_WIDGETS_VERSION', '1.0.0' );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_PREVIOUS_STABLE_VERSION', '1.0.0' );

define( 'UNREGISTER_ELEMENTOR_WIDGETS__FILE__', __FILE__ );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_PLUGIN_BASE', plugin_basename( UNREGISTER_ELEMENTOR_WIDGETS__FILE__ ) );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_PATH', plugin_dir_path( UNREGISTER_ELEMENTOR_WIDGETS__FILE__ ) );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_MODULES_PATH', UNREGISTER_ELEMENTOR_WIDGETS_PATH . 'modules/' );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_URL', plugins_url( '/', UNREGISTER_ELEMENTOR_WIDGETS__FILE__ ) );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_ASSETS_URL', UNREGISTER_ELEMENTOR_WIDGETS_URL . 'assets/' );
define( 'UNREGISTER_ELEMENTOR_WIDGETS_MODULES_URL', UNREGISTER_ELEMENTOR_WIDGETS_URL . 'modules/' );

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function unregister_elementor_widgets_load_plugin() {
	load_plugin_textdomain( 'unregister-elementor' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'unregister_elementor_widgets_fail_load' );
		return;
	}

	$elementor_version_required = '1.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'unregister_elementor_widgets_fail_load_out_of_date' );
		return;
	}

	$elementor_version_recommendation = '1.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_recommendation, '>=' ) ) {
		add_action( 'admin_notices', 'unregister_elementor_widgets_admin_notice_upgrade_recommendation' );
	}

	require( UNREGISTER_ELEMENTOR_WIDGETS_PATH . 'plugin.php' );
}
add_action( 'plugins_loaded', 'unregister_elementor_widgets_load_plugin' );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function unregister_elementor_widgets_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<p>' . __( 'Unregister Elementor Widgets not working because you need to activate the Elementor plugin.', 'unregister-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'unregister-elementor' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<p>' . __( 'Unregister Elementor Widgets not working because you need to install the Elementor plugin', 'unregister-elementor' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'unregister-elementor' ) ) . '</p>';
	}

	echo '<div class="error"><p>' . $message . '</p></div>';
}

function unregister_elementor_widgets_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Unregister Elementor Widgets not working because you are using an old version of Elementor.', 'unregister-elementor' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'unregister-elementor' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

function unregister_elementor_widgets_admin_notice_upgrade_recommendation() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'A new version of Elementor is available. For better performance and compatibility of Unregister Elementor Widgets, we recommend updating to the latest version.', 'unregister-elementor' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'unregister-elementor' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}
