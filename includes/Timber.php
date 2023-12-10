<?php

namespace Habitat;

class Timber
{
    public static function setup()
    {
        $self = new self();

        add_filter( 'timber/twig/functions', [ $self, 'addTwigFunctions' ] );
        add_filter( 'timber/twig/filters',   [ $self, 'addTwigFilters' ] );
        add_filter( 'timber/menuitem/class', [ $self, 'menuItemClass' ], 10, 3 );
    }


    public function menuItemClass( $class, $item, $menu )
    {
        return MenuItem::class;
    }


    public function addTwigFilters( $filters )
    {
        $filters['classname'] = [
            'callable' => function ( array $classes ) {
                return implode( ' ', array_filter( $classes ) );
            },
        ];

        return $filters;
    }


    public function addTwigFunctions( $functions )
    {
        $functions['asset'] = [ 'callable' => __NAMESPACE__ . '\\' . 'asset_uri' ];

        $functions['get_template_part'] = [
            'callable' => function ( string $slug, string $name = null, array $args = [] ) {
                var_dump( [ $slug, $name, $args ] );
                call_user_func_array( 'get_template_part', [ $slug, $name, $args ] );
            },
        ];

        return $functions;
    }
}
