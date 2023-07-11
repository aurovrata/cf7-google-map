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
	 * Google map field names associated with this instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $maps    an array of google map field names associated with this instance.
	 */
	protected $maps;

	/**
	 * Local script for configuring maps.
	 *
	 * @since 1.8.0
	 * @access protected
	 * @var array $local_script an array of parameters for each map field.
	 */
	protected $local_script;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->maps         = array();
		$this->local_script = array(
			'plane_mode' => ( class_exists( 'Airplane_Mode_Core' ) && Airplane_Mode_Core::getInstance()->enabled() ),
			'geocode'    => get_option( 'cf7_googleMap_enable_geocode' ) ? '1' : '0',
			'places'     => get_option( 'cf7_googleMap_enable_places' ) ? '1' : '0',
			'fields'     => array(),
		);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$dbg = (defined('WP_DEBUG') && WP_DEBUG) ? '':'.min';
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . "css/cf7-googlemap{$dbg}.css", array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$google_map_api_key = get_option( 'cf7_googleMap_api_key' );
		if ( ! class_exists( 'Airplane_Mode_Core' ) || ! Airplane_Mode_Core::getInstance()->enabled() ) {
			$scheme = 'http';
			if ( is_ssl() ) {
				$scheme = 'https';
			}
			wp_register_script( 'google-maps-api-admin', $scheme . '://maps.google.com/maps/api/js?key=' . $google_map_api_key . '&libraries=places', array( 'jquery' ), null, true );
		}
		wp_register_script( 'gmap3-admin', plugin_dir_url( __DIR__ ) . '/assets/gmap3/gmap3.min.js', array( 'jquery', 'google-maps-api-admin' ), $this->version, true );
		wp_register_script( 'js-resize', plugin_dir_url( __DIR__ ) . '/assets/js-resize/jquery.resize.js', array( 'jquery' ), $this->version, true );
		$dbg = (defined('WP_DEBUG') && WP_DEBUG) ? '':'.min';
		wp_register_script( 'cf7-googlemap', plugin_dir_url( __DIR__ ) . "public/js/cf7-googlemap{$dbg}.js", array( 'gmap3-admin' ), $this->version, true );
	}
	/**
	 * Register a [googleMap] shortcode with CF7.
	 * Hooked  o 'wpcf7_init'
	 * This function registers a callback function to expand the shortcode for the googleMap form fields.
	 *
	 * @since 1.0.0
	 */
	public function add_cf7_shortcode_googleMap() {
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag(
				array( 'map', 'map*' ),
				array( $this, 'googleMap_shortcode_handler' ),
				true // has name.
			);

		}
	}
	/**
	 * Function for googleMap shortcode handler.
	 * This function expands the shortcode into the required hiddend fields
	 * to manage the googleMap forms.  This function is called by cf7 directly, registered above.
	 *
	 * @since 1.0.0
	 * @param strng $tag the tag name designated in the tag help screen.
	 * @return string a set of html fields to capture the googleMap information.
	 */
	public function googleMap_shortcode_handler( $tag ) {
		$plugin_url = plugin_dir_url( __DIR__ );
		// enqueue required scripts and styles.
		wp_enqueue_script( 'google-maps-api-admin' );
		wp_enqueue_script( 'gmap3-admin' );
		wp_enqueue_script( 'js-resize' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( 'cf7-googlemap' );
		$map_type = apply_filters( 'cf7_google_map_default_type', 'ROADMAP', $tag->name );
		$map_type = strtoupper( $map_type );
		switch ( $map_type ) {
			case 'ROADMAP':
			case 'SATELLITE';
			case 'HYBRID':
			case 'TERRAIN':
				break;
			default:
				$map_type = 'ROADMAP';
				break;
		}
		$default_marker  = $plugin_url . 'assets/red-marker.png';
		$marker_settings = array(
			'icon'      => apply_filters( 'cf7_google_map_marker_icon_url_path', $default_marker, $tag->name ),
			'draggable' => true,
		);
		$marker_settings = apply_filters( 'cf7_google_map_marker_settings', $marker_settings, $tag->name );
		switch ( true ) {
			case ! isset( $marker_settings['icon'] ):
			case empty( $marker_settings['icon'] ):
			case $default_marker !== $marker_settings['icon'] && ! @getimagesize( $marker_settings['icon'] ):
				debug_msg( 'unable to locate marker icon url: ' . $marker_settings['icon'] );
				$marker_settings['icon'] = $default_marker;
				break;
		}

		$gmap3_settings                             = array(
			'mapTypeControl'    => true,
			'navigationControl' => true,
			'streetViewControl' => true,
			'zoomControl'       => true,
			'rotateControl'     => false,
			'fullscreenControl' => false,
			'rotateControl'     => false,
		);
		$gmap3_settings                             = apply_filters( 'cf7_google_map_settings', $gmap3_settings, $tag->name );
		$this->local_script['fields'][ $tag->name ] = array(
			'gmap3_settings'  => $gmap3_settings,
			'map_type'        => $map_type,
			'marker_settings' => $marker_settings,
			'init'            => apply_filters( 'cf7_google_map_initialise_on_document_ready', true, $tag->name ),
		);
		wp_localize_script( 'cf7-googlemap', 'cf7GoogleMap', $this->local_script );

		$tag = new WPCF7_FormTag( $tag );
		if ( empty( $tag->name ) ) {
			return '';
		}
		if ( ! in_array( $tag->name, $this->maps ) ) {
			$this->maps[] = $tag->name;
		}
		ob_start();
		include plugin_dir_path( __FILE__ ) . '/partials/cf7-googlemap.php';
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Validate the data
	 * hooked on 'wpcf7_validate_map' and 'wpcf7_validate_map*'
	 *
	 * @since 1.0.0
	 * @param      array $result     filtered results.
	 * @param      array $tag     tag details.
	 * @return     array    $result     results of validation.
	 **/
	public function validate_data( $result, $tag ) {
		// Backward Comp
		if ( WPCF7_VERSION >= '4.6' ) {
			$tag = new WPCF7_FormTag( $tag );
		} else {
			$tag = new WPCF7_Shortcode( $tag );
		}

		$type = $tag->type;

		$name = $tag->name;

		// Get POST Value.
		// $posted_lat = isset( $_POST['lat-'.$name] ) ? (string) $_POST['lat-'.$name] : '';
		// TODO better validation for address checkbox fields.
		if ( $tag->is_required() && isset( $_POST[ $name ] ) && empty( $_POST[ $name ] ) ) {
			$form     = wpcf7_get_current_contact_form();
			$required = __( 'The location is required.', 'cf7-google-map' );
			if ( ! empty( $form ) ) {
				$messages = $form->prop( 'messages' );
				$required = isset( $messages['map_required'] ) ? $messages['map_required'] : $required;
			}
			$result->invalidate( $tag, $required );
		}
		return $result;
	}
	/**
	 * Setup location data
	 * hooked on cf7 filter 'wpcf7_posted_data'
	 *
	 * @since 1.0.0
	 * @param      Array $posted_data     an array of field-name=>value pairs.
	 * @return     Array    filtered $posted_data     .
	 **/
	public function setup_data( $posted_data ) {
		// get the corresponding cf7 form
		if ( ! isset( $_POST['_wpcf7'] ) ) {
			return $posted_data;
		}
		$fid          = sanitize_text_field( $_POST['_wpcf7'] );
		$contact_form = WPCF7_ContactForm::get_instance( $fid );
		$tags         = $contact_form->scan_form_tags();

		foreach ( (array) $tags as $tag ) {
			if ( empty( $tag['name'] ) || 'map' != $tag['type'] ) {
				continue;
			}
			$field = 'lat-' . $tag['name'];
			if ( isset( $_POST[ $field ] ) ) {
				$posted_data[ $field ] = sanitize_text_field( $_POST[ $field ] );
			}
			$field = 'lng-' . $tag['name'];
			if ( isset( $_POST[ $field ] ) ) {
				$posted_data[ $field ] = sanitize_text_field( $_POST[ $field ] );
			}
			if ( get_option( 'cf7_googleMap_enable_geocode', 0 ) ) {
				$field = 'address-' . $tag['name'];
				if ( isset( $_POST[ $field ] ) ) {
					/** @since 1.4.3 fix address*/
					$address = sanitize_text_field( $_POST[ $field ] );
					$address = json_decode( stripslashes( $address ) );
					if ( empty( $address ) ) {
						$address = array();
					}
					if ( ! is_array( $address ) ) {
						$address = array( $address );
					}
					$address_text          = implode( ',' . PHP_EOL, $address );
					$address_text          = apply_filters( 'cf7_google_map_mailtag_address', $address_text, $address, $tag['name'] );
					$posted_data[ $field ] = $address_text;
				}
			}
		}
		return $posted_data;
	}
	/**
	 * Function to filter custom options values added to tagged map fields.
	 * Hooks action 'cf7_2_post_saving_tag_map'
	 *
	 * @since 1.2.0
	 * @param mixed  $submitted_value  submitted value for the field.
	 * @param string $field_name  the field name.
	 * @return mixed value to store for the field.
	 **/
	public function save_map( $submitted_value, $field_name ) {
		if ( isset( $_POST[ 'zoom-' . $field_name ] ) ) {
			$submitted_value = array(
				'zoom' => (int) sanitize_text_field( $_POST[ 'zoom-' . $field_name ] ),
				'clat' => sanitize_text_field( $_POST[ 'clat-' . $field_name ] ) * 1.0,
				'lat'  => sanitize_text_field( $_POST[ 'lat-' . $field_name ] ) * 1.0,
				'clng' => sanitize_text_field( $_POST[ 'clng-' . $field_name ] ) * 1.0,
				'lng'  => sanitize_text_field( $_POST[ 'lng-' . $field_name ] ) * 1.0,
			);
			if ( isset( $_POST[ 'line-' . $field_name ] ) ) {
				$submitted_value['line']    = sanitize_text_field( $_POST[ 'line-' . $field_name ] );
				$submitted_value['city']    = sanitize_text_field( $_POST[ 'city-' . $field_name ] );
				$submitted_value['state']   = sanitize_text_field( $_POST[ 'state-' . $field_name ] );
				$submitted_value['country'] = sanitize_text_field( $_POST[ 'country-' . $field_name ] );
			}
		}
		return $submitted_value;
	}
	/**
	 * Function to build js script for loading values into map field.
	 * Hooked to 'cf7_2_post_field_mapping_tag_map'.
	 *
	 * @since 1.2.0
	 * @param string $script js script.
	 * @param string $field field name.
	 * @param string $form_id form id.
	 * @param string $json_value json value.
	 * @param string $js_form js form object name.
	 * @return string js script.
	 */
	public function load_map( $script, $field, $form_id, $json_value, $js_form ) {
		$sufix   = array( 'zoom', 'clat', 'clng', 'lat', 'lng' );
		$script .= 'if("undefined" != typeof ' . $json_value . '){' . PHP_EOL;
		$script .= '  var $map="";';
		$script .= '  if("undefined" != typeof ' . $json_value . '.zoom){' . PHP_EOL;
		$script .= '    $map=$(\'.wpcf7-form-control-wrap.' . $field . '\', ' . $js_form . ');' . PHP_EOL;
		foreach ( $sufix as $s ) {
			$script .= '    $(\'input[name="' . $s . '-' . $field . '"]\', $map).val(' . $json_value . '.' . $s . ');' . PHP_EOL;
		}
		$script .= '  }' . PHP_EOL;
		$sufix   = array( 'line', 'city', 'state', 'country' );
		$script .= '  if("undefined" != typeof ' . $json_value . '.line){' . PHP_EOL;
		foreach ( $sufix as $s ) {
			$script .= '    $(\'input[name="' . $s . '-' . $field . '"]\', $map).val(' . $json_value . '.' . $s . ');' . PHP_EOL;
		}
		$script .= '  }' . PHP_EOL;
		$script .= '}' . PHP_EOL;
		return $script;
	}
}
