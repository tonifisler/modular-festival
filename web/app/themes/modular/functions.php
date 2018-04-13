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

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
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
        add_action( 'after_setup_theme', array( $this, 'custom_picture_formats' ) );
        add_filter('acf/settings/save_json', array($this, 'settings_save_json'));
		add_filter('acf/settings/load_json', array($this, 'settings_load_json'));
		add_filter( 'wpcf7_form_tag', array($this, 'ses_add_workshops_list_to_contact_form'), 10, 2);
		parent::__construct();
	}

	function ses_add_workshops_list_to_contact_form($tag, $unused) {

	    if ( $tag['name'] != 'workshop' )
	        return $tag;

	    $args = array ( 'post_type' => 'workshop', 'orderby' => 'time', 'order' => 'ASC' );
	    $workshops = get_posts($args);

	    if ( ! $workshops )
	        return $tag;

	    foreach ( $workshops as $workshop ) {
	        $tag['raw_values'][] = $workshop->post_title;
	        $tag['values'][] = $workshop->ID;
            $time = get_post_field('time', $workshop->ID);
	        $tag['labels'][] = "$workshop->post_title – $time";
	        // $tag['pipes']->pipes[] = array ( 'before' => $workshop->post_title, 'after' => $workshop->post_title);
	    }

	    return $tag;
	}

    function custom_picture_formats() {
        add_image_size('artist_square', 500, 500, 1);
        add_image_size('partner_logo', 500, 110);
    }

    // Got this from WPML
    function settings_save_json( $path ) {
		// bail early if dir does not exist
		if( !is_writable($path) ) {
			return $path;
		}
		// remove trailing slash
		$path = untrailingslashit( $path );
		// ammend
		$path = $path . '/' . $this->lang;
		// make dir if does not exist
		if( !file_exists($path) ) {
			mkdir($path, 0777, true);
		}
		// return
		return $path;
	}

    function settings_load_json( $paths ) {
        if( !empty($paths) ) {
            foreach( $paths as $i => $path ) {
                // remove trailing slash
                $path = untrailingslashit( $path );
                // ammend
                $paths[ $i ] = $path . '/' . $this->lang;
            }
        }
        // return
        return $paths;

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
        $artist_labels = array(
            'name'               => _x( 'Artists', 'post type general name', 'modular' ),
            'singular_name'      => _x( 'Artist', 'post type singular name', 'modular' ),
            'menu_name'          => _x( 'Artists', 'admin menu', 'modular' ),
            'name_admin_bar'     => _x( 'Artist', 'add new on admin bar', 'modular' ),
            'add_new'            => _x( 'Add New', 'artist', 'modular' ),
            'add_new_item'       => __( 'Add New Artist', 'modular' ),
            'new_item'           => __( 'New Artist', 'modular' ),
            'edit_item'          => __( 'Edit Artist', 'modular' ),
            'view_item'          => __( 'View Artist', 'modular' ),
            'all_items'          => __( 'All Artists', 'modular' ),
            'search_items'       => __( 'Search Artists', 'modular' ),
            'parent_item_colon'  => __( 'Parent Artists:', 'modular' ),
            'not_found'          => __( 'No artists found.', 'modular' ),
            'not_found_in_trash' => __( 'No artists found in Trash.', 'modular' )
        );

        $artist_args = array(
            'labels'             => $artist_labels,
            'description'        => __( 'Description.', 'modular' ),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'artist' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'           => 'dashicons-microphone',
            'supports'           => array( 'title', 'editor' )
        );

        register_post_type( 'artist', $artist_args );

		$workshops_label = array(
            'name'               => _x( 'Workshops', 'post type general name', 'modular' ),
            'singular_name'      => _x( 'Workshop', 'post type singular name', 'modular' ),
            'menu_name'          => _x( 'Workshops', 'admin menu', 'modular' ),
            'name_admin_bar'     => _x( 'Workshop', 'add new on admin bar', 'modular' ),
            'add_new'            => _x( 'Add New', 'workshop', 'modular' ),
            'add_new_item'       => __( 'Add New Workshop', 'modular' ),
            'new_item'           => __( 'New Workshop', 'modular' ),
            'edit_item'          => __( 'Edit Workshop', 'modular' ),
            'view_item'          => __( 'View Workshop', 'modular' ),
            'all_items'          => __( 'All Workshops', 'modular' ),
            'search_items'       => __( 'Search Workshops', 'modular' ),
            'parent_item_colon'  => __( 'Parent Workshops:', 'modular' ),
            'not_found'          => __( 'No workshops found.', 'modular' ),
            'not_found_in_trash' => __( 'No workshops found in Trash.', 'modular' )
        );

        $workshops_args = array(
            'labels'             => $workshops_label,
            'description'        => __( 'Description.', 'modular' ),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'workshop' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'           => 'dashicons-edit',
            'supports'           => array( 'title', 'editor' )
        );

        register_post_type( 'workshop', $workshops_args );
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
        $context['options'] = get_fields('options');
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		return $context;
	}

    function kint($input) {
      d($input);
    }

	function format_int($number) {
		$int = strlen($number) < 2 ? "0$number" : $number;
		return $int;
	}

    function the_content_filter($content) {
        return apply_filters( 'the_content', $content );
    }

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter( 'kint', new Twig_SimpleFilter('kint', array($this, 'kint')));
        $twig->addFilter( 'format_int', new Twig_SimpleFilter('format_int', array($this, 'format_int')));
        $twig->addFilter( 'the_content_filter', new Twig_SimpleFilter('the_content', array($this, 'the_content_filter')));
		// $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

}

new StarterSite();
