<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/thechetanvaghela/
 * @since      1.0.0
 *
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    RTSE_Rate_The_Site_Experience
 * @subpackage RTSE_Rate_The_Site_Experience/includes
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class RTSE_Rate_The_Site_Experience_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rate-the-site-experience',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
