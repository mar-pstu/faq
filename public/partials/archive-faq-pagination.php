<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


global $wp_taxonomies;


$taxonomy_name = apply_filters( 'faq_pagination_taxonomy_name', 'faq_category' );


?>


<div class="<?php echo apply_filters( 'faq_pagination_container_classes', 'faq-pagination' ); ?>" role="list">
	
	<a role="listintem" href="<?php echo home_url( '/', null ); ?>"><?php _e( 'Главная', PSTU_FAQ_NAME ); ?></a>

	<?php

		if ( is_tax( $taxonomy_name ) ) {

			$term = get_queried_object();
			
			if ( is_object( $term ) && ! is_wp_error( $term ) ) {

				if ( array_key_exists( $term->taxonomy, $wp_taxonomies ) ) {

					$post_type_object = get_post_type_object( $wp_taxonomies[ $term->taxonomy ]->object_type[ 0 ] );

					?>

						<a role="listintem" href="<?php echo get_post_type_archive_link( $post_type_object->name ); ?>"><?php echo $post_type_object->labels->name; ?></a>

						<span role="listintem"><?php echo $term->name; ?></span>

					<?php

				}

			}

		} elseif ( is_single() ) {

			$term = get_the_terms( get_the_ID(), $taxonomy_name );

			if ( is_array( $term ) && ! empty( $term ) ) {

				$term = array_shift( $term );

				$post_type_object = get_post_type_object( get_post_type( get_the_ID() ) );

				?>

					<a role="listintem" href="<?php echo get_post_type_archive_link( $post_type_object->name ); ?>"><?php echo $post_type_object->labels->name; ?></a>

					<a role="listintem" href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a>

					<span role="listintem"><?php echo get_the_title( get_the_ID() ); ?></span>

				<?php

			}


		} else {

			if ( array_key_exists( $taxonomy_name, $wp_taxonomies ) ) {

				$post_type_object = get_post_type_object( $wp_taxonomies[ $taxonomy_name ]->object_type[ 0 ] );

				?>

					<span role="listintem"><?php echo $post_type_object->labels->name; ?></span>

				<?

			}

		}

	?>

</div>