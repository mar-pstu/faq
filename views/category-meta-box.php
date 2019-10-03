<select name="<?php echo $slug; ?>" id="<?php echo $slug; ?>_control" style="display: block; width: 100%;">
	<?php echo $this->render_options( $this->get_categories( $this->object->get( 'name' ) ), $current_category ); ?>
</select>