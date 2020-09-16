<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Класс отвечающий за функциональность для
 * таксономии "Школьные предметы"
 * @since      1.0.0
 * @package    websputnik
 * @subpackage websputnik/admin
 * @author     chomovva <chomovva@gmail.com>
 */
class PartAdminTaxonomyFAQCategory extends PartTaxonomyFAQCategory {


	/**
	 * Инициализация класса и установка его свойства.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       Имя плагин и слаг метаполей
	 * @param    string    $version           Текущая версия
	 */
	public function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq_category_admin';
	}


	/**
	 *	Регистрация метабокса
	 * @since    2.0.0
	 * @var      string       $post_type
	 */
	public function add_meta_box( $post_type ) {
		global $wp_taxonomies;
		if ( is_object_in_taxonomy( $post_type, $this->taxonomy_name ) && isset( $wp_taxonomies[ $this->taxonomy_name ] ) ) {
			add_meta_box(
				$this->part_name,
				$wp_taxonomies[ $this->taxonomy_name ]->labels->singular_name,
				array( $this, 'render_metabox_content' ),
				$post_type,
				'side',
				'high',
				null
			);
		}
	}


	/**
	 * Сохранение записи типа "конкурсная работа"
	 * @since    2.0.0
	 * @var      int          $post_id
	 */
	public function set_object_terms( $post_id, $post ) {
		if ( ! isset( $_POST[ "{$this->part_name}_nonce" ] ) ) return;
		if ( ! wp_verify_nonce( $_POST[ "{$this->part_name}_nonce" ], $this->part_name ) ) { wp_nonce_ays(); return; }
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( wp_is_post_revision( $post_id ) ) return;
		if ( 'page' == $_POST[ 'post_type' ] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_nonce_ays();
			return;
		}
		$terms = [];
		if ( isset( $_POST[ $this->part_name ] ) ) {
			$terms = wp_parse_id_list( $_POST[ $this->part_name ] );
		}
		wp_set_object_terms( $post_id, $terms, $this->taxonomy_name, false );
	}


	/**
	 * Регистрирует стили для админки
	 * @since    2.0.0
	 * @var      WP_Post       $post
	 */
	public function render_metabox_content( $post ) {
		$terms = get_terms( [
			'taxonomy'   => $this->taxonomy_name,
			'hide_empty' => false,
			'fields'     => 'id=>name',
		] );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			wp_nonce_field( $this->part_name, "{$this->part_name}_nonce" );
			$id = "{$this->part_name}-control";
			$label = '';
			$name = $this->part_name;
			$selected = wp_get_object_terms( $post->ID, $this->taxonomy_name, [
				'fields' => 'ids',
			] );
			$control = COntrol::create_control( $this->plugin_name, $this->version, 'dropdown', [
				'atts'     => [
					'class'  => 'form-control',
					'id'     => $id,
					'name'   => $this->part_name,
				],
				'choices'  => $terms,
				'selected' => ( is_array( $selected ) ) ? $selected : [],
			] );
			include dirname( __FILE__ ) . '/partials/form-group.php';
		} else {
			_e( 'Сначала заполните таксономию!', $this->plugin_name );
		}
	}


	/**
	 * Сохрание дополнительных метаполей для термина
	 * @param    int    $term_id    идентификатор текущего терма
	 */
	public function save_term_fields( $term_id ) {
		if ( ! current_user_can( 'edit_term', $term_id ) ) return;
		if (
			( isset( $_POST[ '_wpnonce' ] ) && ! wp_verify_nonce( $_POST[ '_wpnonce' ], "update-tag_$term_id" ) ) ||
			( isset( $_POST[ '_wpnonce_add-tag' ] ) && ! wp_verify_nonce( $_POST[ '_wpnonce_add-tag' ], "add-tag" ) )
		) return;
		foreach ( $this->taxonomy_meta_fields as $name => $label ) {
			$new_value = ( isset( $_REQUEST[ $name ] ) ) ? $this->sanitize_taxonomy_meta_fields( $name, $_REQUEST[ $name ] ) : '';
			if ( empty( $new_value ) ) {
				delete_term_meta( $term_id, $name );
			} else {
				update_term_meta( $term_id, $name, $new_value );
			}
		}
	}


	/**
	 * Проверка и очистка дополнительных метаполей
	 * @var      string    $key      Идентификатор поля
	 * @var      string    $value    Новое значение металополя
	 * */
	function sanitize_taxonomy_meta_fields( $key, $value ) {
		$result = '';
		switch ( $key ) {
			case 'color_bg':
			case 'color_text':
				$result = sanitize_hex_color( $value );
				break;
		}
		return apply_filters( "sanitize_{$this->taxonomy_name}_taxonomy_meta_fields", $result, $key, $value );
	}


	/**
	 * Регистрирует дополнительную колонку в таблице терпов
	 * @since    2.0.0
	 * @param    array        $columns    массив зарегистрированных еолонок
	 * @return   array
	 */
	public function add_columns( $columns ) {
		$columns[ "{$this->part_name}_term_id" ] = __( 'ID', $this->plugin_name );
		return $columns;
	}


	/**
	 * Формирует html код содержимого ячейки
	 * @since    2.0.0
	 * @param    string    $content      содержимое яцейки
	 * @param    string    $column_name  идентификатор ячейки
	 * @param    int       $term_id      идентификатор терма
	 * @return   string
	 */
	public function render_custom_columns( $content, $column_name, $term_id ) {
		if ( "{$this->part_name}_term_id" == $column_name ) {
			$content = '<b><code>' . $term_id . '</code></b>';
		}
		return $content;
	}


}