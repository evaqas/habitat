<?php

namespace Habitat;

class Filters
{
    public static function init()
    {
        $self = new self();

        add_filter( 'get_the_archive_title', [ $self, 'alterArchiveTitle' ] );
        add_filter( 'timber/twig',           [ $self, 'addToTwig' ] );
        add_filter( 'timber/context',        [ $self, 'addMenus'] );
        add_filter( 'timber/context',        [ $self, 'addAcfOptions'] );
    }


    public function addToTwig( $twig ) {
        $twig->addFunction( new \Timber\Twig_Function( 'asset', 'Habitat\\asset_uri' ) );
        $twig->addFunction( new \Timber\Twig_Function( 'get_template_part', function ( $template, array $args = [] ) {
            array_unshift( $args, $template, null );
            call_user_func_array( 'get_template_part', $args );
        }, [ 'is_variadic' => true ] ) );
        return $twig;
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
            $menu = new \Timber\Menu( $menu_slug );
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


    public function alterArchiveTitle( $title )
    {
        if ( is_category() || is_tag() || is_tax() ) {
            $title = single_term_title( '', false );
        } elseif ( is_author() ) {
            $title = sprintf( __( 'Autoriaus %s įrašai:', 'habitat' ), '<span class="vcard">' . get_the_author() . '</span>' );
        } elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        }

        return $title;
    }
}
