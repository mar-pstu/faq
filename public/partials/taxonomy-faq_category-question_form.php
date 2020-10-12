<?php


namespace websputnik;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


$question_form = do_shortcode( '[faq_category_public_question_form]', false );


if ( ! empty( $question_form ) ) {

	?>

		<h2><?php _e( 'Задать вопрос', PSTU_FAQ_NAME ); ?></h2>

	<?

	echo $question_form;

}