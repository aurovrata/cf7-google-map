<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/admin
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Cf7_GoogleMap_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_GoogleMap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_GoogleMap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
     $plugin_dir = plugin_dir_url( __FILE__ );
		wp_enqueue_style( $this->plugin_name, $plugin_dir . 'css/cf7-googleMap-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_GoogleMap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_GoogleMap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
     $plugin_dir = plugin_dir_url( __FILE__ );
     $google_map_api_key = 'AIzaSyBAuTD7ld6g6nEKfrb-AdEh6eq5MLQ1g-E';
  	wp_enqueue_script( 'google-maps-api-admin', 'http://maps.google.com/maps/api/js?key='.$google_map_api_key, array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'gmap3-admin', $plugin_dir . '/js/gmap3.min.js', array( 'jquery' ), $this->version, true );
  	wp_enqueue_script( 'arrive-js', $plugin_dir . '/js/arrive.min.js', array( 'jquery' ), $this->version, true );
  	wp_enqueue_script( $this->plugin_name, $plugin_dir . '/js/admin_settings_map.js', array( 'jquery' ), $this->version, true );
    wp_localize_script( $this->plugin_name, 'cf7_map_admin_settings', array(
  		'theme_dir' 			=> plugin_dir_url( __DIR__ ),
      'marker_lat'   => '12.007089',
      'marker_lng'   => '79.810600',
      'map_zoom'         => '3',
  	) );

	}


	/**
	 * Add to the wpcf7 tag generator.
	 * This function registers a callback function with cf7 to display
	 * the tag generator help screen in the form editor. Hooked on 'wpcf7_admin_init'
	 * @since 1.0.0
	 */
	function add_cf7_tag_generator_googleMap() {
	    if ( class_exists( 'WPCF7_TagGenerator' ) ) {
	        $tag_generator = WPCF7_TagGenerator::get_instance();
	        $tag_generator->add( 'map', __( 'Google map', 'cf7-google-map' ), array($this,'googleMap_tag_generator') );
	    }
	}
	
	/**
	 * GoogleMap tag help screen.
	 *
	 * This function is called by cf7 plugin, and is registered with a hooked function above
	 *
	 * @since 1.0.0
	 * @param WPCF7_ContactForm $contact_form the cf7 form object
	 * @param array $args arguments for this form.
	 */
	function googleMap_tag_generator( $contact_form, $args = '' ) {
    $args = wp_parse_args( $args, array() );
  	$type = 'date';
		include( plugin_dir_path( __FILE__ ) . '/partials/cf7-tag-display.php');
	}


}
