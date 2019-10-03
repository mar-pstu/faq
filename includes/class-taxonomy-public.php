<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };



class pstuFAQTaxonomyPublic extends pstuFAQAbstractPart {



	public function run() {
		add_action( 'template_redirect', array( $this, 'index_redirect' ), 10, 0 );
	}




	public function index_redirect() {
		if ( is_tax( $this->object->get( 'name' ) ) ) {
			$permalink = get_permalink( get_term_meta( get_queried_object_id(), "{$this->slug}_page_id", true ) );
			if ( $permalink ) {
				wp_redirect( $permalink, 301 );
			}
			exit;
		}
	}







}