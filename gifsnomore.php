<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 *
 * Plugin Name: GIFsNoMore
 * Plugin URI:  https://github.com/thenextweb/gifs-no-more
 * Version: 1.0-alpha
 * Description: Transform animated GIF attachments to HTML5 video
 * Author: The Next Web
 * Author URI: http://thenextweb.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with This code. If not, see https://www.gnu.org/licenses/gpl-3.0.html .
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_gifsnomore() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/gifsnomore-activator.php';
    Gifsnomore_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_gifsnomore() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/gifsnomore-deactivator.php';
    Gifsnomore_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gifsnomore' );
register_deactivation_hook( __FILE__, 'deactivate_gifsnomore' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/gifsnomore.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gifsnomore() {

    $plugin = new Gifsnomore();
    $plugin->run();

}
run_gifsnomore();
