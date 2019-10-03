<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };






class pstuFAQShortcodeTheCategoryPosts {



	protected $post_type;



	protected $taxonomy;



	function __construct( $slug, $version, $domain, $name ) {
		$this->slug = $slug;
 		$this->version = $version;
 		$this->domain = $domain;
 		$this->name = $name;
 		$this->post_type = 'faq';
 		$this->taxonomy = 'faq_category';
	}



	function run() {
		add_action( 'init', array( $this, 'register_assets' ), 10, 0 );
		add_action( 'init', array( $this, 'register_blocks' ), 10, 0 );
		if ( ! is_admin() ) {
			add_shortcode( $this->name, array( $this, 'render_content' ) );
			add_shortcode( strtoupper( $this->name ), array( $this, 'render_content' ) );
		}
	}




	protected function get_entries( $term_id ) {
		$result = __return_empty_array();
		$args = array(
			'numberposts'      => '-1',
			'category'         => 0,
			'orderby'          => 'post',
			'order'            => 'DESC',
			'post_type'        => $this->post_type,
			'suppress_filters' => true,
			'tax_query'        => array(
				'relation'       => 'AND',
				array(
					'taxonomy'   => $this->taxonomy,
					'field'      => 'term_id',
					'terms'      => $term_id,
					'operator'   => 'IN',
				),
			),
		);
		$entries = get_posts( $args );
		if ( is_array( $entries ) && ! empty( $entries ) ) {
			$result = array_map( function ( $item ) {
				return array(
					'permalink'  => get_permalink( $item->ID ),
					'post_title' => apply_filters( 'the_title', $item->post_title, $item->ID )
				);
			} , $entries );
		}
		return $result;
	}




	protected function render_category_entries( $term_id ) {
		$entries = $this->get_entries( $term_id );
		$related_category = get_term_meta( $term_id, "{$this->slug}_related_category", true );
		if ( ! empty( $related_category ) ) {
			$related_category_args = explode( '_' , $related_category );
			switch_to_blog( $related_category_args[ 0 ] );
			$entries = array_merge( $entries, $this->get_entries( $related_category_args[ 1 ] ) );
			restore_current_blog();
		}
		return ( empty( $entries ) ) ? '' : sprintf(
			'<ul class="faq-category-%1$s">%2$s</ul>',
			$term_id,
			implode( "\r\n", array_map( function ( $entry ) {
				return sprintf(
					'<li><a href="%1$s">%2$s</a></li>',
					$entry[ 'permalink' ],
					$entry[ 'post_title' ]
				);
			}, $entries ) )
		);
	}




	public function render_content( $atts ) {
		$atts = shortcode_atts( array(
			'id'              => '-1',
		), $atts, $this->name );
		$result = __return_empty_array();
		$atts[ 'id' ] = sanitize_key( $atts[ 'id' ] );
		if ( '-1' == $atts[ 'id' ] ) {
			// выводим все категории
			$terms = get_terms( array(
				'taxonomy'     => $this->taxonomy,
				'hide_empty'   => false,
				'orderby'      => 'name', 
				'order'        => 'ASC',
				'fields'       => 'id=>name',
			) );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term_id => $name ) {
					$list = $this->render_category_entries( $term_id );
					if ( ! empty( $list ) ) $result[] = sprintf(
						'<h3>%1$s</h3> %2$s',
						apply_filters( 'single_term_title', $name ),
						$list
					);
				}
			}
		} else {
			// выводим посты одной категории
			$list = $this->render_category_entries( $atts[ 'id' ] );
			if ( ! empty( $list ) ) {
				$result[] = $list;
			}
		}
		return implode( "\r\n", $result );
	}






	public function register_assets() {
		wp_register_script(
			"{$this->slug}-editor-the-category-posts",
			PSTU_FAQ_ASSETS . 'scripts/editor-the-category-posts.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ),
			$this->version,
			true
		);
	}



	public function register_blocks() {
		register_block_type( "pstu-faq/the-category-posts", array(
			'editor_script' => "{$this->slug}-editor-the-category-posts",
		) );
	}



}