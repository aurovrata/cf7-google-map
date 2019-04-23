<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/public
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Cf7_GoogleMap_Public {

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
   * google map field names associated with this instance.
   *
   * @since    1.0.0
   * @access   protected
   * @var      array    $maps    an array of google map field names associated with this instance.
   */
  protected $maps;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
    $this->maps=array();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-googlemap.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

     $google_map_api_key = get_option('cf7_googleMap_api_key');
     //  AIzaSyBAuTD7ld6g6nEKfrb-AdEh6eq5MLQ1g-E
     if(! class_exists( 'Airplane_Mode_Core' ) || !Airplane_Mode_Core::getInstance()->enabled()){
       $scheme = 'http';
       if(is_ssl()) $scheme = 'https';
       wp_register_script( 'google-maps-api-admin', $scheme.'://maps.google.com/maps/api/js?key=' . $google_map_api_key . '&libraries=places', array( 'jquery' ), null, true );
     }
     wp_register_script( 'gmap3-admin', plugin_dir_url( __DIR__ ) . '/assets/gmap3/gmap3.min.js', array( 'jquery', 'google-maps-api-admin'), $this->version, true );
     wp_register_script( 'js-resize', plugin_dir_url( __DIR__ ) . '/assets/js-resize/jquery.resize.js', array( 'jquery'), $this->version, true );
  }
  /**
	 * Register a [googleMap] shortcode with CF7.
	 * Hooked  o 'wpcf7_init'
	 * This function registers a callback function to expand the shortcode for the googleMap form fields.
	 * @since 1.0.0
	 */
	public function add_cf7_shortcode_googleMap() {
    if( function_exists('wpcf7_add_form_tag') ) {
      wpcf7_add_form_tag(
        array( 'map', 'map*' ),
        array($this,'googleMap_shortcode_handler'),
        true //has name
      );

    }
	}
  /**
	 * Function for googleMap shortcode handler.
	 * This function expands the shortcode into the required hiddend fields
	 * to manage the googleMap forms.  This function is called by cf7 directly, registered above.
	 *
	 * @since 1.0.0
	 * @param strng $tag the tag name designated in the tag help screen
	 * @return string a set of html fields to capture the googleMap information
	 */
	public function googleMap_shortcode_handler( $tag ) {
      //enqueue required scripts and styles
      wp_enqueue_script( 'google-maps-api-admin');
      wp_enqueue_script( 'gmap3-admin');
      wp_enqueue_script( 'js-resize');
      wp_enqueue_style( 'dashicons' );
      wp_enqueue_style( $this->plugin_name);

	    $tag = new WPCF7_FormTag( $tag );
      if ( empty( $tag->name ) ) {
    		return '';
    	}
      if(!in_array($tag->name, $this->maps)){
        $this->maps[] = $tag->name;
      }
      $plugin_url = plugin_dir_url( __DIR__ );
      ob_start();
	    include( plugin_dir_path( __FILE__ ) . '/partials/cf7-googlemap.php');
      $html = ob_get_contents ();
      ob_end_clean();
	    return $html;
	}

  /**
   * validate the data
   * hooked on 'wpcf7_validate_map' and 'wpcf7_validate_map*'
   * @since 1.0.0
   * @param      array    $result     filtered results.
   * @param      array    $tag     tag details.
   * @return     array    $result     results of validation.
  **/
  public function validate_data($result, $tag ) {
    // Backward Comp
    if(WPCF7_VERSION >= '4.6') {
        $tag = new WPCF7_FormTag($tag);
    }else{
        $tag = new WPCF7_Shortcode($tag);
    }

    $type = $tag->type;

    $name = $tag->name;

    // Get POST Value
    //$posted_lat = isset( $_POST['lat-'.$name] ) ? (string) $_POST['lat-'.$name] : '';
    /*TODO better validation for address checkbox fields*/
    if ($tag->is_required() && isset( $_POST[$name] ) && empty($_POST[$name]) ) {
        $result->invalidate( $tag, __('Please select a location on the map','cf7-google-map') );
    }
    return $result;
  }
  /**
   *Setup location data
   * hooked on cf7 filter 'wpcf7_posted_data'
   * @since 1.0.0
   * @param      Array    $posted_data     an array of field-name=>value pairs.
   * @return     Array    filtered $posted_data     .
  **/
  public function setup_data($posted_data){
    //get the corresponding cf7 form
    if( !isset($_POST['_wpcf7'])){
      return $posted_data;
    }
    $contact_form = WPCF7_ContactForm::get_instance($_POST['_wpcf7']);
    $tags = $contact_form->scan_form_tags();

		foreach ( (array) $tags as $tag ) {
			if ( empty( $tag['name'] ) || 'map' != $tag['type'] ) {
				continue;
			}
      $field = 'lat-'.$tag['name'];
      if(isset($_POST[$field])){
        $posted_data[$field] = $_POST[$field];
      }
      $field = 'lng-'.$tag['name'];
      if(isset($_POST[$field])){
        $posted_data[$field] = $_POST[$field];
      }
    }
    return $posted_data;
  }
  /**
   * Function to filter custom options values added to tagged map fields.
   * Hooks action 'cf7_2_post_saving_tag_map'
   * @since 1.2.0
   * @param mixed $submitted_value  submitted value for the field
   * @param string $field_name  the field name
   * @return mixed value to store for the field.
  **/
  public function save_map($submitted_value, $field_name){
    if( isset($_POST['zoom-'.$field_name]) ){
      $submitted_value = array(
        'zoom'=>(int) $_POST['zoom-'.$field_name],
        'clat'=>$_POST['clat-'.$field_name]*1.0,
        'lat'=>$_POST['lat-'.$field_name]*1.0,
        'clng'=>$_POST['clng-'.$field_name]*1.0,
        'lng'=>$_POST['lng-'.$field_name]*1.0
      );
      if(isset($_POST['line-'.$field_name])) {
        $submitted_value['line'] = sanitize_text_field($_POST['line-'.$field_name]);
        $submitted_value['city']= sanitize_text_field($_POST['city-'.$field_name]);
        $submitted_value['state']= sanitize_text_field($_POST['state-'.$field_name]);
        $submitted_value['country']= sanitize_text_field($_POST['country-'.$field_name]);
      }
    }
    return $submitted_value;
  }
  /**
  * Function to build js script for loading values into map field.
  * Hooked to 'cf7_2_post_field_mapping_tag_map'.
  *
  *@since 1.2.0
  *@param string $script text_description
  *@param string $field text_description
  *@param string $form_id text_description
  *@param string $json_value text_description
  *@param string $js_form text_description
  *@return string js script.
  */
  public function load_map($script, $field, $form_id, $json_value, $js_form){
    $sufix = array("zoom", "clat", "clng", "lat", "lng");
    $script .= 'if("undefined" != typeof '.$json_value.'){' . PHP_EOL;
    $script .= '  var $map="";';
    $script .= '  if("undefined" != typeof '.$json_value.'.zoom){' . PHP_EOL;
    $script .= '    $map=$(\'.wpcf7-form-control-wrap.' . $field . '\', '.$js_form.');'. PHP_EOL;
    foreach($sufix as $s){
      $script .= '    $(\'input[name="'. $s .'-'. $field .'"]\', $map).val('. $json_value .'.'. $s .');' . PHP_EOL;
    }
    $script .= "  }". PHP_EOL;
    $sufix = array("line", "city", "state", "country");
    $script .= '  if("undefined" != typeof '.$json_value.'.line){' . PHP_EOL;
    foreach($sufix as $s){
      $script .= '    $(\'input[name="'. $s .'-'. $field .'"]\', $map).val('. $json_value .'.'. $s .');' . PHP_EOL;
    }
    $script .= "  }". PHP_EOL;
    $script .= "}". PHP_EOL;
    return $script;
  }
}
