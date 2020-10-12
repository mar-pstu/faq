<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Абстрактный класс "частей" плагина
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class PartPublicTaxonomyFAQCategory extends PartTaxonomyFAQCategory {


	use TemplateInclude;


	function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq_category_public';
	}


	/**
	 * Менеджер шорткодов, выбирает и запускает нужные методы
	 * @param  array       $atts           аргументы шорткода
	 * @param  string      $content        контент между "тегами" шорткодаты как
	 * @param  string|null $shortcode_name имя шорткода
	 * @return string                      html-код
	 */
	public function shortode_manager( $atts = [], $content = '', $shortcode_name = null ) {
		$html = $content;
		if ( null != $shortcode_name ) {
			$key = str_replace( $this->part_name . '_', '', $shortcode_name );
			switch ( $key ) {

				case 'list_of_posts':
						$atts = shortcode_atts( [
							'term_id' => '',
						], $atts, $shortcode_name );
						$atts[ 'term_id' ] = sanitize_key( $atts[ 'term_id' ] );
						if ( $atts[ 'term_id' ] ) {
							$html = self::render_list_of_posts( $atts[ 'term_id' ], $this->plugin_name );
						}
					break;

				case 'question_form':
					$html = self::render_question_form( $this->plugin_name );

			}
		}
		return $html;
	}


	public function question_form_run() {
		session_start();
		if (
			true
			&& $_SERVER[ 'REQUEST_METHOD' ] == 'POST'
			&& isset( $_POST[ 'question' ] )
			&& is_array( $_POST[ 'question' ] )
		) {
			$_SESSION[ 'question_form' ] = [
				'send_result' => false,
				'form_data'   => [
					'name'      => '',
					'email'     => '',
					'message'   => '',
					'check'     => md5( date( 'Y-m-d' ) ),
					'user_ip'   => self::get_user_ip(),
				],
				'warnings'    => [],
			];
			// проверка времени отправления
			if (
				! array_key_exists( 'check',  $_POST[ 'question' ] )
				|| ! $_SESSION[ 'question_form' ][ 'form_data' ][ 'check' ] == trim( sanitize_text_field( $_POST[ 'question' ][ 'check' ] ) )
			) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'check_fail';
			}
			// проверка имени пользователя
			if ( array_key_exists( 'name',  $_POST[ 'question' ] ) ) {
				$_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ] = trim( sanitize_text_field( $_POST[ 'question' ][ 'name' ] ) );
			}
			if ( empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ] ) ) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'no_name';
			}
			// проверка email
			if ( array_key_exists( 'email',  $_POST[ 'question' ] ) ) {
				$_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] = trim( sanitize_email( $_POST[ 'question' ][ 'email' ] ) );
			}
			if ( ! is_email( $_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ] ) ) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'no_email';
			}
			// проверка категории
			if ( array_key_exists( 'category',  $_POST[ 'question' ] ) ) {
				$_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ] = absint( $_POST[ 'question' ][ 'category' ] );
			}
			if ( ! $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ] ) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'no_category';
			}
			// проверка сообщения
			if ( array_key_exists( 'message',  $_POST[ 'question' ] ) ) {
				$_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ] = trim( sanitize_textarea_field( $_POST[ 'question' ][ 'message' ] ) );
			}
			if ( empty( $_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ] ) ) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'no_question';
			}
			// проверка по чёрноме списку
			if (
				wp_check_comment_disallowed_list(
					$_SESSION[ 'question_form' ][ 'form_data' ][ 'name' ],
					$_SESSION[ 'question_form' ][ 'form_data' ][ 'email' ],
					'',
					$_SESSION[ 'question_form' ][ 'form_data' ][ 'message' ],
					$_SESSION[ 'question_form' ][ 'form_data' ][ 'user_ip' ],
					''
				)
			) {
				$_SESSION[ 'question_form' ][ 'warnings' ][] = 'spam';
			} else {
				ob_start();
				include dirname( __FILE__ ) . '/partials/question-message.php';
				$message = ob_get_contents();
				ob_end_clean();
				$to = get_term_meta( $_SESSION[ 'question_form' ][ 'form_data' ][ 'category' ], 'mail_for_questions', true );
				$to = COntrol::parse_email_list( $to );
				if ( empty( $to ) ) {
					$to = get_bloginfo( 'admin_email', 'raw' );
				}
				$headers = 'Content-Type: text/html; charset=utf-8';
				$subject = sprintf( '%1$s %2$s', __( 'Сообщение с сайта', $this->plugin_name ), get_bloginfo( 'name', 'raw' ) );
				$_SESSION[ 'question_form' ][ 'send_result' ] = wp_mail( $to, $subject, $message, $headers );
				if ( ! $_SESSION[ 'question_form' ][ 'send_result' ] ) {
					$_SESSION[ 'question_form' ][ 'warnings' ] = 'send_fail';
				}
				$_SESSION[ 'question_form' ][ 'warnings' ] = array_unique( $_SESSION[ 'question_form' ][ 'warnings' ] );
			}
			wp_redirect( $_SERVER['HTTP_REFERER'], 302 );
			exit;
		}
	}


	/**
	 * Формтрует html-код формы или сообщение об успехной отправке формы
	 * */
	public static function render_question_form( $plugin_name ) {
		ob_start();
		$html = '';
		if ( array_key_exists( 'question_form', $_SESSION ) ) {
			if ( array_key_exists( 'send_result', $_SESSION[ 'question_form' ] ) && $_SESSION[ 'question_form' ][ 'send_result' ] ) {
				include dirname( __FILE__ ) . '/partials/question-form-success.php';
				unset( $_SESSION[ 'question_form' ] );
			} else {
				include dirname( __FILE__ ) . '/partials/question-form.php';
				unset( $_SESSION[ 'question_form' ] );
			}
		} else {
			include dirname( __FILE__ ) . '/partials/question-form.php';
		}
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}


	/**
	 * Формирует html-код списка постов в переданной категории
	 * @param    int      $term_id       идентификатор категории
	 * @param    string   $plugin_name   идентификатор категории
	 * @return   string                  html-код
	 * */
	public function render_list_of_posts( $term_id, $plugin_name ) {
		global $post;
		$result = [];
		$entries = get_posts( [
			'numberposts' => -1,
			'orderby'     => 'post_title',
			'order'       => 'DESC',
			'post_type'   => 'faq',
			'suppress_filters' => true,
			'tax_query'   => [
				'relation'  => 'AND',
				[
					'taxonomy' => $this->taxonomy_name,
					'field'    => 'term_id',
					'terms'    => $term_id,
					'operator' => 'IN',
				],
			],
		] );
		if ( is_array( $entries ) && ! empty( $entries ) ) {
			foreach ( $entries as $entry ) {
				setup_postdata( $post = $entry );
				$result[] = sprintf(
					'<li><a href="%1$s" %2$s >%3$s</a></li>',
					get_the_permalink( $post, false ),
					is_post_type_viewable( 'faq' ),
					get_the_title( $post->ID )
				);
			}
			wp_reset_postdata();
		}
		return ( empty( $result ) ) ? '<p>' . __( 'Вопросы-ответы не добавлены', $plugin_name ) . '</p>' : '<ul>' . implode( "\r\n", $result ) . '</ul>';
	}


	/**
	 * Возвращает стандартный логотип для таксономий
	 * @param    mixed    $value       значение поля
	 * @param    int      $term_id     идентификатор категории
	 * @return   mixed
	 * */
	public function get_default_logo( $value = '', $term_id = null ) {
		if ( empty( $value ) ) {
			$value = plugin_dir_url( __FILE__ ) . '/images/question.svg';
		}
		return $value;
	}


	/**
	 * Ищет и подключает шаблон начала обёртки для списка категорий вопросов-ответов
	 * */
	public function before_terms_list_wrap() {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-terms_list-before_loop.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Ищет и подключает шаблон закрытия обёртки для списка категорий вопросов-ответов
	 * */
	public function after_terms_list_wrap() {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-terms_list-after_loop.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Ищет и подключает шаблон отдельного категории
	 * */
	public function terms_list_item( $term ) {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-terms_list-item.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Подключает форму для задания вопроса
	 * */
	public function include_question_form() {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-question_form.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}



}