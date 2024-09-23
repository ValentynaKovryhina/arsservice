<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://imaris-agentur.de/
 * @since             1.0.0
 * @package           Ars_Service
 *
 * @wordpress-plugin
 * Plugin Name:       ArsService
 * Plugin URI:        https://imaris-agentur.de/
 * Description:       A custom plugin for internal use, designed to manage repair requests. Administrators can create and track equipment repair tickets, ensuring efficient management and resolution within the team.
 * Version:           1.0.0
 * Author:            Imaris
 * Author URI:        https://imaris-agentur.de//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ars-service
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ARS_SERVICE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ars-service-activator.php
 */
function activate_ars_service() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ars-service-activator.php';
	Ars_Service_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ars-service-deactivator.php
 */
function deactivate_ars_service() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ars-service-deactivator.php';
	Ars_Service_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ars_service' );
register_deactivation_hook( __FILE__, 'deactivate_ars_service' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ars-service.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ars_service() {

	$plugin = new Ars_Service();
	$plugin->run();

}
run_ars_service();
