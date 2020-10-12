<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


?>


<br />


<form method="post">

	<?php
		if (
			true
			&& array_key_exists( 'question_form', $_SESSION )
			&& array_key_exists( 'warnings', $_SESSION[ 'question_form' ] )
			&& is_array( $_SESSION[ 'question_form' ][ 'warnings' ] )
			&& ! empty( $_SESSION[ 'question_form' ][ 'warnings' ] )
	) : ?>
		<ul>
			<?php foreach ( $_SESSION[ 'question_form' ][ 'warnings' ] as $warning ) : ?>
				<li class="text-warning">
					<?php
						switch ( $warning ) {
							case 'check_fail':
								_e( 'Обновите страницу и попробуйте ещё раз.', PSTU_FAQ_NAME );
								break;
							case 'no_name':
								_e( 'Введите Ваше имя', PSTU_FAQ_NAME );
								break;
							case 'no_email':
								_e( 'Введите Ваш email', PSTU_FAQ_NAME );
								break;
							case 'no_category':
								_e( 'Выберите категорию вопроса', PSTU_FAQ_NAME );
								break;
							case 'no_question':
								_e( 'Задайте вопрос', PSTU_FAQ_NAME );
								break;
							case 'spam':
								_e( 'Подозрение на спам! Обратитесь к администратору сайта!', PSTU_FAQ_NAME );
								break;
							default:
								printf( __( 'Ошибка "%s": обратитесь к администратору!', PSTU_FAQ_NAME ), $warning );
								break;
						}
					?>
				</li>
			<?php endforeach; ?>	
		</ul>
	<?php endif; ?>
	
	<?php
		echo Control::render_input( 'hidden', [
			'id'    => 'question-form-check',
			'name'  => 'question[check]',
			'value' => ( isset( $_SESSION[ 'question_form' ][ 'form_data' ][ 'check' ] ) ) ? esc_attr( $_SESSION[ 'question_form' ][ 'form_data' ][ 'check' ] ) : '',
			'required' => 'required',
		] );
	?>

	<div>
		<label for="question-form-name"><?php _e( 'Ваше имя', PSTU_FAQ_NAME ); ?></label>
		<?php
			echo Control::render_input( 'text', [
				'id'    => 'question-form-name',
				'name'  => 'question[name]',
				'value' => ( isset( $_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ] ) ) ? esc_attr( $_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ] ) : '',
				'required' => 'required',
			] );
		?>
	</div>

	<div>
		<label for=""><?php _e( 'Ваш email', PSTU_FAQ_NAME ); ?></label>
		<?php
			echo Control::render_input( 'email', [
				'id'    => 'question-form-email',
				'name'  => 'question[email]',
				'value' => ( isset( $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] ) ) ? esc_attr( $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] ) : '',
				'required' => 'required',
			] );
		?>
	</div>

	<div>
		<label for="question-form-category"><?php _e( 'Категория вопроса', PSTU_FAQ_NAME ); ?></label>
		<?php
			$categories = get_terms( [
				'hide_empty' => false,
				'taxonomy'   => 'faq_category',
			], '' );
			if ( is_array( $categories ) &&  ! empty( $categories ) ) {
				echo Control::render_dropdown(
					wp_list_pluck( $categories, 'name', 'term_id' ),
					( isset( $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ] ) ) ? [ $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ] ] : [],
					[
						'id'    => 'question-form-category',
						'name'  => 'question[category]',
						'required' => 'required',
						'class' => 'form-control',
					]
				);
			} else {
				echo Control::render_input( 'hidden', [
					'id'    => 'question-form-category',
					'name'  => 'question[category]',
					'value' => '0',
				] );
			}
		?>
	</div>

	<div>
		<label for="question-form-message"><?php _e( 'Сообщение', PSTU_FAQ_NAME ); ?></label>
		<?php
			echo Control::render_textarea( ( isset( $_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ] ) ) ? $_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ] : '', [
				'id'    => 'question-form-message',
				'name'  => 'question[message]',
				'required' => 'required',
				'class' => 'form-control',
			] );
		?>
	</div>

	<button type="submit" class="btn btn-primary"><?php _e( 'Задать вопрос', PSTU_FAQ_NAME ); ?></button>

</form>

<br />