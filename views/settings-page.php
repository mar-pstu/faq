<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>
<div class="wrap">
	<h1 class="dashicons-before dashicons-sos"> <?php echo get_admin_page_title(); ?></h1>
	<form method="post" enctype="multipart/form-data" action="options.php">
		<?php settings_fields( $this->slug ); ?>
		<?php do_settings_sections( $this->slug ); ?>
		<?php submit_button(); ?>
	</form>
</div>