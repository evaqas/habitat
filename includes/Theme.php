<?php

namespace Habitat;

class Theme
{
    public static function init()
    {
        $theme = new self();

        add_action( 'after_setup_theme',  [ $theme, 'cleanup' ] );
        add_action( 'after_setup_theme',  [ $theme, 'setup' ] );
        add_action( 'after_setup_theme',  [ $theme, 'contentWidth' ], 0 );
        add_action( 'wp_enqueue_scripts', [ $theme, 'enqueueScripts' ] );
        add_action( 'wp_enqueue_scripts', [ $theme, 'enqueueStyles' ] );
    }


    public function enqueueScripts()
    {
        $entrypointsPath = get_theme_file_path('assets/dist/entrypoints.json');
        $entrypoints = file_exists( $entrypointsPath ) ? json_decode( file_get_contents( $entrypointsPath ), true )['entrypoints'] : [];

        foreach ( $entrypoints as $entry => $points ) {
            $funcName = "is_{$entry}";
            if ( $entry === 'main' ) {
                foreach ( $points['js'] as $i => $jsPath ) {
                    $handle = explode( '.', basename( $jsPath ) )[0];
                    wp_enqueue_script( "habitat/{$handle}", get_theme_file_uri( $jsPath ), [], null, true );
                }
            } else if ( function_exists( $funcName ) && call_user_func( $funcName ) ) {
                foreach ( $points['js'] as $i => $jsPath ) {
                    $handle = explode( '.', basename( $jsPath ) )[0];
                    wp_enqueue_script( "habitat/{$handle}", get_theme_file_uri( $jsPath ), [], null, true );
                }
            }
        }
    }


    public function enqueueStyles()
    {
        wp_enqueue_style( 'habitat/main', asset('css/main.css'), [], null );
    }


    public function setup()
    {
        load_theme_textdomain( 'habitat', get_template_directory() . '/languages' );

        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');

        register_nav_menus( [
            'main-menu'   => esc_html__( 'Pagrindinis meniu', 'habitat' ),
            'social-menu' => esc_html__( 'Soc. tinklų meniu', 'habitat' ),
        ] );
    }


    public function contentWidth()
    {
        $GLOBALS['content_width'] = apply_filters( 'habitat_content_width', 640 );
    }


    public function cleanup()
    {
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
        remove_action( 'wp_head', 'rest_output_link_wp_head' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'wp_head', 'feed_links_extra', 3 );

        add_filter( 'the_generator',     '__return_false' );
        add_filter( 'style_loader_src',  [ $this, 'removeVersion' ] );
        add_filter( 'script_loader_src', [ $this, 'removeVersion' ] );
    }


    public function removeVersion( $src )
    {
        if ( strpos( $src, 'ver=' . get_bloginfo('version') ) ) {
            $src = remove_query_arg( 'ver', $src );
        }

        return $src;
    }
}
