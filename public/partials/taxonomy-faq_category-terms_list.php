<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


$terms = get_terms( [
	'taxonomy'   => 'faq_category',
	'hide_empty' => true,
] );


if ( is_array( $terms ) && ! empty( $terms ) ) {

	do_action( 'taxonomy-faq_category-terms_list-before_loop' );

	foreach ( $terms as $term ) {

		do_action( 'taxonomy-faq_category-terms_list-item', $term );

	}

	do_action( 'taxonomy-faq_category-terms_list-after_loop' );

} else {

	do_action( 'taxonomy-faq_category-terms_list-empty' );

}