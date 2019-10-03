<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };






class pstuFAQPublic extends pstuFAQAbstractPart {



 	public function run() {
 		add_action( 'template_redirect', 'archive_redirect' );
 	}



 	public function archive_redirect() {
 		if ( get_post_type() == $this->type ) {
			if ( is_home() or is_front_page() or is_category() or is_archive() ) {
				$o = get_option( $this->slug );
				$page_id = ( isset( $o[ 'page_id' ] ) ) ? $o[ 'page_id' ] : '';
				$page_id = ( function_exists( 'pll_get_post' ) ) ? pll_get_post( $page_id ) : $page_id;
				wp_redirect( get_the_permalink( $page_id ), 301 );
				exit;
			}
		}
 	}




}