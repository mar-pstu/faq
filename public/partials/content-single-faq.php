<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


do_action( 'faq_before_single_post' );


?>


	<article id="faq-<?php the_ID(); ?>" <?php post_class( '', get_the_ID() ); ?> >


		<h1><?php the_title( '', '', true ); ?></h1>


		<?php the_content(); ?>


	</article>


<?


do_action( 'faq_after_single_post' );