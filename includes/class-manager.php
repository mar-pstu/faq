<?php


namespace pstu_faq;


/**
 * Файл, который определяет основной класс плагина
 *
 * @link       https://pstu.edu/
 * @since      2.0.0
 *
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 */

/**
 * Основной класс плагина
 * @since      2.0.0
 * @package    pstu_faq
 * @subpackage pstu_faq/includes
 * @author     Your Name <chomovva@gmail.com>
 */
class Manager {


	/**
	 * Загрузчик, который отвечает за регистрацию всех хуков, фильтров и шорткодов.
	 * @since    2.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Регистрирует хуки, фильтры, шорткоды
	 */
	protected $loader;


	/**
	 * Уникальый идентификаторв плагина
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    Строка используется для идентификации плагина в Wp и интернационализации
	 */
	protected $plugin_name;


	/**
	 * Текущая версия плагина
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    Текущая версия плагина
	 */
	protected $version;


	/**
	 * Инициализация переменных плагина, подключение файлов.
	 * @since    2.0.0
	 */
	public function __construct() {
		$this->version = ( defined( 'PSTU_FAQ_VERSION' ) ) ? PSTU_FAQ_VERSION : '2.1.0';
		$this->plugin_name = ( defined( 'PSTU_FAQ_NAME' ) ) ? PSTU_FAQ_NAME : 'pstu_faq';
		$this->load_dependencies();
		$this->set_locale();
		$this->init();
		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->define_admin_hooks();
		} else {
			$this->define_public_hooks();
		}
	}


	/**
	 * Подключает файлы с "зависимостями"
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-part.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-part-post_type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-part-taxonomy.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-part-user_role.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-control.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-parsedown.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-part-post_type-faq.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-part-taxonomy-faq_category.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-part-user_role-faq_editor.php';

		/**
		 * Классы отвечающие за функционал админки
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-part-admin-readme_tab.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-part-admin-settings-manager.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-part-admin-post_type-faq.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-part-admin-taxonomy-faq_category.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-part-admin-user_role-faq_editor.php';

		/**
		 * Классы отвечающие за функционал публичной части сайта
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/trait-template_include.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-part-public-post_type-faq.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-part-public-taxonomy-faq_category.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-part-public-user_role-faq_editor.php';

		/**
		 * Класс, отвечающий за регистрацию хуков, фильтров и шорткодов.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		/**
		 * Класс отвечающий за интернализацию.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php';

		/**
		 * Касс, который регистрирует типов записей и таксономий.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-init.php';

		$this->loader = new Loader();

	}


	/**
	 * Добавлет функциональность для интернационализации.
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new I18n( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}


	/**
	 * Регистрирует новые типы постов и таксономии
	 * @since    2.0.0
	 * @access   private
	 */
	private function init() {

		$plugin_init = new Init( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_init, 'register_taxonomy_for_object_type', 20, 0 );

		$class_post_type_faq = new PartPostTypeFAQ( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $class_post_type_faq, 'register_post_type', 10, 0 );

		$class_taxonomy_faq_category = new PartTaxonomyFAQCategory( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $class_taxonomy_faq_category, 'register_taxonomy', 10, 0 );

		$class_user_role_faq_editor = new PartUserRoleFAQEditor( $this->get_plugin_name(), $this->get_version() );

	}


	/**
	 * Регистрация хуков и фильтров для админ части плагина
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		// элементы форм
		$object_control = new Control( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $object_control, 'admin_enqueue_styles', 10, 0 );
		$this->loader->add_action( 'admin_enqueue_scripts', $object_control, 'admin_enqueue_scripts', 10, 0 );

		// страница настроек плагина
		$class_part_settings_manager = new PartAdminSettingsManager( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $class_part_settings_manager, 'add_page' );
		$this->loader->add_action( 'current_screen', $class_part_settings_manager, 'run_tab' );
		$this->loader->add_action( 'admin_init', $class_part_settings_manager, 'register_settings', 10, 0 );
		$this->loader->add_action( 'wp_ajax_' . $this->get_plugin_name() . '_settings', $class_part_settings_manager, 'run_ajax', 20, 0 );

		// вывод справки по плагину
		$class_part_readme_tab = new PartAdminReadmeTab( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( $this->get_plugin_name() . '_settings-tabs', $class_part_readme_tab, 'add_settings_tab', 10, 1 );
		$this->loader->add_action( $this->get_plugin_name() . '_settings-form_' . $class_part_readme_tab->get_part_name(), $class_part_readme_tab, 'render_tab', 10, 1 );

		// тип поста "Вопросы-ответы"
		$class_post_type_faq = new PartAdminPostTypeFAQ( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( $this->get_plugin_name() . '_register_settings', $class_post_type_faq, 'register_settings', 10, 1 );
		$this->loader->add_filter( $this->get_plugin_name() . '_settings-tabs', $class_post_type_faq, 'add_settings_tab', 10, 2 );
		$this->loader->add_action( $this->get_plugin_name() . '_settings-form_' . $class_post_type_faq->get_post_type_name(), $class_post_type_faq, 'render_settings_form', 10, 1 );

		// таксономия "Научный совет"
		$class_taxonomy_faq_category = new PartAdminTaxonomyFAQCategory( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $class_taxonomy_faq_category, 'add_meta_box', 10, 1 );
		$this->loader->add_action( 'save_post', $class_taxonomy_faq_category, 'set_object_terms', 10, 2 );
		$this->loader->add_action( 'create_' . $class_taxonomy_faq_category->get_taxonomy_name(), $class_taxonomy_faq_category, 'save_term_fields', 10, 1 );
		$this->loader->add_action( 'edited_' . $class_taxonomy_faq_category->get_taxonomy_name(), $class_taxonomy_faq_category, 'save_term_fields', 10, 1 );
		$this->loader->add_action( 'manage_edit-' . $class_taxonomy_faq_category->get_taxonomy_name() . '_columns', $class_taxonomy_faq_category, 'add_columns', 10, 1 );
		$this->loader->add_action( 'manage_' . $class_taxonomy_faq_category->get_taxonomy_name() . '_custom_column', $class_taxonomy_faq_category, 'render_custom_columns', 10, 3 );


		// роль пользователя "Редактор вопросов-ответов"
		$class_user_role_faq_editor = new PartAdminUserRoleFAQEditor( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $class_user_role_faq_editor, 'add_capabilities', 10, 0 );
		$this->loader->add_action( 'pre_get_posts', $class_user_role_faq_editor, 'posts_of_the_current_user', 10, 1 );
		$this->loader->add_action( 'admin_menu', $class_user_role_faq_editor, 'remove_menus', 10, 0 );
		$this->loader->add_action( 'admin_init', $class_user_role_faq_editor, 'menus_redirect', 10, 0 );

	}


	/**
	 * Регистрация хуков и фильтров для публично части плагина
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		// роль пользователя "Редактор вопросов-ответов"
		$class_user_role_science_counsil_editor = new PartPublicUserRoleFAQEditor( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'login_redirect', $class_user_role_science_counsil_editor, 'login_redirect_filter', 10, 3 );
		$this->loader->add_filter( 'ajax_query_attachments_args', $class_user_role_science_counsil_editor, 'attachments_of_the_current_user', 10, 1 );
		
		// тип поста "Вопросы-ответы"
		$class_post_type_faq = new PartPublicPostTypeFAQ( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'template_include', $class_post_type_faq, 'choosing_template_to_include', 10, 1 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_loop', $class_post_type_faq, 'include_post_loop_template', 10, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_post_loop', $class_post_type_faq, 'include_post_loop_template', 10, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_before_main_content', $class_post_type_faq, 'before_main_content', 10, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_after_main_content', $class_post_type_faq, 'after_main_content', 10, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_before_loop', $class_post_type_faq, 'include_taxonomies_list', 10, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_before_loop', $class_post_type_faq, 'before_list_wrap', 20, 0 );
		$this->loader->add_action( $class_post_type_faq->get_post_type_name() . '_after_loop', $class_post_type_faq, 'after_list_wrap', 20, 0 );
		
		// таксономия "Категори вопросов-ответов"
		$class_taxonomy_faq_category = new PartPublicTaxonomyFAQCategory( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'template_include', $class_taxonomy_faq_category, 'choosing_template_to_include', 10, 1 );
		$this->loader->add_shortcode( $class_taxonomy_faq_category->get_part_name() . '_list_of_posts', $class_taxonomy_faq_category, 'shortode_manager', 10, 3 );
		$this->loader->add_action( 'taxonomy-' . $class_taxonomy_faq_category->get_taxonomy_name() . '-terms_before', $class_taxonomy_faq_category, 'terms_before', 10, 0 );
		$this->loader->add_action( $class_taxonomy_faq_category->get_taxonomy_name() . '-render_term', $class_taxonomy_faq_category, 'render_term', 10, 1 );
		$this->loader->add_action( 'taxonomy-' . $class_taxonomy_faq_category->get_taxonomy_name() . '-terms_after' . '-render_term', $class_taxonomy_faq_category, 'terms_after', 10, 0 );
		$this->loader->add_filter( $class_taxonomy_faq_category->get_taxonomy_name() . '_logo', $class_taxonomy_faq_category, 'get_default_logo', 10, 2 );

	}


	/**
	 * Запск загрузчика для регистрации хукой, фильтров и шорткодов в WordPress
	 * @since    2.1.0
	 */
	public function run() {
		$this->loader->run();
	}


	/**
	 * Возвращает имя плагина используется для уникальной идентификации его в контексте
	 * WordPress и для определения функциональности интернационализации.
	 * @since     2.0.0
	 * @return    string    Идентификатор плагина
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * Возвращает ссылку на класс, который управляет хуками с плагином.
	 * @since     2.0.0
	 * @return    Loader    Класс "загрузчик" хуков, фильтров и шорткодов.
	 */
	public function get_loader() {
		return $this->loader;
	}


	/**
	 * Возвращает номер версии плагина. Используется при регистрации файлов
	 * скриптов, стилей и обновлении плагина.
	 * @since     2.1.0
	 * @return    string    Номер текущей версии плагина
	 */
	public function get_version() {
		return $this->version;
	}


}