<?php
/**
 * Fired during plugin activation
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 */

/**
 * Fired during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Cf7_GoogleMap_Activator {

	/**
	 * Pluging activation check.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// check if the dependent plugins are active.
		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			exit( 'This plugin requires the Contact Form 7 plugin to be installed first' );
		}
		/** @since 1.3.0 */
		add_option( 'cf7_googleMap_enable_geocode', 0 );
		add_option( 'cf7_googleMap_enable_places', 0 );
		$notices                            = get_option( 'cf7-google-map-notices', array() );
		$nonce                              = wp_create_nonce( 'cf7_gmap_install_notice' );
		$notice                             = array(
			'nonce' => $nonce,
			'type'  => 'install-info', // translators: the comment links to the plugin 'settings' page.
			'msg'   => sprintf( __( 'Google Maps APIs policy has changed, and now requires API keys to be setup and the required APIs enabled. Before you start using maps in your forms, please review your <a href="%s">settings</a> and ensure you have a key and the correct APIs enabled for it.', 'cf7-polylang' ), admin_url( '/options-general.php?page=cf7-googleMap-settings' ) ),
		);
		$notices['admin.php']['page=wpcf7'] = $notice;
		$notices['edit.php']['post_type=wpcf7_contact_form'] = $notice;
		$notices['plugins.php']['any']                       = $notice;
		update_option( 'cf7-google-map-notices', $notices );
	}

}
