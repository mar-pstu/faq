<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>

<form>

	<div>
		<input type="hidden" name="login" value="">
	</div>

	<div>
		<?php if ( is_array( $terms ) && ! empty( $terms ) ) : ?>
			<select name="term_id" required="required">
				<option value=""><?php esc_attr_e( 'Не выбрано', $this->domain ); ?></option>
				<?php foreach ( $terms as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo strip_tags( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php else : ?>
			<input type="hidden" name="term_id" value="<?php echo esc_attr( $atts[ 'term_id' ] ); ?>" readonly="readonly">
		<?php endif; ?>
	</div>

	<div>
		<input type="text" name="yname" value="" placeholder="<?php _e( 'Ваше имя', $this->domain ); ?>">
	</div>

	<div>
		<input type="email" name="yemail" value="" placeholder="<?php _e( 'Ваш email', $this->domain ); ?>">
	</div>

	<div>
		<textarea name="message" rows="10" style="width: 250px;" placeholder="<?php _e( 'Сообщение', $this->domain ); ?>" required></textarea>
	</div>

	<div>
		<button type="submit"><?php _e( 'Отправить', $this->domain ); ?></button>
	</div>

</form>