<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


?>


<li id="faq-<?php the_ID(); ?>"><a href="<?php the_permalink( get_the_ID() ) ?>"><?php the_title( '', '', true ); ?></a></li>