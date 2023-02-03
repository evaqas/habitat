<?php

namespace Habitat;

class Timber
{
    public static function setup()
    {
        $self = new self();

        add_filter( 'timber/twig/functions', [ $self, 'addTwigFunctions' ] );
        add_filter( 'timber/context',        [ $self, 'addMenus'] );
        add_filter( 'timber/context',        [ $self, 'addAcfOptions'] );
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
            $menu = \Timber::get_menu( $menu_slug );
            if ( ! $menu ) continue;
            $context[ str_replace( '-', '_', $menu_slug ) ] = $this->formatMenuItems( $menu->items );
        }

        $context['lang_menu'] = $this->getLanguagesMenu();

        return $context;
    }


    public function formatMenuItems( $items )
    {
        if ( ! is_array( $items ) ) return false;

        return array_map( function ( $item ) {

            $class = [];

            if ( $item->current )               $class[] = 'is-current';
            if ( $item->current_item_parent )   $class[] = 'is-parent';
            if ( $item->current_item_ancestor ) $class[] = 'is-ancestor';
            if ( $item->has_child_class )       $class[] = 'has-children';

            return [
                'ID'       => $item->object_id,
                'name'     => $item->name,
                'url'      => $item->url,
                'class'    => implode( ' ', $class ),
                'children' => $this->formatMenuItems( $item->children ),
            ];
        }, $items );
    }


    public function getLanguagesMenu()
    {
        if ( ! function_exists( 'pll_the_languages' ) ) {
            return [];
        }

        $langs = array_values( pll_the_languages( [ 'raw' => 1 ] ) );

        $current_lang = array_filter( $langs, function ( $lang ) {
            return $lang['current_lang'];
        } );
        $current_lang = array_shift( $current_lang );

        $sub_langs = array_filter( $langs, function ( $lang ) {
            return ! $lang['current_lang'];
        } );

        $current_lang['children'] = array_values( $sub_langs );

        return $current_lang;
    }
}
