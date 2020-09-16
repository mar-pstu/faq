<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Регистрирует произвольные типы записи и ппроизвольные таксономии
 *
 * @since      2.0.0
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Init extends Part {


	/**
	 * Привязывает указанные таксономии к типам постов.
	 */
	public function register_taxonomy_for_object_type() {
		register_taxonomy_for_object_type( 'faq_category', 'faq' );
		register_taxonomy_for_object_type( 'post_tag', 'faq' );
	}


}