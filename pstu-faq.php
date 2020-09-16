<?php


namespace pstu_faq;


/**
 * Стартовый файл регистрации плагина в WordPress
 *
 * @link              https://pstu.edu/
 * @since             2.0.0
 * @package           pstu-dissertation
 *
 * @wordpress-plugin
 * Plugin Name:       Вопросы-ответы
 * Plugin URI:        https://pstu.edu/
 * Description:       Плагин для публикации раздела вопросов-ответов в сети сайтов ГВУЗ ПГТУ.
 * Version:           2.1.0
 * Author:            chomovva
 * Author URI:        https://cct.pstu.edu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pstu_faq
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
define( 'PSTU_FAQ_VERSION', '2.1.0' );
define( 'PSTU_FAQ_NAME', 'pstu_faq' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'pstu_faq\activate' );
register_deactivation_hook( __FILE__, 'pstu_faq\deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run() {

	$plugin = new Manager();
	$plugin->run();

}
run();