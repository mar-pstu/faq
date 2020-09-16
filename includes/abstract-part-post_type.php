<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Абстрактный класс произвольных типов постов плагина
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
abstract class PostType extends Part {


	/**
	 * Идентификатор (имя) типа поста
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version      идентификатор типа поста
	 */
	protected $post_type_name;


	/**
	 * Метаполя
	 * @since    2.1.0
	 * @access   private
	 * @var      array     $meta_fields  идентификатор => имя поля
	 */
	protected $post_type_meta_fields;


	/**
	 * Настройки для этого типа записи
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version    настройки
	 */
	protected $post_type_option;


	/**
	 * Возвращает идентификатор (имя) типа поста
	 * @since     2.1.0
	 * @return    string    Номер текущей версии плагина
	 */
	public function get_post_type_name() {
		return $this->post_type_name;
	}


}