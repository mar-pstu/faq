<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


$terms = get_terms( [
	'taxonomy'   => 'faq_category',
	'hide_empty' => false,
] );


if ( is_array( $terms ) && ! empty( $terms ) ) {

	do_action( 'taxonomy-faq_category-terms_before' );

	foreach ( $terms as $term ) {
		
		do_action( 'faq_category-render_term', $term );

	}

	do_action( 'taxonomy-faq_category-terms_after' );

} else {

	do_action( 'taxonomy-faq_category-no_terms' );

}