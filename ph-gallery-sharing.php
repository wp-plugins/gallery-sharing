<?php
/**
 *	Gallery Sharing between WordPress instances. 
 *	
 *
 *
 * @wordpress-plugin
 * Plugin Name:       PALASTHOTEL Gallery Sharing
 * Description:       This Plugin provides a way to share Galleries between WordPress instances
 * Version:           1.0
 * Author:            PALASTHOTEL by Edward Bock
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gallery-sharing-activator.php';

/** This action is documented in includes/class-ph-octavius-activator.php */
register_activation_hook( __FILE__, array( 'Gallery_Sharing_Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gallery-sharing-deactivator.php';

/** This action is documented in includes/class-ph-octavius-deactivator.php */
register_deactivation_hook( __FILE__, array( 'Gallery_Sharing_Deactivator', 'deactivate' ) );

/**
 * The core plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gallery-sharing.php';

/**
 * Begins execution of the plugin.
 */
function run_gallery_sharing() {

	$plugin = new Gallery_Sharing();
	$plugin->run();

}
run_gallery_sharing();
