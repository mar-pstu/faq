<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };



class pstuFAQPostTypeAdmin extends pstuFAQAbstractPart {




	public function run() {
		add_filter( 'post_updated_messages', array( $this, 'post_type_messages' ), 10, 1 );
	}



	public function post_type_messages( $messages ) {
		global $post, $post_ID;
		$messages[ $this->object->get( 'name' ) ] = array(
			0 => '',
			1 => sprintf( __( 'Запис оновлено. <a href="%s">Переглянути</a>', $this->domain ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Параметр оновлено', $this->domain ),
			3 => __( 'Параметр видалено.', $this->domain ),
			4 => __( 'Запис оновлено', $this->domain ),
			5 => isset($_GET['revision']) ? sprintf( __( '"Питання-відповідь" відновлено з редакції: %s', $this->domain ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
			6 => sprintf( __( '"Питання-відповідь" опублікована в списку. <a href="%s">Переглянути</a>', $this->domain ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( '"Питання-відповідь" збережено.', $this->domain ),
			8 => sprintf( __( 'Відправлено на перевірку. <a target="_blank" href="%s">Переглянути</a>', $this->domain ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Заплановано до публікації: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Переглянути</a>', $this->domain ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Чернетку оновлено. <a target="_blank" href="%s">Переглянути</a>', $this->domain ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		return $messages;
	}



}