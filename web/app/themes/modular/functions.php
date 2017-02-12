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
        add_action( 'admin_menu', array( $this, 'remove_default_post_type' ) );
		parent::__construct();
	}

    function remove_default_post_type() {
        remove_menu_page('edit.php');
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
		//this is where you can register custom post types
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
