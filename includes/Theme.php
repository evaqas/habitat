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
        add_action( 'wp_enqueue_scripts', [ $theme, 'enqueueAssets' ] );
    }


    public function enqueueAssets()
    {
        $needs_version = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        $entrypoints_path = get_theme_file_path( DIST_PATH . '/entrypoints.json' );
        $entrypoints = file_exists( $entrypoints_path ) ? json_decode( file_get_contents( $entrypoints_path ), true )['entrypoints'] : [];

        $enqueue_fns = [
            'js'  => 'wp_enqueue_script',
            'css' => 'wp_enqueue_style',
        ];

        foreach ( $entrypoints as $entry_name => $assets ) {

            list( $fn_name, $param_name ) = array_pad( explode( '-', $entry_name, 2 ), 2, null );
            $conditional_fn = "is_{$fn_name}";

            if ( $entry_name === 'main' || function_exists( $conditional_fn ) && call_user_func( $conditional_fn ) ) {
                foreach ( $assets as $asset_type => $assets ) {
                    if ( isset( $enqueue_fns[ $asset_type ] ) ) {

                        $asset_fn = $enqueue_fns[ $asset_type ];

                        foreach ( $assets as $asset_path ) {

                            $handle = explode( '.', basename( $asset_path ) )[0];

                            $params = [
                                "habitat/{$handle}",
                                asset_uri( $asset_path ),
                                [],
                                $needs_version ? filemtime( asset_path( $asset_path ) ) : null,
                            ];

                            if ( $asset_type === 'js' ) $params[] = true;

                            call_user_func_array( $asset_fn, $params );
                        }
                    }
                }
            }
        }
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
