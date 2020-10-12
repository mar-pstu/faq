<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


echo apply_filters( 'faq_archive_title_before', '<h1>' );

post_type_archive_title( apply_filters( 'faq_archive_title_prefix', '' ), true );

echo apply_filters( 'faq_archive_title_before', '</h1>' );

$description = get_the_post_type_description();

if ( ! empty( trim( $description ) ) ) {

	echo apply_filters( 'faq_archive_description_before', '<div>' );

	echo $description;

	echo apply_filters( 'faq_archive_description_after', '</div>' );

}