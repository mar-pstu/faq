<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };






class pstuFAQShortcodeFormShow {



	use pstuFAQFunctions;



	protected $type;



	protected $category;



	function __construct( $slug, $version, $domain, $name ) {
		$this->slug = $slug;
 		$this->version = $version;
 		$this->domain = $domain;
 		$this->name = $name;
 		$this->type = 'faq';
 		$this->category = 'faq_category';
	}



	function run() {
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_assets' ), 10, 0 );
		add_action( 'init', array( $this, 'register_assets' ), 10, 0 );
		add_action( 'init', array( $this, 'register_blocks' ), 10, 0 );
		add_action( "wp_ajax_{$this->name}", array( $this, 'ajax_manager' ) );
	   	add_action( "wp_ajax_nopriv_{$this->name}", array( $this, 'ajax_manager' ) );
		add_shortcode( $this->name, array( $this, 'render_content' ) );
		add_shortcode( strtoupper( $this->name ), array( $this, 'render_content' ) );
	}



	public function enqueue_assets() {
		wp_register_style( 'fancybox', PSTU_FAQ_ASSETS . 'styles/fancybox.css', array(), '3.3.5', 'all' );
 		wp_register_script( 'fancybox', PSTU_FAQ_ASSETS . 'scripts/fancybox.js', array( 'jquery' ), '3.3.5', true );
		wp_register_script( "{$this->slug}-form-send", PSTU_FAQ_ASSETS . 'scripts/public-form-send.js', array( 'jquery', 'fancybox' ), $this->version, true );
		wp_localize_script( "{$this->slug}-form-send", $this->name, array(
			'method'        => 'POST',
			'action'        => $this->name,
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'security'      => wp_create_nonce( $this->name ),
			'success'       => __( 'Сообщение отправлено.', $this->domain ),
			'error'         => __( 'Произошла ошибка.', $this->domain ),
			'empty_message' => __( 'Зполните поле сообщение', $this->domain ),
		) );
	}



	protected function check_secure() {
		if ( ! check_ajax_referer( $this->name, 'security' ) ) {
			wp_send_json_error( 'secure' );
		}
		if ( isset( $_POST[ 'query' ] ) ) {
			if ( ! empty( $_POST[ 'query' ][ 'login' ] ) ) {
				wp_send_json_error( 'error' );
			}
		} else {
			wp_send_json_error( 'query' );
		}
	}



	protected function blacklist_check( $fields ) {
		return wp_blacklist_check(
			$fields[ 'yname' ],
			$fields[ 'yemail' ],
			'',
			$fields[ 'message' ],
			$this->get_the_user_ip(),
			''
		);
	}



	protected function get_fields() {
		$result = __return_empty_array();
		foreach ( array( 'yname', 'yemail', 'message', 'term_id' ) as $key ) {
			if ( isset( $_POST[ 'query' ][ $key ] ) ) {
				$value = $_POST[ 'query' ][ $key ];
				switch ( $key ) {
					case 'term_id':
						$result[ $key ] = sanitize_key( $value );
					case 'message':
						$result[ $key ] = strip_tags( sanitize_textarea_field( $value ) );
						break;
					case 'yemail':
						$result[ $key ] = sanitize_email( $value );
						break;
					default:
						$result[ $key ] = sanitize_text_field( $value );
						break;
				}
			} else {
				$result[ $key ] = '';
			}
		}
		return $result;
	}



	protected function get_term_email( $term_id ) {
		$result = __return_empty_array();
		$result[] = get_term_meta( $term_id, "{$this->slug}_email", true );
		$result[] = get_bloginfo( 'admin_email' );
		return implode( ", ", $result );
	}



	protected function get_message_content( $fields ) {
		ob_start();
		include PSTU_FAQ_VIEWS . 'message-content.php';
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}



	protected function send_mail( $fields ) {
		$subject = sprintf(
			'%1$s %2$s',
			__( 'Сообщение с сайта', $this->domain ),
			get_bloginfo( 'name' )
		);
		$headers = sprintf(
			'From: %1$s <%2$s>%3$sContent-type: text/html%3$scharset=utf-8%3$s',
			$fields[ 'yname' ],
			( is_email( $fields[ 'yemail' ] ) ) ? $fields[ 'yemail' ] : get_bloginfo( 'admin_email' ),
			"\r\n"
		);
		return wp_mail(
			$this->get_term_email( $fields[ 'term_id' ] ),
			$subject,
			$this->get_message_content( $fields ),
			$headers
		);
	}



	public function ajax_manager() {
		$this->check_secure();
		$fields = $this->get_fields();
		if ( empty( $fields[ 'message' ] ) ) {
			wp_send_json_error( __( 'Заполниите поле "Сообщение"', $this->domain ) );
		}
		if ( empty( $fields[ 'term_id' ] ) ) {
			wp_send_json_error( __( 'Выберите категорию вопроса', $this->domain ) );
		}
		if ( $this->blacklist_check( $fields ) ) {
			wp_send_json_error( __( 'Вы в "чёрном списке". Обратитесь к администратору сайта.', $this->domain ) );
		}
		if ( $this->send_mail( $fields ) ) {
			wp_send_json_success( __( 'Мы с вами обязательно свяжемся.', $this->domain ) );
		} else {
			wp_send_json_error( __( 'Попробуйте позже или Обратитесь к администратору.', $this->domain ) );
		}
		wp_die();
	}



	public function render_content( $atts ) {
		$atts = shortcode_atts( array(
			'term_id'       => '-1',
			'modal'         => '0',
		), $atts, $this->name );
		wp_enqueue_script( "{$this->slug}-form-send" );
		$terms = __return_empty_array();
		if ( '-1' == $atts[ 'term_id' ] ) {
			$terms = get_terms( array(
				'taxonomy'     => $this->category,
				'hide_empty'   => false,
				'fields'       => 'id=>name',
				'meta_query'   => array(
					'relation'     => 'OR',
					array(
						'key'      => "{$this->slug}_non_public",
						'compare'  => 'NOT EXISTS',
					),
					array(
						'key'      => "{$this->slug}_non_public",
						'compare'  => 'NOT LIKE',
						'value'    => 'on',
					),
				),
			) );
		}
		ob_start();
		include PSTU_FAQ_VIEWS . 'form.php';
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}



	public function register_assets() {
		wp_register_script(
			"{$this->slug}-editor-form-show",
			PSTU_FAQ_ASSETS . 'scripts/editor-form-show.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' ),
			$this->version,
			true
		);
	}



	public function register_blocks() {
		register_block_type( "pstu-faq/form-show", array(
			'editor_script' => "{$this->slug}-editor-form-show",
		) );
	}



}