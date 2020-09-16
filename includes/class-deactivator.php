<?php


namespace pstu_faq;


/**
 * Запускается при деактивации плагина
 *
 * @link       https://pstu.edu/
 * @since      2.1.0
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 */

/**
 * Запускается при деактивации плагина
 *
 * В этом классе находится весь код, который необходимый при деактивации плагина.
 *
 * @since      2.1.0
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Deactivator {


	/**
	 * Действия при деактивации
	 *
	 * @since    2.1.0
	 */
	public static function deactivate() {
		remove_role( 'faq_editor' );
	}


}