<?php

namespace Habitat;

class App extends Context
{
    public static function init()
    {
        add_action( 'wp', function () {
            $self = new self();
            $self->addMenus();
            add_filter( 'timber/context', [ $self, 'mergeContext' ] );
        } );
    }


    public function page_title()
    {
        if ( is_singular() ) {
            return get_the_title();
        } else if ( is_home() && ! is_front_page() ) {
            return single_post_title( '', false );
        } else if ( is_archive() ) {
            return get_the_archive_title();
        } else if ( is_search() ) {
            return sprintf( esc_html__( 'Paieškos rezultatai pagal „%s“', 'habitat' ), '<span>' . get_search_query() . '</span>' );
        } else if ( is_404() ) {
            return __( 'Nerasta', 'habitat' );
        }
    }


    public function options()
    {
        return function_exists('get_fields') ? get_fields('option') : null;
    }


    protected function addMenus()
    {
        $menus = get_registered_nav_menus();

        foreach ( $menus as $menu_slug => $menu_title ) {
            if ( ! has_nav_menu( $menu_slug ) ) continue;
            $menu = \Timber::get_menu( $menu_slug );
            $this->addToContext( str_replace( '-', '_', $menu_slug ), $menu->get_items() );
        }
    }


    public function lang_menu()
    {
        if ( ! function_exists( 'pll_the_languages' ) ) {
            return null;
        }

        $langs = pll_the_languages( [ 'raw' => 1 ] );

        return array_map( function ( $lang ) {

            $item = [
                'name'      => strtoupper( $lang['slug'] ),
                'link_atts' => [
                    'href' => $lang['url'],
                ],
            ];

            if ( $lang['current_lang'] ) {
                $item['atts']['class'] = 'is-current';
            }

            return $item;

        }, array_values( $langs ) );
    }
}
