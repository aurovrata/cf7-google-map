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

     $plugin_dir = plugin_dir_url( __DIR__ );
     //TODO: setup google map api in settings section of general page
     $google_map_api_key = get_option('cf7_googleMap_api_key');
  	wp_enqueue_script( 'google-maps-api-admin', 'http://maps.google.com/maps/api/js?key='.$google_map_api_key, array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'gmap3-admin', $plugin_dir . '/assets/gmap3/gmap3.min.js', array( 'jquery', 'google-maps-api-admin' ), $this->version, true );
  	wp_enqueue_script( 'arrive-js', $plugin_dir . '/assets/arrive/arrive.min.js', array( 'jquery' ), $this->version, true );
  	wp_enqueue_script( $this->plugin_name, $plugin_dir . '/admin/js/admin_settings_map.js', array( 'jquery' ), $this->version, true );
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
		include( plugin_dir_path( __FILE__ ) . '/partials/cf7-tag-display.php');
	}
  /**
   * Register a settings sub-menu
   * hooked on 'admin_menu'
   * @since 1.0.0
  **/
  public function add_settings_submenu(){
    //create new sub menu
    add_options_page('Google Map extension for Contact Form 7', 'CF7 Google Map', 'administrator','cf7-googleMap-settings', array($this,'show_settings_page') );
  }
  /**
   * Display the settings page
   * called by the function 'add_options_page'
   * @since 1.0.0
   *
  **/
  public function show_settings_page(){
    ?>
    <div class="wrap">
      <form method="post" action="options.php">
        <?php settings_fields( 'cf7-google-map-settings-group' ); ?>
        <?php do_settings_sections( 'cf7-google-map-settings-group' ); ?>
        <h2>Contact form 7 Google Map Extension Settings</h2>

        <label for="cf7_googleMap_api_key">Get an API Key from <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">Google</a></label><br />
        <input type="text" name="cf7_googleMap_api_key" value="<?php echo esc_attr( get_option('cf7_googleMap_api_key') ); ?>" />
        <style>input[name="cf7_googleMap_api_key"]{width:100%; max-width:350px;}</style>
        <?php submit_button();?>
      </form>
    </div>
    <?php
  }
  /**
   * Register settings options
   * hooked on 'admin_init'
   * @since 1.0.0
   *
  **/
  public function register_settings(){
    // Default API KEY Google Maps
    register_setting( 'cf7-google-map-settings-group', 'cf7_googleMap_api_key', array($this,'maps_api_validation') );
  }
  /**
   * Validates a google API key
   *
   * @since 1.0.0
   * @param      string    $input     API key to check.
   * @return     string    validated API key   .
  **/
  public function maps_api_validation($input){

      if (strlen($input) < 20 ){
          add_settings_error( '', '', __('API KEY INVALID','cf7-google-map'), 'error' );
          return '';
      }else{
          return $input;
      }
  }
  /**
   * Set up email tags
   * hooked on cf7 filter 'wpcf7_collect_mail_tags'
   * @since 1.0.0
   * @param      Array    $mailtags     tag-name.
   * @return     string    $p2     .
  **/
  public function email_tags( $mailtags = array() ) {
    $contact_form = WPCF7_ContactForm::get_current();
    $tags = $contact_form->scan_form_tags();

		foreach ( (array) $tags as $tag ) {
			if ( !empty($tag['name']) && 'map' == $tag['basetype'] ) {
        $mailtags[] = 'lng-'.$tag['name'];
        $mailtags[] = 'lat-'.$tag['name'];
        if( false !== ($key = array_search($tag['name'], $mailtags)) ){
          unset($mailtags[$key]);
        }
      }
    }

    return $mailtags;
  }
}
