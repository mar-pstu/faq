<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


get_header();


do_action( 'faq_before_main_content' );


if ( have_posts() ) {


	do_action( 'faq_before_loop' );


	?>

		<ul>

	<?php


		while ( have_posts() ) {

			the_post();

			do_action( 'faq_post_loop' );

			include PartPublicPostTypeFAQ::get_template_file_path( 'content-faq.php' );
			
		}


	?>

		</ul>

	<?php


	do_action( 'faq_after_loop' );

	
} else {

	do_action( 'faq_no_post_found' );

}


do_action( 'faq_after_main_content' );


get_footer();