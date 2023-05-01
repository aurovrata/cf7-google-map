<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/includes
 * @author     Aurovrata V. <vrata@syllogic.in>
 */
class Cf7_GoogleMap {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cf7_GoogleMap_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @param String $version plugin version number.
	 */
	public function __construct( $version ) {

		$this->plugin_name = 'cf7-googlemap';
		$this->version     = $version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cf7_GoogleMap_Loader. Orchestrates the hooks of the plugin.
	 * - Cf7_GoogleMap_i18n. Defines internationalization functionality.
	 * - Cf7_GoogleMap_Admin. Defines all hooks for the admin area.
	 * - Cf7_GoogleMap_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-googlemap-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wordpress-gurus-debug-api.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cf7-googlemap-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cf7-googlemap-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cf7-googlemap-public.php';

		/**
		* Persist admin notices:0

		* @since 1.3.0
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/assets/persist-admin-notices/persist-admin-notices-dismissal.php';

		$this->loader = new Cf7_GoogleMap_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cf7_GoogleMap_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cf7_GoogleMap_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cf7_GoogleMap_Admin( $this->get_plugin_name(), $this->get_version() );
		/* WP Hooks */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_submenu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		/** @since 1.3.0 update and notices */
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'upgrade_completed', 10, 2 );

		// CF7 Hooks.
		$this->loader->add_action( 'wpcf7_admin_init', $plugin_admin, 'add_cf7_tag_generator_googleMap' );
		$this->loader->add_filter( 'wpcf7_collect_mail_tags', $plugin_admin, 'email_tags' );
		/** @since 1.3.1 for displaying admin notices */
		$this->loader->add_action( 'admin_init', 'PAnD', 'init' );
		/** @since 1.5.1 add CF7 Smart Grid helper hooks */
		$this->loader->add_action( 'cf7sg_ui_grid_helper_hooks', $plugin_admin, 'print_helper_hooks' );
		$this->loader->add_action( 'cf7sg_ui_grid_js_helper_hooks', $plugin_admin, 'print_js_helper_hooks' );
		$this->loader->add_action( 'cf7sg_enqueue_admin_editor_scripts', $plugin_admin, 'enqueue_grid_helper' );
		/** @since 1.7.0 include required message in admin messages */
		$this->loader->add_filter( 'wpcf7_messages', $plugin_admin, 'required_message' );
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cf7_GoogleMap_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		// CF7 Hooks.
		// handles the display of the google map on front-end forms.
		$this->loader->add_action( 'wpcf7_init', $plugin_public, 'add_cf7_shortcode_googleMap' );
		// validate the data.
		$this->loader->add_filter( 'wpcf7_validate_map', $plugin_public, 'validate_data', 10, 2 );
		$this->loader->add_filter( 'wpcf7_validate_map*', $plugin_public, 'validate_data', 10, 2 );
		$this->loader->add_filter( 'wpcf7_posted_data', $plugin_public, 'setup_data', 5, 1 );
		// mail tag.
		// $this->loader->add_filter('wpcf7_mail_tag_replaced', $plugin_public, 'setup_mailtag',10,3 );
		// cf72post hooks.
		// save submission to db.
		$this->loader->add_action( 'cf7_2_post_saving_tag_map', $plugin_public, 'save_map', 10, 2 );
		// echo script for mapping values to form.
		$this->loader->add_filter( 'cf7_2_post_field_mapping_tag_map', $plugin_public, 'load_map', 10, 5 );

	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cf7_GoogleMap_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
