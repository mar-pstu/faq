<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };



class pstuFAQPostTypePublic extends pstuFAQAbstractPart {



	public function run() {
		add_action( 'template_redirect', array( $this, 'index_redirect' ), 10, 0 );
	}




	public function index_redirect() {
		if ( is_post_type_archive( $this->object->get( 'name' ) ) ) {
			$options = get_option( $this->slug );
			if ( ( isset( $options[ 'page_id' ] ) ) && ( ! empty( $options[ 'page_id' ] ) ) ) {
				if ( function_exists( 'pll_get_post' ) ) $options[ 'page_id' ] = pll_get_post( $options[ 'page_id' ] );
				wp_redirect( get_permalink( $options[ 'page_id' ] ), 301 );
				exit;
			}
		}
	}




}