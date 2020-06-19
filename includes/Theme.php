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

        add_filter( 'acf/settings/default_language', [ $theme, 'acfDefaultLanguage' ] );
        add_filter( 'acf/settings/current_language', [ $theme, 'acfCurrentLanguage' ] );
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


    public function acfDefaultLanguage( $lang )
    {
        return function_exists('pll_default_language') ? pll_default_language() : $lang;
    }


    public function acfCurrentLanguage( $lang )
    {
        return function_exists('pll_current_language') ? pll_current_language() : $lang;
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
