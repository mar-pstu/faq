<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Абстрактный класс "частей" плагина
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class PartPostTypeFAQ extends PostType {


	/**
	 * Идентификатор части плагина
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version    идентификатор "части" плагина
	 */
	protected $settings;


	function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq';
		$this->post_type_name = 'faq';
		$this->meta_fields = [];
		$this->post_type_option = $this->get_part_option( $this->post_type_name, [
			'archive_description' => '',
			'before_main_content' => '',
			'after_main_content'  => '',
		] );
	}


	/**
	 * Регистрациятипа поста "Диссертация"
	 */
	public function register_post_type() {
		register_post_type( $this->post_type_name, [
			'labels'             => [
				'name'               => __( 'Вопросы-ответы', $this->plugin_name ), // Основное название типа записи
				'singular_name'      => __( 'Вопросы-ответы', $this->plugin_name ), // отдельное название записи типа Book
				'add_new'            => __( 'Добавить новую', $this->plugin_name ),
				'add_new_item'       => __( 'Добавить новый вопрос', $this->plugin_name ),
				'edit_item'          => __( 'Редактировать вопрос', $this->plugin_name ),
				'new_item'           => __( 'Новый вопрос', $this->plugin_name ),
				'view_item'          => __( 'Посмотреть вопросы-овтеты', $this->plugin_name ),
				'search_items'       => __( 'Найти запись', $this->plugin_name ),
				'not_found'          => __( 'Записей не найдено', $this->plugin_name ),
				'not_found_in_trash' => __( 'В корзине ничего не найдено', $this->plugin_name ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Вопросы-ответы', $this->plugin_name ),

			],
			'description'        => $this->post_type_option[ 'archive_description' ],
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => true,
			'exclude_from_search' => false,
			'capability_type'    => $this->post_type_name,
			'capabilities'       => [
				'edit_published_posts'=> "edit_published_{$this->post_type_name}",
				'publish_posts'       => "publish_{$this->post_type_name}",
				'delete_published_posts' => "delete_published_{$this->post_type_name}",
				'edit_posts'          => "edit_{$this->post_type_name}",
				'delete_posts'        => "delete_{$this->post_type_name}",
				'read_post'           => "read_{$this->post_type_name}",
				'read_private_posts'  => "read_private_{$this->post_type_name}",
			],
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-sos',
			'menu_position'      => null,
			'supports'           => [ 'title', 'editor', 'thumbnail', 'comments' ],
		] );
	}


}