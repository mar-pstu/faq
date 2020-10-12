<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


?>


<table cellspacing="0" style="border: 1px solid #bbbbbb; width: 100%;">

	<tbody>

		<?php if ( array_key_exists( 'user_ip', $_SESSION[ 'question_form' ][ 'form_data' ] ) && ! empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'user_ip' ] ) ) : ?>
			<tr>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px; width: 30%;">
					<?php _e( 'IP пользователя', PSTU_FAQ_NAME ); ?>
				</td>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px;">
					<?php echo $_SESSION[ 'question_form' ][ 'form_data' ][ 'user_ip' ]; ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( array_key_exists( 'name', $_SESSION[ 'question_form' ][ 'form_data' ] ) && ! empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ] ) ) : ?>
			<tr>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px; width: 30%;">
					<?php _e( 'Имя пользователя', PSTU_FAQ_NAME ); ?>
				</td>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px;">
					<?php echo $_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ]; ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( array_key_exists( 'email', $_SESSION[ 'question_form' ][ 'form_data' ] ) && ! empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] ) ) : ?>
			<tr>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px; width: 30%;">
					<?php _e( 'Email пользователя', PSTU_FAQ_NAME ); ?></td>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px;">
					<a href="mailto:<?php echo esc_attr( $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] ); ?>">
						<?php echo $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ]; ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( array_key_exists( 'category', $_SESSION[ 'question_form' ][ 'form_data' ] ) && ! empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ] ) ) : ?>
			<tr>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px; width: 30%;">
					<?php _e( 'Категория вопроса', PSTU_FAQ_NAME ); ?></td>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px;">
					<?php
						$term = get_term( $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ], 'faq_category', OBJECT, 'raw' );
						if ( is_object( $term ) && ! is_wp_error( $term ) ) {
							echo $term->name;
						} else {
							echo '-';
						}
					?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( array_key_exists( 'message', $_SESSION[ 'question_form' ][ 'form_data' ] ) && ! empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ] ) ) : ?>
			<tr>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px; width: 30%;">
					<?php _e( 'Сообщение', PSTU_FAQ_NAME ); ?>
				</td>
				<td style="border: 1px solid #bbbbbb; padding: 2px 5px;">
					<?php echo $_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ]; ?>
				</td>
			</tr>
		<?php endif; ?>

	</tbody>
</table>