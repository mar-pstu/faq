<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Класс отвечающий за функциональность для
 * пользовательского типа записи "Объект недвижимости"
 * @since      2.1.0
 * @package    pstu_faq
 * @subpackage pstu_faq/admin
 * @author     chomovva <chomovva@gmail.com>
 * */
class PartPublicPostTypeFAQ extends PartPostTypeFAQ {


	use TemplateInclude;


	/**
	 * Инициализация класса и установка его свойства.
	 *
	 * @since    2.1.0
	 * @param    string    $plugin_name       Имя плагин и слаг метаполей
	 * @param    string    $version           Текущая версия
	 * */
	public function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq_public';
	}


	/**
	 * Подключает шаблоны для содержимого постов в цикле WP
	 * */
	public function include_post_loop_template() {
		$file_path = '';
		if ( is_single() ) {
			$file_path = $this->get_template_file_path( 'content-single-faq.php' );
		} elseif ( is_post_type_archive( $this->post_type_name ) ) {
			$file_path = $this->get_template_file_path( 'content-faq.php' );
		}
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Выводит контент перед началом основго блока вопросов-ответов
	 * */
	public function before_main_content() {
		echo do_shortcode( $this->post_type_option[ 'before_main_content' ], false );
	}


	/**
	 * Выводит контент после основго блока с содержимым вопросов-ответов
	 * */
	public function after_main_content() {
		echo do_shortcode( $this->post_type_option[ 'after_main_content' ], false );
	}


	/**
	 * Вставляет в шаблон архива записи ссылки на элементы таксономии
	 * */
	public function include_taxonomies_list() {
		if ( ! is_tax() ) {
			$taxonomy_names = get_object_taxonomies( $this->post_type_name, 'names' );
			if ( is_array( $taxonomy_names ) && ! empty( $taxonomy_names ) ) {
				foreach ( $taxonomy_names as $taxonomy_name ) {
					$file_path = $this->get_template_file_path( "taxonomy-{$taxonomy_name}-terms.php" );
					if ( ! empty( $file_path ) ) {
						include $file_path;
					}
				}
			}
		}
	}


	/**
	 * Ищет и подключает шаблон начала обёртки для списка вопросов ответов
	 * */
	public function before_list_wrap() {
		$file_path = $this->get_template_file_path( "archive-{$this->post_type_name}-before_loop.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Ищет и подключает шаблон конца обёртки для списка вопросов ответов
	 * */
	public function after_list_wrap() {
		$file_path = $this->get_template_file_path( "archive-{$this->post_type_name}-after_loop.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


}