<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


class Control {


	/**
	 * Имя плагина и слаг метаполей
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    Уникальный идентификтор плагина в контексте WP
	 */
	protected $plugin_name;


	/**
	 * Версия плагина
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    Номер текущей версии плагина
	 */
	protected $version;


	/**
	 * Инициализация класса и установка его свойства.
	 *
	 * @since    1.0.0
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


	public static function render_checkbox_group( $name, $choices = [], $checked = [] ) {
		$html = '';
		if ( ! is_array( $checked ) ) {
			$checked = [ $checked ];
		}
		foreach ( $choices as $current => $label ) {
			$atts = [
				'name'    => $name . '[]',
				'value'   => $current,
			];
			if ( in_array( $current, $checked ) ) {
				$atts[ 'checked' ] = 'checked';
			}
			$html .= '<li><label class="checkbox">' . self::render_input( 'checkbox', $atts ) . ' ' . $label . '</label></li>';
		}
		return ( empty( $html ) ) ? '' : '<ul class="list-inline">' . $html . '</ul>';
	}


	public static function render_radio_group( $name, $choices = [], $checked = [] ) {
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
			$html .= '<label class="radio">' . self::render_input( 'radio', $atts ) . ' ' . $label . '</label>';
		}
		return $html;
	}


	/**
	 * Формирует html-код элемента формы
	 * @param  string $type тип элемента формы
	 * @param  array  $atts аттрибуты тега элемента формы
	 * @return string       html-код
	 */
	public static function render_input( $type = 'text', $atts = [] ) {
		return '<input type="' . $type . '" ' . self::render_atts( $atts ) . ' >';
	}


	/**
	 * Формирует html-код "сборного поля"
	 * @param  string $plugin_name идентификатор плагина
	 * @param  string $version     версия плагина
	 * @param  array  $controls    массив с информацией об вложенных элементах формы
	 * @param  array  $atts        массив оттрибутов
	 * @return string              html-код
	 */
	public static function render_composite( $plugin_name, $version, $controls = [], $atts = [] ) {
		$result = [];
		foreach ( $controls as $control ) {
			$control = array_merge( [
				'type' => '',
				'args' => [],
			], $control );
			$result[] = self::create_control( $plugin_name, $version, $control[ 'type' ], $control[ 'args' ] );
		}
		if ( ! array_key_exists( 'class', $atts ) ) {
			$atts[ 'class' ] = '';
		}
		$atts[ 'class' ] .= ' composite-field';
		return ( empty( $result ) ) ? '' : '<div ' . self::render_atts( $atts ) . ' >' . implode( "\r\n", $result ) . '</div>';
	}


	/**
	 * Формирует html-код галереи изображений
	 * @param  string $plugin_name идентификатор плагина
	 * @param  string $version     версия плагина
	 * @param  string $template    шаблон для выода полей формы
	 * @param  string $name        общее имя полей формы
	 * @param  array  $value       значение полей формы
	 * @param  array  $atts        массив дополнительных атриутов тега для обёртки
	 * @return string              html-код
	 */
	public static function render_image_gallery( $plugin_name, $version, $name, $value = [], $atts = [] ) {
		wp_enqueue_media();
		add_thickbox();
		$html = '';
		if ( ! is_array( $value ) ) {
			$value = wp_parse_list( $value );
		}
		$value = array_filter( array_map( function ( $attachment_id ) {
			$thumbnauil_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail', false );
			$full_url = wp_get_attachment_image_url( $attachment_id, 'full', false );
			return ( $thumbnauil_url && $full_url ) ? [
				'id'   => $attachment_id,
				'url'  => $thumbnauil_url,
				'full' => $full_url,
			] : null;
		}, $value ) );
		if ( empty( $value ) ) {
			$data = '[]';
		} else {
			$data = wp_json_encode( $value );
		}
		ob_start();
		?>
			<div class="image-gallery" data-image-gallery="<?php echo $name; ?>" >
				<script type="text/javascript">
					var <?php echo $name; ?>_data = <?php echo $data; ?>;
				</script>
				<div class="images"></div>
				<button  class="button button-primary add-button" type="button">
					<?php _e( 'Выбрать изображения', $plugin_name ); ?>
				</button>
				<script type="text/html" id="tmpl-<?php echo $name; ?>">
					<div class="gallery-item" data-image-id="{{data.id}}">
						<input type="hidden" name="<?php echo $name; ?>[]" value="{{data.id}}">
						<img src="{{data.url}}">
						<a type="button" class="thickbox" href="#TB_inline?&width=600&inlineId=full-image-{{data.id}}">🔎</a>
						<div id="full-image-{{data.id}}" style="display: none;">
							<img class="image-gallery-full-size" src="{{data.full}}">
						</div>
						<button type="button" title="<?php esc_attr_e( 'Удалить', $plugin_name ); ?>" class="button remove-button">&times;</button>
					</div>
				</script>
			</div>
		<?
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}


	/**
	 * Формирует html-код динамического списка элементов формы
	 * @param  string $plugin_name идентификатор плагина
	 * @param  string $version     версия плагина
	 * @param  string $template    шаблон для выода полей формы
	 * @param  string $name        общее имя полей формы
	 * @param  array  $value       значение полей формы
	 * @param  array  $atts        массив дополнительных атриутов тега для обёртки
	 * @return string              html-код
	 */
	public static function render_list( $plugin_name, $version, $template, $name, $value = [], $atts = [] ) {
		$html = '';
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
			$html .= ob_get_contents();
			ob_end_clean();
		}
		return $html;
	}


	/**
	 * Общий метод для формирования элементов формы
	 * @param  string $plugin_name идегтификатор плагина
	 * @param  string $version     версия плагина
	 * @param  string $type        тип элемента формы
	 * @param  array  $args        массив дополнительных аргументов элемента
	 * @return string              html-код
	 */
	public static function create_control( $plugin_name, $version, $type = '', $args = [] ) {
		$result = '';
		switch ( $type ) {

			case 'gallery':
				$result = self::render_image_gallery( $plugin_name, $version, $args[ 'name' ], $args[ 'value' ], $args[ 'atts' ] );
				break;
			
			case 'list':
				$args = array_merge( [
					'template' => '',
					'name'     => 'list',
					'value'    => [],
					'atts'     => [],
				], $args );
				$result = self::render_list( $plugin_name, $version, $args[ 'template' ], $args[ 'name' ], $args[ 'value' ], $args[ 'atts' ] );
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
	 * @param  array  $atts  ассоциативный массив аттрибут=>значение
	 * @return string        html-код
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
	 * @param  array $default           расзерённые парметры и стандартные значения
	 * @param  array $args              неочищенные параметры
	 * @param  array $sanitize_callback одномерный массив с именами функция, с помощью поторых нужно очистить параметры
	 * @param  array $required          обязательные параметры
	 * @param  array $not_empty         параметры которые не могут быть пустыми
	 * @return array                    возвращает ощиченный массив разрешённых параметров
	 */
	public static function parse_only_allowed_args( $default, $args, $sanitize_callback = [], $required = [], $not_empty = [] ) {
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
	 * @since    1.0.0
	 * @param    mixed     $var переменная
	 */
	protected static function var_dump( $var ) {
		echo "<pre>";
		var_dump( $var );
		echo "</pre>";
	}


	/**
	 * Регистрирует стили для "части" плагина
	 * @since    2.0.0
	 */
	public function admin_enqueue_styles() {
		wp_enqueue_style( "{$this->plugin_name}-control", plugin_dir_url( dirname( __FILE__ ) ) . 'admin/styles/admin-control.css', [], $this->version, 'all' );
	}


	/**
	 * Регистрирует скрипты для "части" плагина
	 * @since    2.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-datepicker' ); 
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/styles/jquery-ui.css', [], '1.11.4', 'all' );
		wp_enqueue_media();
		wp_enqueue_script( 'chomovva-admin-control', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/scripts/admin-control.js',  [ 'jquery', 'wp-color-picker' ], $this->version, false );
	}


	/**
	 * Проверяет авляется ли массив ассоциативным
	 * @param    array   $arr   массив для проверки
	 * @return   bool           результат проверки
	 * */
	public static function is_assoc( $arr = [] ) {
		if ( ! is_array( $arr ) ) {
			return false;
		}
		return ( count( array_filter( array_keys( $arr ),'is_string' ) ) == count( $arr ) );
	}


	/**
	 * Собирает со строки валидные email
	 * @param    sting|array   $emails   неочщенная строка
	 * @return   string                  строка с очищенными email
	 * */
	public static function parse_email_list( $emails = '' ) {
		return implode( ", ", array_filter( array_map( 'sanitize_email', wp_parse_list( $emails ) ) ) );
	}


}