<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>




<tr class="form-field term-group-wrap">
	<th scope="row">
		<label for="<?php echo $this->_page_id; ?>_page_id">
			<?php _e( 'Сторінка з описом:', $this->domain ); ?>
		</label>
	</th>
	<td>
		<select name="<?php echo $this->slug; ?>_page_id" id="<?php echo $this->slug; ?>_page_id" style="display: block; width: 100%;">
			<option value=""><?php _e( 'Не обрано', $this->domain ); ?></option>
			<?php foreach ( $this->get_pages() as $id => $title ) : ?>
				<option value="<?php echo $id; ?>" <?php selected( $page_id, $id, true ); ?> ><?php echo $title; ?></option>
			<?php endforeach; ?>
		</select>
	</td>
</tr> 




<tr class="form-field term-group-wrap form-required">
	<th scope="row">
		<label for="<?php echo $this->slug; ?>_email">
			<?php _e( 'Email для автоматичного пересилання:', $this->domain ); ?>
		</label>
	</th>
	<td>
		<input type="text" id="<?php echo $this->slug; ?>_email" value="<?php echo $email; ?>" name="<?php echo $this->slug; ?>_email">
	</td>
</tr>




<tr class="form-field term-group-wrap form-required">
	<th scope="row">
		<label for="<?php echo $this->slug; ?>_non_public">
			<?php _e( 'Непублічна категорія:', $this->domain ); ?>
		</label>
	</th>
	<td>
		<p class="description">
			<input type="checkbox" id="<?php echo $this->slug; ?>_non_public" value="on" name="<?php echo $this->slug; ?>_non_public" <?php checked( 'on', $non_public, true ); ?>>
			<?php esc_html_e( 'Категорія не використовується у фільтрах та питаннях відвідувачів и не используется на других сайтах сети.', $this->domain ); ?>						
		</p>
	</td>
</tr>




<?php if ( ! empty( $multisite_categories ) ) : ?>
	<tr class="form-field term-group-wrap">
		<th scope="row">
			<label for="<?php echo $this->slug; ?>_related_category">
				<?php _e( 'Привязання категория', $this->domain ); ?>
			</label>
		</th>
		<td>
			<select name="<?php echo $this->slug; ?>_related_category" id="<?php echo $this->slug; ?>_related_category" style="display: block; width: 100%;">
				<option value=""><?php _e( 'Не выбрано', $this->domain ); ?></option>
				<?php foreach ( $multisite_categories as $value => $label ) : ?>
					<option value="<?php echo $value ?>" <?php selected( $related_category, $value, true ); ?>><?php echo esc_attr( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
<?php endif; ?>