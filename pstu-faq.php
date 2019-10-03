<?php

/**
Plugin Name: FAQ
Plugin URI: http://pstu.edu/
Description: Плагин для создания раздела типовых "вопросов-ответов" в сети сайтов
Author: PSTU
Version: 2.0.0
Author URI: https://chomovva.ru/
License: GPL2
Text Domain: pstu-faq
Domain Path: /languages/
*/


if ( ! defined( 'ABSPATH' ) ) { exit; };



define( 'PSTU_FAQ_INCLUDES', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );
define( 'PSTU_FAQ_ASSETS', untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/assets/' );
define( 'PSTU_FAQ_LANGUAGES', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
define( 'PSTU_FAQ_VIEWS', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/views/' );
define( 'PSTU_FAQ_TEMPLATES', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );



require_once PSTU_FAQ_INCLUDES . 'class-manager.php';



function run_pstu_faq() {
	$manager = new pstuFAQManager( 'pstu_faq', '2.0.0', 'pstu-faq', 'faq', 'faq_category' );
	$manager->run();
}

run_pstu_faq();