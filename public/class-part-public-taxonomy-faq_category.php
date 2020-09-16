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
			$atts = shortcode_atts( [
				'term_id' => '',
				'empty'   => 'e-',
			], $atts, $shortcode_name );
			$atts[ 'term_id' ] = sanitize_key( $atts[ 'term_id' ] );
			if ( $atts[ 'term_id' ] ) {
				switch ( $key ) {

					case 'list_of_posts':
						$html = self::render_list_of_posts( $atts[ 'term_id' ], $this->plugin_name );
						break;
					
				}
				if ( empty( trim( $html ) ) ) {
					$html = $atts[ 'empty' ];
				}
			}
		}
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
	 * Подключает шаблон вывода одного терма в списке терминов таксономии "Категория вопроса-ответа"
	 * @param   WP_Term   $term   термин таксономии
	 * */
	public function render_term( $term ) {
		$file_path = $this->get_template_file_path( 'faq_category-term.php' );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
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
	 * Ищет и подключает шаблон начала списка
	 * */
	public function terms_before() {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-terms_before.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


	/**
	 * Ищет и подключает шаблон завершения списка
	 * */
	public function terms_after() {
		$file_path = $this->get_template_file_path( "taxonomy-{$this->taxonomy_name}-terms_after.php" );
		if ( ! empty( $file_path ) ) {
			include $file_path;
		}
	}


}