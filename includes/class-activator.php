<?php


namespace pstu_faq;


/**
 * Запускается при активации плагина
 *
 * @link       http://pstu.edu
 * @since      2.1.0
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 */

/**
 * Запускается при активации плагина.
 * В этом классе находится весь код, который необходимый при активации плагина.
 * @since      2.0.0
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Activator {


	/**
	 * Действия которые необходимо выполнить при активации
	 * @since    2.0.0
	 */
	public static function activate() {
		$options = get_option( PSTU_FAQ_NAME );
		if ( ! is_array( $options ) && ! array_key_exists( 'version', $options ) && empty( $options[ 'version' ] ) ) {
			$options = [
				'version'           => PSTU_FAQ_VERSION,
				'updating_progress' => false,
			];
			update_option( PSTU_FAQ_NAME, $options );
		}
		add_role( 'faq_editor', __( 'Редактор вопросов-ответов', PSTU_FAQ_NAME ), [
			'read'         => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
		] );
	}


}