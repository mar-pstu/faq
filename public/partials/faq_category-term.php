<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


$term_logo_url = apply_filters( 'faq_category_logo', get_term_meta( $term, 'logo', true ), $term->term_id );


?>


<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
	
	<section id="faq_category-<?php echo $term->term_id; ?>" class="faq_category-term" role="listitem">
		
		<div class="row middle-xs">
			
			<?php if ( ! empty( $term_logo_url ) ) : ?>
				<div class="col-xs-4 col-sm-3 col-md-3 col-lg-2">
					<a href="<?php echo get_term_link( $term ); ?>">
						<img class="lazy faq_category-term_logo" src="#" data-src="<?php echo esc_attr( $term_logo_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
					</a>
				</div>
			<?php endif; ?>

			<div class="col-xs-8 col-sm col-md col-lg">
				<h4><a href="<?php echo get_term_link( $term ); ?>"><?php echo apply_filters( 'single_term_title', $term->name ); ?></a></h4>
				<?php echo $term->description; ?>
			</div>

		</div>

	</section>

</div>