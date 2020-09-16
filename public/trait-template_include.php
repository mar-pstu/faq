<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Функции для выбора шаблонов в публичной часи сайта
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
trait TemplateInclude {


	/**
	 * Ищет шаблон для вывода контента в текущей теме
	 * @since    1.0.0
	 * @param    string|array  $file  имя файла
	 * @return   string               путь к файлу-шаблону
	 */
	public static function get_template_file_path( $file_names ) {
		$result = false;
		if ( ! is_array( $file_names ) ) {
			$file_names = [ $file_names ];
		}
		foreach ( $file_names as $file_name ) {
			$file_name = ltrim( $file_name, '/' );
			if ( ! empty( $file_name ) ) {
				$path = PSTU_FAQ_NAME . '-templates/' . $file_name;
				$path = get_stylesheet_directory() . '/' . $path;
				if ( file_exists( $path ) ) {
					$result = $path;
				} else {
					$path = get_template_directory() . '/' . $path;
					if ( file_exists( $path ) ) {
						$result = $path;
					} else {
						$path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $file_name;
						if ( file_exists( $path ) ) {
							$result = $path;
						}
					}
				}
			}
			if ( $result ) {
				break;
			}
		}
		return $result;
	}


	/**
	 * Выбирает шиблон для вывода контента
	 * @since    1.0.0
	 * @param    string $original_template шаблон для подключения
	 * @return   string                    шаблон для подключения
	 */
	public function choosing_template_to_include( string $original_template ) {
		$template = $original_template;
		if ( isset( $this->post_type_name ) ) {
			if ( is_post_type_archive( $this->post_type_name ) ) {
				$new_template = $this->get_template_file_path( [ "archive-{$this->post_type_name}.php" ] );
				if ( ! empty( $new_template ) ) {
					$template = $new_template;
				}
			} elseif ( is_singular( $this->post_type_name ) ) {
				$new_template = $this->get_template_file_path( [ "single-{$this->post_type_name}.php" ] );
				if ( ! empty( $new_template ) ) {
					$template = $new_template;
				}
			}
		} elseif ( isset( $this->taxonomy_name ) ) {
			if ( is_tax( $this->taxonomy_name ) && is_main_query() ) {
				$new_template = $this->get_template_file_path( 'archive-' . $this->taxonomy_name . '.php' );
				if ( empty( $new_template ) ) {
					$taxonomy = get_taxonomy( $this->taxonomy_name );
					if ( $taxonomy ) {
						$object_types = $taxonomy->object_type;
						do {
							$new_template = $this->get_template_file_path( 'archive-' . array_shift( $object_types ) . '.php' );
							if ( ! empty( $new_template ) ) {
								$template = $new_template;			
								break;
							}
						} while ( ! empty( $object_types ) );
					}
				} else {
					$template = $new_template;
				}
			}
		}
		return $template;
	}


}