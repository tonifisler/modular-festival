<?php

$timber = new \Timber\Timber();

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});

	return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		// add_theme_support( 'post-formats' );
		// add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
        add_action( 'admin_menu', array( $this, 'remove_from_menu' ) );
		parent::__construct();
	}

    function remove_from_menu() {
        $user = wp_get_current_user();
        if ( !in_array( 'administrator', (array) $user->roles ) ) {
            remove_menu_page('edit.php');
            remove_menu_page( 'edit-comments.php' );
            remove_menu_page( 'plugins.php' );
            remove_menu_page( 'tools.php' );
        }
    }

    function register_assets() {
        wp_register_script('polyfills', get_template_directory_uri() . '/build/js/polyfills.min.js', [], '1.1', true);
        wp_enqueue_script('polyfills');

        wp_register_script('vendors', get_template_directory_uri() . '/build/js/vendors.min.js', [], '1.1', true);
        wp_enqueue_script('vendors');

        wp_register_script('vendors.bunddle', get_template_directory_uri() . '/build/js/vendors.bundle.js', [], '1.1', true);
        wp_enqueue_script('vendors.bunddle');

        wp_register_script('app.bundle', get_template_directory_uri() . '/build/js/app.bundle.js', [], '1.1', true);
        wp_enqueue_script('app.bundle');

        wp_register_style('main', get_template_directory_uri() . '/build/css/main.css');
        wp_enqueue_style('main');
    }

	function register_post_types() {
        $event_labels = array(
            'name'               => _x( 'Events', 'post type general name', 'modular' ),
            'singular_name'      => _x( 'Event', 'post type singular name', 'modular' ),
            'menu_name'          => _x( 'Events', 'admin menu', 'modular' ),
            'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'modular' ),
            'add_new'            => _x( 'Add New', 'event', 'modular' ),
            'add_new_item'       => __( 'Add New Event', 'modular' ),
            'new_item'           => __( 'New Event', 'modular' ),
            'edit_item'          => __( 'Edit Event', 'modular' ),
            'view_item'          => __( 'View Event', 'modular' ),
            'all_items'          => __( 'All Events', 'modular' ),
            'search_items'       => __( 'Search Events', 'modular' ),
            'parent_item_colon'  => __( 'Parent Events:', 'modular' ),
            'not_found'          => __( 'No events found.', 'modular' ),
            'not_found_in_trash' => __( 'No events found in Trash.', 'modular' )
        );

        $event_args = array(
            'labels'             => $event_labels,
            'description'        => __( 'Description.', 'modular' ),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'event' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'           => 'dashicons-calendar',
            'supports'           => array( 'title', 'editor' )
        );

        register_post_type( 'event', $event_args );
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		return $context;
	}

	// function myfoo( $text ) {
	// 	$text .= ' bar!';
	// 	return $text;
	// }

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		// $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

}

new StarterSite();
