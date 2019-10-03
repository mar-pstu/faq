<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };



class pstuFAQSettings extends pstuFAQAbstractPart {



	public function run() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}



	public function register_settings() {
		register_setting( $this->slug, $this->slug, array( $this, 'validate_settings' ) );
		//
		add_settings_section( "{$this->slug}_main", __( 'Налаштування публікації FAQ', $this->domain ), '', $this->slug );
		add_settings_field( 'page_id', __( 'Вибір сторніки для виводу записів', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_main", array(
			'type'      => 'select',
			'id'        => 'page_id',
			'desc'      => '',
			'vals'      => $this->get_pages(),
			'label_for' => 'page_id',
		) );
		//
		add_settings_section( "{$this->slug}_email", __( 'Налаштування email', $this->domain ), '', $this->slug );
		add_settings_field( 'Тема', __( 'Тема повідомлення', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_email", array(
			'type'      => 'text',
			'id'        => 'email_subject',
			'desc'      => '',
			'label_for' => 'email_subject',
		) );
		add_settings_field( 'emails', __( 'E-mail адміністраторів', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_email", array(
			'type'      => 'text',
			'id'        => 'emails',
			'desc'      => __( 'перерахувати через кому', $this->domain ),
			'label_for' => 'emails',
		) );
		add_settings_field( 'from_email', __( 'Від кого (email)', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_email", array(
			'type'      => 'text',
			'id'        => 'from_email',
			'desc'      => __( 'перерахувати через кому', $this->domain ),
			'label_for' => 'from_email',
		) );
		add_settings_field( 'reply_to', __( 'Reply-To (email)', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_email", array(
			'type'      => 'text',
			'id'        => 'reply_to',
			'desc'      => __( 'перерахувати через кому', $this->domain ),
			'label_for' => 'reply_to',
		) );
		if ( is_multisite() ) {
			add_settings_section( "{$this->slug}_multisite", __( 'Мультисайт', $this->domain ), '', $this->slug );
			add_settings_field( "non_public_site", __( 'Мультисайт', $this->domain ), array( $this, 'get_setting' ), $this->slug, "{$this->slug}_multisite", array(
				'type'      => 'checkbox',
				'id'        => 'non_public_site',
				'desc'      => __( 'Посты вопросов-ответов нельзя автоматически публиковать на других сайтах сети', $this->domain ),
				'label_for' => "non_public_site",
			) );
		}
	}



	/**
	 * Возвращает поле формы
	 *
	 * @param    array     $params    Параметры поля формы
	 * @return   string    $result    html
	 */
	public function get_setting( $args ) {
		extract( $args );
		$options = get_option( $this->slug );
		$value = ( isset( $options[ $id ] ) && $options[ $id ] ) ? esc_attr( $options[ $id ] ) : '';
		switch ( $type ) {
			case 'select':
				printf(
					'<select name="%1$s[%2$s]" id="%1$s_%2$s">%3$s</select>',
					$this->slug,
					$id,
					$this->render_options( $vals, $value ),
					( empty( $desc ) ) ? '' : '<p class="description">' . $desc . '</p>'
				);
				break;
			case 'text':
				printf(
					'<input type="text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" class="regular-text" id="%1$s_%2$s" /> %5$s',
					$this->slug,
					$id,
					$value,
					( empty( $placeholder ) ) ? '' : strip_tags( $placeholder ),
					( empty( $desc ) ) ? '' : '<p class="description">' . $desc . '</p>'
				);
				break;
			case 'checkbox':
				printf(
					'<p class="description"><input type="checkbox" name="%1$s[%2$s]" value="on" %3$s class="checkbox" id="%1$s_%2$s" /> %5$s</p>',
					$this->slug,
					$id,
					checked( $value, 'on', false ),
					( empty( $placeholder ) ) ? '' : strip_tags( $placeholder ),
					$desc
				);
				break;
		}
	}



	/**
	 * Валидация настроек
	 *
	 * @param    array     $options    массив опций формы
	 * @return   array     $result     $id => $value
	 */
	public function validate_settings( $options ) {
		$result = __return_empty_array();
		foreach ( $options as $key => $value ) {
			switch ( $key ) {
				case 'from_email':
				case 'reply_to':
				case 'emails':
					$result[ $key ] = $this->validate_email_list( $value );
					break;
				default:
					$result[ $key ] = sanitize_text_field( $value );
					break;
			}
		}
		return $result;
	}




	public function register_page() {
		add_submenu_page(
			"edit.php?post_type={$this->object->get( 'name' )}",
			__( 'Налаштування', $this->domain ),
			__( 'Налаштування', $this->domain ),
			'manage_options',
			$this->slug,
			function () {
				include PSTU_FAQ_VIEWS . 'settings-page.php';
			}
		);
	}



}