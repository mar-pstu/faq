<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };



class pstuFAQTaxonomyAdmin extends pstuFAQAbstractPart {


	public function run() {
		$name = $this->object->get( 'name' );
		add_action( "{$name}_add_form_fields", array( $this, 'add_term_fields' ), 10, 1 );
		add_action( "{$name}_edit_form_fields", array( $this, 'edit_term_fields' ), 10, 2 );
		add_action( "created_{$name}", array( $this, 'save_term_fields' ), 10, 2 );
		add_action( "edited_{$name}", array( $this, 'save_term_fields' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 1 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 1 );
	}




	public function save_term_fields( $term_id, $tt_id ) {
		if ( ! isset( $_POST[ "{$this->slug}_nonce" ] ) ) return;
		if ( ! wp_verify_nonce( $_POST[ "{$this->slug}_nonce" ], $this->slug ) ) return;
		// страница с описанием
		if( isset( $_POST[ "{$this->slug}_page_id" ] ) ) {
			update_term_meta( $term_id, "{$this->slug}_page_id", sanitize_key( $_POST[ "{$this->slug}_page_id" ] ) );
		} else {
			delete_term_meta( $term_id, "{$this->slug}_page_id" );
		}
		// email ответственного
		if( isset( $_POST[ "{$this->slug}_email" ] ) ) {
			update_term_meta( $term_id, "{$this->slug}_email", $this->validate_email_list( $_POST[ "{$this->slug}_email" ] ) );
		} else {
			delete_term_meta( $term_id, "{$this->slug}_email" );
		}
		// непубличная категория
		if (
			isset( $_POST[ "{$this->slug}_non_public" ] )
			&& 'on' == $_POST[ "{$this->slug}_non_public" ]
		) {
			update_term_meta( $term_id, "{$this->slug}_non_public", 'on' );
		} else {
			delete_term_meta( $term_id, "{$this->slug}_non_public" );
		}
		// связанная категория с другого блога сети
		if( isset( $_POST[ "{$this->slug}_related_category" ] ) ) {
			update_term_meta( $term_id, "{$this->slug}_related_category", sanitize_text_field( $_POST[ "{$this->slug}_related_category" ] ) );
		} else {
			delete_term_meta( $term_id, "{$this->slug}_related_category" );
		}
	}




	protected function get_multisite_categories() {
		$result = __return_empty_array();
		$sites = get_sites( array(
			'site__not_in'   => get_current_blog_id(),
			'public'         => '1',
			'orderby'        => 'path',
			'order'          => 'ASC',
		) );
		if ( is_array( $sites ) && ! empty( $sites ) ) {
			foreach ( $sites as $site ) {
				$options = get_option( $this->slug );
				if ( isset( $options[ 'non_public_site' ] ) || empty( $options[ 'non_public_site' ] ) ) {
					switch_to_blog( $site->blog_id );
					$options = get_option( $this->slug, array() );
					if ( isset( $options[ 'non_public_site' ] ) && 'on' == $options[ 'non_public_site' ] ) continue;
					$terms = get_terms( array(
						'taxonomy'     => $this->object->get( 'name' ),
						'hide_empty'   => false,
						'orderby'      => 'name', 
						'order'        => 'ASC',
						'meta_query'   => array(
							'relation'     => 'OR',
							array(
								'key'      => "{$this->slug}_non_public",
								'compare'  => 'NOT EXISTS',
							),
							array(
								'key'      => "{$this->slug}_non_public",
								'compare'  => 'NOT LIKE',
								'value'    => 'on',
							),
						),
					) );
					if ( is_array( $terms ) && ! empty( $terms ) ) {
						$site_name = get_bloginfo( 'name' );
						$options = __return_empty_array();
						foreach ( $terms as $term ) {
							$value = sprintf(
								'%1$s_%2$s',
								$site->blog_id,
								$term->term_id
							);
							$label = sprintf(
								'%1$s - %2$s',
								$site_name,
								esc_html( $term->name )
							);
							$result[ $value ] = $label;
						}
					}
				}
			}
			restore_current_blog();
		}
		return $result;
	}




	public function add_term_fields() {
		wp_nonce_field( $this->slug, "{$this->slug}_nonce" );
		$multisite_categories = ( is_multisite() ) ? $this->get_multisite_categories() : __return_empty_array();
		include PSTU_FAQ_VIEWS . 'add-term-field.php';
	}



	public function edit_term_fields( $term, $taxonomy ) {
		$page_id = get_term_meta( $term->term_id, "{$this->slug}_page_id", true );
		$email = get_term_meta( $term->term_id, "{$this->slug}_email", true );
		$email = ( isset( $email ) ) ? esc_attr( $email ) : '';
		$non_public = get_term_meta( $term->term_id, "{$this->slug}_non_public", true );
		$related_category = get_term_meta( $term->term_id, "{$this->slug}_related_category", true );
		$multisite_categories = ( is_multisite() ) ? $this->get_multisite_categories() : __return_empty_array();
		wp_nonce_field( $this->slug, "{$this->slug}_nonce" );
		include PSTU_FAQ_VIEWS . 'edit-term-field.php';
	}




	/**
	 *	Добавление метабокса
	 */
	public function add_meta_box( $post_type ) {
		if ( is_object_in_taxonomy( $post_type, $this->object->get( 'name' ) ) ) {
			add_meta_box(
				$this->slug . '_' .$this->object->get( 'name' ),
				$this->object->get( 'labels' )[ 'singular_name' ],
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'side',
				'high',
				null
			);
		}
	}



	/**
	*	Вывод контента метабокса на странице педактирования поста
	*/
	public function render_meta_box_content( $post ) {
		$slug = "{$this->slug}_{$this->object->get( 'name' )}";
		wp_nonce_field( $slug, "{$slug}_nonce" );
		$current_category = get_terms( array(
			'taxonomy'    => $this->object->get( 'name' ),
			'object_ids'  => $post->ID,
			'fields'      => 'ids',
		) );
		$current_category = ( is_array( $current_category ) && ! empty( $current_category ) ) ? array_shift( $current_category ) : '';
		include PSTU_FAQ_VIEWS . 'category-meta-box.php';
	}





	/**
	* Сохранение поста
	*/
	public function save_post( $post_id ) {
		$slug = "{$this->slug}_{$this->object->get( 'name' )}";
		// безопасность
		if ( ! isset( $_POST[ "{$slug}_nonce" ] ) ) { return; }
		if ( ! wp_verify_nonce( $_POST[ "{$slug}_nonce" ], $slug ) ) {
			wp_nonce_ays();
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( wp_is_post_revision( $post_id ) ) return;	
		if ( 'page' == $_POST[ 'post_type' ] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_nonce_ays();
			return;
		}

		if ( isset( $_POST[ $slug ] ) ) {
			wp_set_object_terms( $post_id, intval( sanitize_key( $_POST[ $slug ] ) ), $this->object->get( 'name' ), false );
		} else {
			$terms = get_terms( array(
				'taxonomy'    => $this->object->get( 'name' ),
				'object_ids'  => $post_id,
				'fields'      => 'ids',
			) );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				wp_remove_object_terms( $post_id, $terms, $this->object->get( 'name' ) );
			}
		}

	}








}