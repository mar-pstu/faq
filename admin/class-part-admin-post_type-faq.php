<?php


namespace pstu_faq;


if ( ! defined( 'ABSPATH' ) ) {	exit; };


/**
 * Класс отвечающий за функциональность для
 * пользовательского типа записи "Объект недвижимости"
 * @since      2.1.0
 * @package    pstu_faq
 * @subpackage pstu_faq/admin
 * @author     chomovva <chomovva@gmail.com>
 * */
class PartAdminPostTypeFAQ extends PartPostTypeFAQ {


	/**
	 * Инициализация класса и установка его свойства.
	 *
	 * @since    2.1.0
	 * @param    string    $plugin_name       Имя плагин и слаг метаполей
	 * @param    string    $version           Текущая версия
	 * */
	public function __construct( $plugin_name, $version ) {
		parent::__construct( $plugin_name, $version );
		$this->part_name = 'faq_admin';
	}


	/**
	 * Регистрирует настройки плагина
	 * */
	public function register_settings() {
		register_setting( $this->post_type_name, $this->post_type_name, [ $this, 'sanitize_setting_callback' ] );
		add_settings_section( 'archive', __( 'Архив', $this->plugin_name ), [ $this, 'render_section_info' ], $this->post_type_name ); 
		add_settings_field( 'archive_description', __( 'Описание', $this->plugin_name ), [ $this, 'render_setting_field'], $this->post_type_name, 'archive', 'archive_description' );
		add_settings_section( 'main_content', __( 'Контейнер', $this->plugin_name ), [ $this, 'render_section_info' ], $this->post_type_name ); 
		add_settings_field( 'before_main_content', __( 'Код перед основным контейнером', $this->plugin_name ), [ $this, 'render_setting_field'], $this->post_type_name, 'main_content', 'before_main_content' );
		add_settings_field( 'after_main_content', __( 'Код после основного контейнера', $this->plugin_name ), [ $this, 'render_setting_field'], $this->post_type_name, 'main_content', 'after_main_content' );
	}


	/**
	 * Выводит html-код формы ввода настроек для таксономии
	 * @param    string    $page_slug    идентификатор страницы настроек
	 * */
	public function render_settings_form( string $page_slug ) {
		?>
			<form action="options.php" method="POST">
				<?php
					settings_fields( $this->post_type_name );
					do_settings_sections( $this->post_type_name );
					submit_button();
				?>
			</form>
		<?php
	}


	/**
	 * Описание секции настроек
	 * @param  [type] $section [description]
	 */
	public function render_section_info( $section ) {
		if ( null != $this->part_name ) {
			$file_path = dirname( __FILE__ ) . "/helpers/{$this->part_name}-section_info-{$section[ 'id' ]}.md";
			echo $this->get_parsedown_text( $file_path );
		}
	}


	/**
	 * Формирует и вывоит html-код элементов формы настроек плагина
	 * @since    1.0.0
	 * @param    string    $id       идентификатор опции
	 */
	public function render_setting_field( $id ) {
		$name = "{$this->post_type_name}[{$id}]";
		switch ( $id ) {
			case 'before_main_content':
			case 'after_main_content':
				echo Control::create_control(
					$this->plugin_name,
					$this->version,
					'textarea',
					[
						'value' => $this->post_type_option[ $id ],
						'atts'  => [
							'class' => 'form-control',
							'id'    => $id,
							'name'  => $name,
						],
					]
				);
				break;
			case 'archive_description':
				wp_editor( $this->post_type_option[ $id ], $id, [
					'wpautop'       => 0,
					'media_buttons' => 0,
					'textarea_name' => $name,
					'textarea_rows' => 5,
					'tabindex'      => null,
					'editor_css'    => '',
					'editor_class'  => 'form-editor',
					'teeny'         => 0,
					'dfw'           => 0,
					'tinymce'       => 1,
					'quicktags'     => 0,
					'drag_drop_upload' => false
				] );
				break;

		}
	}


	/**
	 * Очистка данных
	 * @since    1.0.0
	 * @var      array    $options
	 */
	public function sanitize_setting_callback( $options ) {
		$result = [];
		foreach ( $options as $name => &$value ) {
			$new_value = null;
			switch ( $name ) {
				case 'before_main_content':
				case 'after_main_content':
				case 'archive_description':
					$new_value = wp_kses_post( $value );
					break;
			}
			if ( null != $new_value && ! empty( $new_value ) ) {
				$result[ $name ] = $new_value;
			}
		}
		return $result;
	}


	/**
	 * Фильтр, который добавляет вкладку с опциями для текущего типа записи
	 * на страницу настроектплагина
	 * @since    1.0.0
	 * @param    array     $tabs     исходный массив вкладок идентификатор вкладки=>название
	 * @param    array     $slug     идентификатор объекта, который вызвал это событие
	 * @return   array     $tabs     отфильтрованный массив вкладок идентификатор вкладки=>название
	 */
	public function add_settings_tab( $tabs, string $slug = '' ) {
		$post_type = get_post_type_object( $this->post_type_name );
		if ( ! is_null( $post_type ) ) {
			$tabs[ $this->post_type_name ] = $post_type->labels->name;
		}
		return $tabs;
	}


}