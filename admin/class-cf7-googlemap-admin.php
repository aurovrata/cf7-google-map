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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

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
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		switch ( true ) {
			case ( 'wpcf7_contact_form' == $screen->post_type && 'post' == $screen->base ):
			case ( 'toplevel_page_wpcf7' == $hook || 'contact_page_wpcf7-new' == $hook ):
				$plugin_dir         = plugin_dir_url( __DIR__ );
				$google_map_api_key = get_option( 'cf7_googleMap_api_key', '' );
				$airplane_mode      = true;
				if ( ! class_exists( 'Airplane_Mode_Core' ) || ! Airplane_Mode_Core::getInstance()->enabled() ) {
					$scheme = 'http';
					if ( is_ssl() ) {
						$scheme = 'https';
					}
					wp_enqueue_script( 'google-maps-api-admin', $scheme . '://maps.google.com/maps/api/js?key=' . $google_map_api_key, array( 'jquery' ), null, true );
					$airplane_mode = false;
				}
				wp_enqueue_script( 'gmap3-admin', $plugin_dir . '/assets/gmap3/gmap3.min.js', array( 'jquery', 'google-maps-api-admin' ), $this->version, true );
				wp_enqueue_script( 'arrive-js', $plugin_dir . '/assets/arrive/arrive.min.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( $this->plugin_name, $plugin_dir . '/admin/js/admin_settings_map.js', array( 'jquery' ), $this->version, true );
				wp_localize_script(
					$this->plugin_name,
					'cf7_map_admin_settings',
					array(
						'theme_dir'  => plugin_dir_url( __DIR__ ),
						'marker_lat' => '12.007089',
						'marker_lng' => '79.810600',
						'map_zoom'   => '3',
						'airplane'   => $airplane_mode,
					)
				);
				break;
		}
	}
	/**
	 * Enqeue CF7 Smart Grid helper script.
	 * Hooked on action 'cf7sg_enqueue_admin_editor_scripts'.
	 *
	 * @since 1.5.1
	 */
	public function enqueue_grid_helper() {
		wp_enqueue_script( 'cf7sg-grid-js', plugin_dir_url( __FILE__ ) . 'js/ui-custom-helper.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			'cf7sg-grid-js',
			'cf7sgHelper',
			array(
				'geocode' => get_option( 'cf7_googleMap_enable_geocode', 0 ),
			)
		);
	}

	/**
	 * Add to the wpcf7 tag generator.
	 * This function registers a callback function with cf7 to display
	 * the tag generator help screen in the form editor. Hooked on 'wpcf7_admin_init'
	 *
	 * @since 1.0.0
	 */
	function add_cf7_tag_generator_googleMap() {
		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add( 'map', __( 'Google map', 'cf7-google-map' ), array( $this, 'googleMap_tag_generator' ) );
		}
	}

	/**
	 * GoogleMap tag help screen.
	 *
	 * This function is called by cf7 plugin, and is registered with a hooked function above
	 *
	 * @since 1.0.0
	 * @param WPCF7_ContactForm $contact_form the cf7 form object
	 * @param array             $args arguments for this form.
	 */
	function googleMap_tag_generator( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		include plugin_dir_path( __FILE__ ) . '/partials/cf7-tag-display.php';
	}
	/**
	 * Register a settings sub-menu
	 * hooked on 'admin_menu'
	 *
	 * @since 1.0.0
	 **/
	public function add_settings_submenu() {
		// create new sub menu
		add_options_page( 'Google Map extension for Contact Form 7', 'CF7 Google Map', 'administrator', 'cf7-googleMap-settings', array( $this, 'show_settings_page' ) );
	}
	/**
	 * Display the settings page
	 * called by the function 'add_options_page'
	 *
	 * @since 1.0.0
	 **/
	public function show_settings_page() {
		/** @since 1.4.3 */
		require_once plugin_dir_path( __FILE__ ) . 'partials/cf7-google-map-settings.php';
	}
	/**
	 * Register settings options
	 * hooked on 'admin_init'
	 *
	 * @since 1.0.0
	 **/
	public function register_settings() {
		// Default API KEY Google Maps
		register_setting( 'cf7-google-map-settings-group', 'cf7_googleMap_api_key', array( $this, 'maps_api_validation' ) );
		register_setting( 'cf7-google-map-settings-group', 'cf7_googleMap_enable_geocode', array( 'type' => 'boolean' ) );
		register_setting( 'cf7-google-map-settings-group', 'cf7_googleMap_enable_places', array( 'type' => 'boolean' ) );
	}
	/**
	 * Validates a google API key
	 *
	 * @since 1.0.0
	 * @param      string $input     API key to check.
	 * @return     string    validated API key   .
	 **/
	public function maps_api_validation( $input ) {

		if ( strlen( $input ) < 20 ) {
			add_settings_error( '', '', __( 'API KEY INVALID', 'cf7-google-map' ), 'error' );
			return '';
		} else {
			return $input;
		}
	}
	/**
	 * Set up email tags
	 * hooked on cf7 filter 'wpcf7_collect_mail_tags'
	 *
	 * @since 1.0.0
	 * @param      Array $mailtags     tag-name.
	 * @return     string    $p2     .
	 **/
	public function email_tags( $mailtags = array() ) {
		$contact_form = WPCF7_ContactForm::get_current();
		$tags         = $contact_form->scan_form_tags();
		foreach ( (array) $tags as $tag ) {
			if ( ! empty( $tag['name'] ) && 'map' == $tag['basetype'] ) {
				$mailtags[] = 'lat-' . $tag['name'];
				$mailtags[] = 'lng-' . $tag['name'];
				if ( get_option( 'cf7_googleMap_enable_geocode', 0 ) ) {
					$mailtags[] = 'address-' . $tag['name'];
				}
				// remove teh default tag.
				if ( false !== ( $key = array_search( $tag['name'], $mailtags ) ) ) {
					unset( $mailtags[ $key ] );
				}
			}
		}
		return $mailtags;
	}
	/**
	 * This function runs when WordPress completes its upgrade process
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * @param $upgrader_object Array
	 * @param $options Array
	 */
	public function upgrade_completed( $upgrader_object, $options ) {
		// If an update has taken place and the updated type is plugins and the plugins element exists
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin != $this->plugin_name ) {
					continue;
				}
				/**
				*@since 1.3.0
				*/
				if ( version_compare( $this->version, '1.3.1', '!=' ) ) {
					return;
				}
				add_option( 'cf7_googleMap_enable_geocode', 1 );
				add_option( 'cf7_googleMap_enable_places', 1 );
				$notices                            = get_option( 'cf7-google-map-notices', array() );
				$nonce                              = wp_create_nonce( 'cf7_gmap_update_notice' );
				$notice                             = array(
					'nonce' => $nonce,
					'type'  => 'uprade-warning',
					'msg'   => sprintf( __( 'Google Maps APIs policy has changed, and now requires API keys to be setup and the required APIs enabled. The settings for this plugin has therefore been updated to reflect these changes.  Please review your <a href="%s">settings</a> and ensure you have the correct APIs enabled for your key.', 'cf7-polylang' ), admin_url( '/options-general.php?page=cf7-googleMap-settings' ) ),
				);
				$notices['admin.php']['page=wpcf7'] = $notice;
				$notices['edit.php']['post_type=wpcf7_contact_form'] = $notice;
				$notices['plugins.php']['any']                       = $notice;
				update_option( 'cf7-google-map-notices', $notices );

			}
		}
	}

	/**
	 * Show a notice to anyone who has just updated this plugin
	 * This notice shouldn't display to anyone who has just installed the plugin for the first time
	 */
	public function admin_notices() {
		global $pagenow;
		// Check the transient to see if we've just updated the plugin
		$notices = get_option( 'cf7-google-map-notices', array() );
		if ( empty( $notices ) ) {
			return;
		}
		if ( ! isset( $notices[ $pagenow ] ) ) {
			return;
		}

		foreach ( $notices[ $pagenow ] as $key => $notice ) {
			switch ( true ) {
				case strpos( $key, 'page=' ) !== false && $_GET['page'] === str_replace( 'page=', '', $key ):
				case strpos( $key, 'post_type=' ) !== false && isset( $_GET['post_type'] ) && $_GET['post_type'] === str_replace( 'post_type=', '', $key ):
				case $key === 'any':
					$dismiss = $notice['nonce'] . '-forever';
					if ( ! PAnD::is_admin_notice_active( $dismiss ) ) {
						unset( $notices[ $pagenow ] );
						update_option( 'cf7-google-map-notices', $notices );
						break;
					}
					?>
				<div data-dismissible="<?php echo $dismiss; ?>" class="updated notice <?php echo $notice['type']; ?> is-dismissible"><p><?php echo $notice['msg']; ?></p></div>
					<?php
					break;
			}
		}
	}
	/**
	 * Add grid helper hooks for individual tags.
	 * Hooked to action 'cf7sg_ui_grid_helper_hooks'.
	 *
	 * @since 3.2.2
	 */
	public function print_helper_hooks() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/cf7-sgui-helper-hooks.php';
	}
	/**
	 * Add grid helper hooks for individual tags.
	 * Hooked to action 'cf7sg_ui_grid_js_helper_hooks'.
	 *
	 * @since 3.2.2
	 */
	public function print_js_helper_hooks() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/cf7-sgui-js-helper-hooks.php';
	}
	/**
	 * Add save draft message to cf7 messages.
	 * Hooked to 'wpcf7_messages', see file contact-form-7/includes/contact-form-template.php fn messages().
	 *
	 * @since 1.7.0
	 * @param array $messages array of messages to filter.
	 * @return array array of cf7 messages.
	 */
	public function required_message( $messages ) {
		$messages['map_required'] = array(
			'description' => __( 'Required maps error message', 'cf7-google-map' ),
			'default'     => __( 'The location is required.', 'cf7-google-map' ),
		);
		return $messages;
	}
}
