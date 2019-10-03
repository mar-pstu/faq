<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>

<div class="form-field term-group form-required">
	<label for="<?php echo $this->slug; ?>_email"><?php _e( 'Email для автоматичного пересилання:', $this->domain ); ?></label>
	<input id="<?php echo $this->slug; ?>_email" name="<?php echo $this->slug; ?>_email" type="text" value="">
</div>


<div class="form-field term-group">
	<label for="<?php echo $this->slug; ?>_page_id"><?php _e( 'Сторінка з описом:', $this->domain ); ?></label>
	<select name="<?php echo $this->slug; ?>_page_id" id="<?php echo $this->slug; ?>_page_id" style="display: block; width: 100%;">
		<option value=""><?php _e( 'Не обрано', $this->domain ); ?></option>
		<?php foreach ( $this->get_pages() as $id => $title ) : ?>
			<option value="<?php echo $id; ?>"><?php echo $title; ?></option>
		<?php endforeach; ?>
	</select>
</div>


<div class="form-field term-group">
	<label for="<?php echo $this->slug; ?>_non_public">
		<input id="<?php echo $this->slug; ?>_non_public" name="<?php echo $this->slug; ?>_non_public" type="checkbox" style="display: inline-block; vertical-align: middle;" value="on">
		<?php _e( 'Непублічна категорія', $this->domain ); ?>
	</label>
	<p><?php esc_html_e( 'Категорія не використовується у фільтрах та питаннях відвідувачів и не используется на других сайтах сети.', $this->domain ); ?></p>
</div>


<?php if ( ! empty( $multisite_categories ) ) : ?>
	<div class="form-field term-group">
		<label for="<?php echo $this->slug; ?>_related_category"><?php _e( 'Привязання категория', $this->domain ); ?></label>
		<select name="<?php echo $this->slug; ?>_related_category" id="<?php echo $this->slug; ?>_related_category" style="display: block; width: 100%;">
			<option value=""><?php _e( 'Не обрано', $this->domain ); ?></option>
			<?php foreach ( $multisite_categories as $value => $label ) : ?>
				<option value="<?php echo $value ?>"><?php echo esc_attr( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
<?php endif; ?>