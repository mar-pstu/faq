<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Создаёт элементы формы и подключает необходимые для них скрипты
 *
 * @since      2.1.0
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Control {


	/**
	 * Имя плагина
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $plugin_name    Уникальный идентификтор плагина в контексте WP
	 */
	protected $plugin_name;


	/**
	 * Версия плагина
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version    Номер текущей версии плагина
	 */
	protected $version;


	/**
	 * Инициализация класса и установка его свойства.
	 *
	 * @since    2.1.0
	 * @param    string    $plugin_name       Имя плагин и слаг метаполей
	 * @param    string    $version           Текущая версия
	 * @param    string    $plugin_name       Имя плагин и слаг метаполей
	 * @param    string    $version           Текущая версия
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
	 * Формирует html-код текстового поля
	 * @param  string $value "значение" поля
	 * @param  array  $atts  атрибуты тега элемента
	 * @return string        html-код
	 */
	public static function render_textarea( $value = '', $atts = [] ) {
		return '<textarea ' . self::render_atts( $atts ) . ' >' . $value . '</textarea>';
	}


	/**
	 * Формирует html-код выпадающего списка
	 * @param  array  $choices           выринаты выбора ключ=>название
	 * @param  array  $selected          выбранные элементы
	 * @param  array  $atts              аттрибуты выпадающего списка
	 * @param  string $show_option_none  что показывать в пустом элементе
	 * @param  string $option_none_value значение пустого элемента
	 * @return string                    html-код выпадающего списка
	 */
	public static function render_dropdown( $choices = [], $selected = [], $atts = [], $show_option_none = '-', $option_none_value = '' ) {
		$html = '';
		if ( is_array( $choices ) && ! empty( $choices ) ) {
			if ( ! is_array( $selected ) ) {
				$selected = [ $selected ];
			}
			$atts = array_merge( [
				'data-selected' => ( empty( $selected ) ) ? '[]' : wp_json_encode( $selected ),
			], $atts );
			$html .= '<select ' . self::render_atts( $atts ) . ' >';
			if ( $show_option_none ) {
				$html .= sprintf( '<option value="%1$s">%2$s</option>', esc_attr( $option_none_value ), $show_option_none );
			}
			foreach ( $choices as $value => $label ) {
				$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', $value, selected( true, in_array( $value, $selected ), false ), $label );
			}
			$html .= '</select>';
		}
		return $html;
	}


	/**
	 * Формирует html-код выпадающего списка
	 * @param  array  $choices           выринаты выбора ключ=>название
	 * @param  array  $selected          выбранные элементы
	 * @param  array  $atts              аттрибуты выпадающего списка
	 * @param  string $show_option_none  что показывать в пустом элементе
	 * @param  string $option_none_value значение пустого элемента
	 * @return string                    html-код выпадающего списка
	 */
	public static function render_step_by_step_dropdown( $name, $choices = [], $selected = [], $atts = [], $show_option_none = '-', $option_none_value = '' ) {
		$html = '';
		if ( ! is_array( $selected ) ) {
			$selected = wp_parse_list( $selected );
		}
		if ( empty( $selected ) ) {
			$data_selected = '[]';
		} else {
			$data_selected = wp_json_encode( array_map( function ( $item ) {
				return array( 'value' => $selected );
			}, $selected ) );
		}
		if ( empty( $selected ) ) {
			$data_choices = '[]';
		} else {
			$data_choices = wp_json_encode( array_map( function ( $item ) {
				return array( 'value' => $selected );
			}, $selected ) );
		}
		if ( is_array( $choices ) && ! empty( $choices ) ) {
			$template = self::render_dropdown( [], '{{data.value.id}}', array_merge( [
				'data-selected' => '{{data.value.id}}',
				'class'  => 'form-control',
				'id'     => $name . '-{{data.value.id}}',
				'min'    => '1',
				'name'   => $name . '[]',
			], $atts ), $show_option_none, $option_none_value );
			ob_start();
			?>
				<div class="step-by-step-dropdown" data-step-by-step-dropdown="<?php echo $name; ?>" >
					<script type="text/javascript">
						var <?php echo $name; ?>_data = <?php echo $data_selected; ?>;
						var <?php echo $name; ?>_choices = <?php echo $data_choices; ?>;
					</script>
					<script type="text/html" id="tmpl-<?php echo $name; ?>">
						<?php echo $template; ?>
					</script>
					<div class="list"></div>
				</div>
			<?
			$html = ob_get_contents();
			ob_end_clean();
		}
		return $html;
	}


	public static function render_radiogroup( $name, $choices = [], $checked = [] ) {
		$html = '';
		if ( ! is_array( $checked ) ) {
			$checked = [ $checked ];
		}
		foreach ( $choices as $current => $label ) {
			$atts = [
				'name'    => $name,
				'value'   => $current,
				'checked' => in_array( $current, $checked ),
			];
			$html .= '<label class="radio">' . self::render_input( $plugin_name, $version, 'radio', $atts ) . ' ' . $label . '</label>';
		}
		return $html;
	}


	/**
	 * Формирует html-код элемента формы
	 * @since    2.1.0
	 * @param    string   $type   тип элемента формы
	 * @param    array    $atts   аттрибуты тега элемента формы
	 * @return   string           html-код
	 */
	public static function render_input( $type = 'text', $atts = [] ) {
		return '<input type="' . $type . '" ' . self::render_atts( $atts ) . ' >';
	}


	/**
	 * Формирует html-код "сборного поля"
	 * @since    2.1.0
	 * @param    string   $plugin_name   идентификатор плагина
	 * @param    string   $version       версия плагина
	 * @param    array    $controls      массив с информацией об вложенных элементах формы
	 * @param    array    $atts          массив оттрибутов
	 * @return   string                  html-код
	 */
	public static function render_composite( $plugin_name, $version, $controls = [], $atts = [] ) {
		$result = [];
		foreach ( $controls as $type => $args ) {
			$result[] = self::create_control( $plugin_name, $version, $type, $args );
		}
		if ( ! array_key_exists( 'class', $atts ) ) {
			$atts[ 'class' ] = '';
		}
		$atts[ 'class' ] .= ' composite-field';
		return ( empty( $result ) ) ? '' : '<div ' . self::render_atts( $atts ) . ' >' . implode( "\r\n", $result ) . '</div>';
	}


	/**
	 * Формирует html-код динамического списка элементов формы
	 * @since    2.1.0
	 * @param    string   $plugin_name   идентификатор плагина
	 * @param    string   $version       версия плагина
	 * @param    string   $template      шаблон для выода полей формы
	 * @param    string   $name          общее имя полей формы
	 * @param    array    $value         значение полей формы
	 * @param    array    $atts          массив дополнительных атриутов тега для обёртки
	 * @return   string                  html-код
	 */
	public static function render_list( $plugin_name, $version, $template, $name, $value = [], $atts = [] ) {
		if ( ! is_array( $value ) ) {
			$value = wp_parse_list( $value );
		}
		if ( empty( $value ) ) {
			$data = '[]';
		} else {
			$data = wp_json_encode( array_map( function ( $item ) {
				return array( 'value' => $item );
			}, $value ) );
		}
		if ( ! empty( trim( $template ) ) ) {;
			ob_start();
			?>
				<div class="list-of-templates" data-list-of-templates="<?php echo $name; ?>" >
					<script type="text/javascript">
						var <?php echo $name; ?>_data = <?php echo $data; ?>;
					</script>
					<div class="list"></div>
					<button  class="button button-primary add-button" type="button">
						<?php _e( 'Добавить строку', $plugin_name ); ?>
					</button>
					<script type="text/html" id="tmpl-<?php echo $name; ?>">
						<div class="list-item">
							<div class="template">
								<?php echo $template; ?>	
							</div>
							<button type="button" title="<?php esc_attr_e( 'Удалить строку', $plugin_name ); ?>" class="button remove-button">
								&times;
							</button>
						</div>
					</script>
				</div>
			<?
			$html = ob_get_contents();
			ob_end_clean();
		}
		return $html;
	}


	/**
	 * Общий метод для формирования элементов формы
	 * @since    2.1.0
	 * @param    string   $plugin_name   идегтификатор плагина
	 * @param    string   $version       версия плагина
	 * @param    string   $type          тип элемента формы
	 * @param    array    $args          массив дополнительных аргументов элемента
	 * @return   string                  html-код
	 */
	public static function create_control( $plugin_name, $version, $type = '', $args = [] ) {
		$result = '';
		switch ( $type ) {
			
			case 'list':
				$args = array_merge( [
					'template' => '',
					'name'     => 'list',
					'value'    => [],
					'atts'     => [],
				], $args );
				$result = self::render_list( $plugin_name, $version, $args[ 'template' ], $args[ 'name' ], $args[ 'value' ], $args[ 'atts' ] );
				break;

			case 'radiogroup':
				//
				break;

			case 'step_by_step_dropdown':
				$args = array_merge( [
					'name'     => 'name' . time(),
					'atts'     => [],
					'choices'  => [],
					'selected' => [],
					'show_option_none' => '-',
					'option_none_value' => '',
				], $args );
				$result = self::render_step_by_step_dropdown( $args[ 'name' ], $args[ 'choices' ], $args[ 'selected' ], $args[ 'atts' ], $args[ 'show_option_none' ], $args[ 'option_none_value' ] );
				break;

			case 'dropdown':
				$args = array_merge( [
					'atts'     => [],
					'choices'  => [],
					'selected' => [],
					'show_option_none' => '-',
					'option_none_value' => '',
				], $args );
				$result = self::render_dropdown( $args[ 'choices' ], $args[ 'selected' ], $args[ 'atts' ], $args[ 'show_option_none' ], $args[ 'option_none_value' ] );
				break;

			case 'composite':
				$args = array_merge( [
					'atts'     => [],
					'controls' => [],
				], $args );
				$result = self::render_composite( $plugin_name, $version, $args[ 'controls' ], $args[ 'atts' ] );
				break;

			case 'textarea':
				$args = array_merge( [
					'atts'  => [],
					'value' => '',
				], $args );
				$result = self::render_textarea( $args[ 'value' ], $args[ 'atts' ] );
				break;

			case 'checkbox':
			case 'radio':
			case 'text':
			case 'number':
			case 'email':
			case 'password':
			case 'hidden':
			case 'date':
			case 'text':
			default:
				$args = array_merge( [
					'atts' => [],
				], $args );
				$result = self::render_input( $type, $args[ 'atts' ] );
				break;

		}

		return $result;

	}


	/**
	 * Формирует html код аттрибутов элемента управления формы
	 * @since    2.1.0
	 * @param    array    $atts   ассоциативный массив аттрибут=>значение
	 * @return   string           html-код
	 */
	public static function render_atts( $atts ) {
		$html = '';
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $key => $value ) {
				$html .= ' ' . $key . '="' . $value . '"';
			}
		}
		return $html;
	}


	/**
	 * Функция для очистки массива параметров
	 * @since    2.1.0
	 * @param    array   $default             расзерённые парметры и стандартные значения
	 * @param    array   $args                неочищенные параметры
	 * @param    array   $sanitize_callback   одномерный массив с именами функция, с помощью поторых нужно очистить параметры
	 * @param    array   $required            обязательные параметры
	 * @param    array   $not_empty           параметры которые не могут быть пустыми
	 * @return   array                        возвращает ощиченный массив разрешённых параметров
	 */
	public function parse_only_allowed_args( $default, $args, $sanitize_callback = [], $required = [], $not_empty = [] ) {
		$args = ( array ) $args;
		$result = [];
		$count = 0;
		while ( ( $value = current( $default ) ) !== false ) {
			$key = key( $default );
			if ( array_key_exists( $key, $args ) ) {
				$result[ $key ] = $args[ $key ];
				if ( isset( $sanitize_callback[ $count ] ) && ! empty( $sanitize_callback[ $count ] ) ) {
					$result[ $key ] = $sanitize_callback[ $count ]( $result[ $key ] );
				}
			} elseif ( in_array( $key, $required ) ) {
				return null;
			} else {
				$result[ $key ] = $value;
			}
			if ( empty( $result[ $key ] ) && in_array( $key, $not_empty ) ) {
				return null;
			}
			$count = $count + 1;
			next( $default );
		}
		return $result;
	}


	/**
	 * Метод для отладки. Выводит информацию о переменной.
	 * @since    2.1.0
	 * @param    mixed     $var переменная
	 */
	protected static function var_dump( $var ) {
		echo "<pre>";
		var_dump( $var );
		echo "</pre>";
	}


	/**
	 * Регистрирует стили для "части" плагина
	 * @since    2.1.0
	 */
	public function admin_enqueue_styles() {
		wp_enqueue_style( "{$this->plugin_name}-control", plugin_dir_url( dirname( __FILE__ ) ) . 'admin/styles/admin-control.css', [], $this->version, 'all' );
	}


	/**
	 * Регистрирует скрипты для "части" плагина
	 * @since    2.1.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' ); 
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/styles/jquery-ui.css', [], '1.11.4', 'all' );
		wp_enqueue_media();
		wp_enqueue_script( "{$this->plugin_name}-control", plugin_dir_url( dirname( __FILE__ ) ) . 'admin/scripts/admin-control.js',  [ 'jquery', 'wp-color-picker' ], $this->version, false );
	}


}