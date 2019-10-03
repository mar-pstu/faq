<?php


if ( ! defined( 'ABSPATH' ) ) { exit; };






class pstuFAQManager {



    protected $domain;



    protected $parts;



    protected $post_type;



    protected $taxonomy;



    function __construct ( $slug, $version, $domain, $post_type_name, $taxonomy_name ) {
        require_once PSTU_FAQ_INCLUDES . 'trait-functions.php';
        require_once PSTU_FAQ_INCLUDES . 'abstract-part.php';
        require_once PSTU_FAQ_INCLUDES . 'abstract-object.php';
        require_once PSTU_FAQ_INCLUDES . 'class-post-type.php';
        require_once PSTU_FAQ_INCLUDES . 'class-taxonomy.php';
        require_once PSTU_FAQ_INCLUDES . 'class-shortcode-the-category-posts.php';
        require_once PSTU_FAQ_INCLUDES . 'class-shortcode-form-show.php';
        $this->domain = $domain;
        $this->post_type = new pstuFAQPostType( $post_type_name, $domain );
        $this->taxonomy = new pstuFAQTaxonomy( $taxonomy_name, $domain );
        $this->parts[] = new pstuFAQShortcodeTheCategoryPosts( $slug, $version, $domain, 'faq_the_category_posts' );
        $this->parts[] = new pstuFAQShortcodeFormShow( $slug, $version, $domain, 'faq_form_show' );
    	if ( is_admin() ) {
            require_once PSTU_FAQ_INCLUDES . 'class-settings.php';
			require_once PSTU_FAQ_INCLUDES . 'class-post-type-admin.php';
            require_once PSTU_FAQ_INCLUDES . 'class-taxonomy-admin.php';
            $this->parts[] = new pstuFAQSettings( $slug, $version, $domain, $this->post_type );
            $this->parts[] = new pstuFAQPostTypeAdmin( $slug, $version, $domain, $this->post_type );
            $this->parts[] = new pstuFAQTaxonomyAdmin( $slug, $version, $domain, $this->taxonomy );
    	} else {
            require_once PSTU_FAQ_INCLUDES . 'class-post-type-public.php';
            require_once PSTU_FAQ_INCLUDES . 'class-taxonomy-public.php';
            $this->parts[] = new pstuFAQPostTypePublic( $slug, $version, $domain, $this->post_type );
            $this->parts[] = new pstuFAQTaxonomyPublic( $slug, $version, $domain, $this->taxonomy );
    	}
    }


    public function run() {
        add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
        add_action( 'init', array( $this, 'register_objects' ) );
        if ( is_array( $this->parts ) ) array_map( function( $part ) {
            $part->run();
        }, $this->parts );
    }



    public function textdomain() {
        load_plugin_textdomain(
            $this->domain,
            false,
            PSTU_FAQ_LANGUAGES
        );
    }



    public function register_objects() {
        $this->post_type->register();
        $this->taxonomy->register();
        register_taxonomy_for_object_type( $this->taxonomy->get( 'name' ), $this->post_type->get( 'name' ) );
    }



}