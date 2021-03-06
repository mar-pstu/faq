<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


get_header();


do_action( 'faq_before_main_content' );


if ( have_posts() ) {


	do_action( 'faq_before_loop' );


	while ( have_posts() ) {

		the_post();

		do_action( 'faq_post_loop' );
		
	}



	do_action( 'faq_after_loop' );

	
} else {

	do_action( 'faq_no_post_found' );

}


do_action( 'faq_after_main_content' );


get_footer();