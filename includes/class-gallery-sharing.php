<?php

/**
 * The core plugin class.
 */
class Gallery_Sharing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {

		$this->plugin_name = 'gallery-sharing';
		$this->version = '1.0';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gallery-sharing-loader.php';

		/**
		 * Object for gallery sharing options
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gallery-sharing-options.php';

		/**
		 * Gets the renderd html from sources by id
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gallery-sharing-builder.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gallery-sharing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gallery-sharing-public.php';

		$this->loader = new Gallery_Sharing_Loader();

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Gallery_Sharing_Admin( $this->get_plugin_name(), $this->get_version() );

		// settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_page' );

		// tinymce plugin
		$this->loader->add_filter( 'mce_buttons', $plugin_admin, 'add_tinymce_button' );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_admin, 'add_tinymce_plugin' );

		// quick tags button
		$this->loader->add_action( 'admin_print_footer_scripts', $plugin_admin, 'add_text_editor_button' );

		// gallery sharing model html content
		$this->loader->add_action( 'wp_ajax_ph_gallery_sharing_modal', $plugin_admin, 'render_modal' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Gallery_Sharing_Public( $this->get_plugin_name(), $this->get_version() );

		/**
		 * register urls for gallery ajax
		 */
		$this->loader->add_action( 'init', $plugin_public, 'add_endpoint' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );
		$this->loader->add_action( 'parse_request', $plugin_public, 'sniff_requests' );

		/**
		 * adds shortcode support for galleries
		 */
		add_shortcode( 'ph-gallery-sharing', array( $plugin_public, 'shortcode' ) );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
