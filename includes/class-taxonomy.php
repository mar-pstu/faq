<?php





if ( ! defined( 'ABSPATH' ) ) { exit; };





class pstuFAQTaxonomy extends pstuFAQAbstractObject {


	function set_fields() {
		$this->labels = array(
			'name'               => __(	'Категорії питань FAQ', $this->domain ),
			'singular_name'      => __(	'Категорії питань FAQ', $this->domain ),
			'search_items'       => __(	'Знайти категорію FAQ', $this->domain ),
			'all_items'          => __(	'Всі категорії FAQ', $this->domain ),
			'parent_item'        => __(	'Батьківський категорія FAQ', $this->domain ),
			'parent_item_colon'  => __(	'Батьківський категорія FAQ', $this->domain ),
			'edit_item'          => __(	'Редагувати категорію', $this->domain ),
			'update_item'        => __(	'Оновити категорію', $this->domain ),
			'add_new_item'       => __(	'Додати нову категорію', $this->domain ),
			'new_item_name'      => __(	'Назва нової категорії', $this->domain ),
			'menu_name'          => __(	'Категорії питань', $this->domain ),
		);
		$this->args = array(
			'hierarchical'       => false,
			'labels'             => $this->labels,
			'show_tagcloud'      => true,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'meta_box_cb'        => false,
			'show_admin_column'  => true,
			'query_var'          => true,
			'show_in_rest'       => true,
		);
	}




	public function register() {
		register_taxonomy( $this->name, array(), $this->args );
	}




}