<?php

/**
 * Fired during plugin activation
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Cf7_GoogleMap_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		//check if the dependent plugins are active
    if(!is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )){
      exit('This plugin requires the Contact Form 7 plugin to be installed first');
    }
	}

}
