<?php



if ( ! defined( 'ABSPATH' ) ) { exit; };



class pstuFAQPostType extends pstuFAQAbstractObject {



	function set_fields() {
		$this->labels = array(
			'name'                => __( 'Питання-відповіді', $this->domain ),
			'singular_name'       => __( 'Питання-відповідь', $this->domain ),
			'add_new'             => __( 'Додати запис FAQ', $this->domain ),
			'add_new_item'        => __( 'Додати новий FAQ', $this->domain ),
			'edit_item'           => __( 'Редагувати FAQ', $this->domain ),
			'new_item'            => __( 'Новий запис FAQ', $this->domain ),
			'all_items'           => __( 'Всі питання-відповіді', $this->domain ),
			'view_item'           => __( 'Перегляд FAQ на сайті', $this->domain ),
			'search_items'        => __( 'Шукати FAQ у списку', $this->domain ),
			'not_found'           => __( 'FAQ не найдено', $this->domain ),
			'not_found_in_trash'  => __( 'В кошику немає FAQ.', $this->domain ),
			'menu_name'           => __( 'Питання-відповіді', $this->domain ),
		);
		$this->args = array(
			'labels'              => $this->labels,
			'public'              => true,
			'show_ui'             => true,
			'has_archive'         => true, 
			'menu_icon'           => 'dashicons-sos',
			'menu_position'       => '57.71',
			'show_in_rest'        => 'true',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'comments' ),
			'taxonomies'          => array( 'post_tag' ),
		);
	}



	public function register() {
		register_post_type( $this->name, $this->args );
	}




}
