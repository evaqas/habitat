<?php

namespace Habitat;

class Timber
{
    public static function setup()
    {
        $self = new self();

        add_filter( 'timber/twig/functions', [ $self, 'addTwigFunctions' ] );
        add_filter( 'timber/twig/filters',   [ $self, 'addTwigFilters' ] );
        add_filter( 'timber/context',        [ $self, 'addMenus'] );
        add_filter( 'timber/context',        [ $self, 'addAcfOptions'] );
    }


    public function addTwigFilters( $filters ) {
        $filters['classname'] = [
            'callable' => function ( array $classes ) {
                return implode( ' ', array_filter( $classes ) );
            },
        ];
        return $filters;
    }


    public function addTwigFunctions( $functions ) {
        $functions['asset'] = [ 'callable' => '\Habitat\\asset_uri' ];
        $functions['get_template_part'] = [
            'callable' => function ( string $slug, string $name = null, array $args = [] ) {
                var_dump( [ $slug, $name, $args ] );
                call_user_func_array( 'get_template_part', [ $slug, $name, $args ] );
            },
        ];
        return $functions;
    }


    public function addAcfOptions( $context )
    {
        if ( function_exists('get_fields') ) {
            $context['options'] = get_fields('option');
        }

        return $context;
    }


    public function addMenus( $context )
    {
        $menus = get_registered_nav_menus();

        foreach ( $menus as $menu_slug => $menu_title ) {
            if ( ! has_nav_menu( $menu_slug ) ) continue;
            $menu = \Timber::get_menu( $menu_slug );
            $context[ str_replace( '-', '_', $menu_slug ) ] = $this->formatMenuItems( $menu->items, $menu_slug );
        }

        $context['lang_menu'] = $this->getLanguagesMenu();

        return $context;
    }


    public function formatMenuItems( $items, $menu_slug )
    {
        if ( ! is_array( $items ) || empty( $items ) ) return false;

        return array_map( function ( $item ) use ( $menu_slug ) {

            $item_classes = [];

            $parsed_url = parse_url( $item->url );
            $item->current = $item->current && empty( $parsed_url['fragment'] );

            if ( $item->current )               $item_classes[] = 'is-current';
            if ( $item->current_item_parent )   $item_classes[] = 'is-parent';
            if ( $item->current_item_ancestor ) $item_classes[] = 'is-ancestor';
            if ( $item->has_child_class )       $item_classes[] = 'has-children';

            $item_classes = apply_filters( 'habitat/menu_item_classes', $item_classes, $item, $menu_slug );
            $link_classes = apply_filters( 'habitat/menu_link_classes', [], $item, $menu_slug );

            return [
                'ID'       => $item->object_id,
                'class'    => implode( ' ', $item_classes ),
                'children' => $this->formatMenuItems( $item->children, $menu_slug ),
                'current'  => (bool) $item->current,
                'link'     => [
                    'name'   => $item->name,
                    'url'    => $item->url,
                    'class'  => implode( ' ', $link_classes ),
                    'target' => $item->target === '_blank' ? '_blank' : '',
                ],
            ];
        }, $items );
    }


    public function getLanguagesMenu()
    {
        if ( ! function_exists( 'pll_the_languages' ) ) {
            return null;
        }

        $langs = pll_the_languages( [ 'raw' => 1 ] );

        return array_map( function ( $lang ) {
            return [
                'ID'   => $lang['id'],
                'link' => [
                    'name'  => strtoupper( $lang['slug'] ),
                    'url'   => $lang['url'],
                    'class' => $lang['current_lang'] ? 'is-current' : '',
                ],
            ];
        }, array_values( $langs ) );
    }
}
