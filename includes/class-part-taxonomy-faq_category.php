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
class PartTaxonomyFAQCategory extends Taxonomy {


	function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq_category';
		$this->taxonomy_name = 'faq_category';
		$this->meta_fields = [
			'mail_for_questions'  => __( 'Email для вопросов' )
		];
		$this->part_option = array_merge( apply_filters( "{$plugin_name}-default_part_options", [
			'description' => '',
		], $this->part_name ), get_option( $this->part_name, [] ) );
	}


	public function register_taxonomy() {
		register_taxonomy( $this->taxonomy_name, [], [ 
			'label'                 => '', // определяется параметром $labels->name
			'labels'                => [
				'name'                => __( 'Категория вопросов-ответов', $this->plugin_name ),
				'singular_name'       => __( 'Категория вопросов-ответов', $this->plugin_name ),
				'search_items'        => __( 'Найти запись', $this->plugin_name ),
				'all_items'           => __( 'Все категории', $this->plugin_name ),
				'edit_item'           => __( 'Редагувати категорию', $this->plugin_name ),
				'update_item'         => __( 'Обновить категорию', $this->plugin_name ),
				'add_new_item'        => __( 'Добавить новую категорию', $this->plugin_name ),
				'new_item_name'       => __( 'Название научного совета', $this->plugin_name ),
				'menu_name'           => __( 'Категории', $this->plugin_name ),
			],
			'description'           => $this->part_option[ 'description' ],
			'public'                => true,
			'publicly_queryable'    => true,
			'show_in_nav_menus'     => true,
			'show_ui'               => true, // равен аргументу public
			'show_in_menu'          => true, // равен аргументу show_ui
			// 'show_tagcloud'         => true, // равен аргументу show_ui
			// 'show_in_quick_edit'    => null, // равен аргументу show_ui
			'hierarchical'          => false,
			'rewrite'               => true,
			// 'query_var'             => $taxonomy, // название параметра запроса
			'capabilities'          => [],
			'meta_box_cb'           => false, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'     => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'          => null, // добавить в REST API
			'rest_base'             => null, // $taxonomy
			// '_builtin'              => false,
			// 'update_count_callback' => '_update_post_term_count',
		] );
	}


}