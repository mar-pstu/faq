<?php





if ( ! defined( 'ABSPATH' ) ) {	exit; };





trait pstuFAQFunctions  {



	function get_pages( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'sort_order'   => 'ASC',
			'sort_column'  => 'post_title',
			'hierarchical' => 1,
			'exclude'      => '',
			'include'      => '',
			'meta_key'     => '',
			'meta_value'   => '',
			'authors'      => '',
			'child_of'     => 0,
			'parent'       => -1,
			'exclude_tree' => '',
			'number'       => '',
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
		) );
		$result = __return_empty_array();
		$pages = get_pages( $args );
		if ( is_array( $pages ) && ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				$result[ $page->ID ] = esc_html( apply_filters( 'the_title', $page->post_title, $page->ID ) );
			}
		}
		return $result;
	}



	function get_categories( $name ) {
		$categories = get_terms( array(
			'taxonomy'      => $name,
			'orderby'       => 'name', 
			'order'         => 'ASC',
			'hide_empty'    => false,
			'fields'        => 'id=>name'
		) );
		return ( is_array( $categories ) && ! empty( $categories ) ) ? $categories : __return_empty_array();
	}



	function render_options( $values, $current = '' ) {
		$result = __return_empty_array();
		if ( is_array( $values ) && ! empty( $values ) ) {
			$result[] = '<option></option>';
			foreach ( $values as $value => $title ) {
				$result[] = sprintf(
					'<option value="%1$s" %2$s>%3$s</option>',
					$value,
					selected( $value, $current, false ),
					esc_html( $title )
				);
			}
		}
		return implode( "\r\n", $result );
	}



	function validate_email_list( $list ) {
		return implode( ", ", array_filter( wp_parse_list( sanitize_text_field( $list ) ), 'is_email' ) );
	}





	function is_taxonomy_for_object_type( $taxonomy_name, $object_type ) {
		$result = __return_false();
		$taxonomy = get_taxonomy( $taxonomy_name );
		if ( $taxonomy && ! is_wp_error( $taxonomy ) && ! get_post_type_object( $object_type ) ) {
			$result = in_array( $object_type, $taxonomy->object_type );
		}
		return $result;
	}





	/**
	 * Выводит шаблон для wp.template
	 *
	 * String
	 * String
	 *
	 */
	public function render_tmpl( $id, $path ) {
		if ( file_exists( $path ) ) printf(
			'<script type="text/html" id="tmpl-%1$s">%2$s</script>',
			$id,
			file_get_contents( $path )
		);
	}




	/**
	 *	Получение IP пользователя
	 */
	private function get_the_user_ip() {
		if ( ! empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
			$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
		} elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
			$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		} else {
			$ip = $_SERVER[ 'REMOTE_ADDR' ];
		}
		return apply_filters( 'edd_get_ip', $ip );
	}




}