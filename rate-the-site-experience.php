<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/thechetanvaghela/
 * @since             1.0.0
 * @package           Rate_The_Site_Experience
 *
 * @wordpress-plugin
 * Plugin Name:       Rate the Site experience
 * Plugin URI:        https://github.com/thechetanvaghela/rate-the-site-experience
 * Description:       Rate the site experience by number.
 * Version:           1.0.0
 * Author:            Chetan Vaghela
 * Author URI:        https://profiles.wordpress.org/thechetanvaghela//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rate-the-site-experience
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
define( 'RTSE_RATE_THE_SITE_EXPERIENCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rate-the-site-experience-activator.php
 */
function rtse_activate_rate_the_site_experience() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rate-the-site-experience-activator.php';
	RTSE_Rate_The_Site_Experience_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rate-the-site-experience-deactivator.php
 */
function rtse_deactivate_rate_the_site_experience() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rate-the-site-experience-deactivator.php';
	RTSE_Rate_The_Site_Experience_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'rtse_activate_rate_the_site_experience' );
register_deactivation_hook( __FILE__, 'rtse_deactivate_rate_the_site_experience' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rate-the-site-experience.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function rtse_run_rate_the_site_experience() {

	$plugin = new RTSE_Rate_The_Site_Experience();
	$plugin->run();

}
rtse_run_rate_the_site_experience();
