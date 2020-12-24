<?php

namespace Habitat;

use Timber\Timber;
use Brain\Hierarchy\Hierarchy;

class Controller
{
    protected $context = [];


    public function __get( $name )
    {
        return isset( $this->context[ $name ] ) ? $this->context[ $name ] : null;
    }


    public function __construct()
    {
        $this->context = Timber::get_context();
        $this->setContextFromMethods();
    }


    public function pagination()
    {
        return is_home() || is_archive() ? Timber::get_pagination(4) : null;
    }


    public function posts()
    {
        return is_home() || is_archive() ? Timber::get_posts() : null;
    }


    public function post()
    {
        return is_singular() ? Timber::get_post() : null;
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
        }
    }


    public function view()
    {
        Timber::render( $this->getTemplates(), $this->getContext() );
    }


    protected function getTemplates()
    {
        global $wp_query;

        $hierarchy = new Hierarchy();

        $templates = array_map( function ( $filename ) {
            return basename( $filename, '.php' ) . '.php.twig';
        }, $hierarchy->getTemplates( $wp_query ) );

        return $templates;
    }


    protected function getContext()
    {
        return array_filter( $this->context, function ( $property ) {
            return ! is_null( $property );
        } );
    }


    protected function setContextFromMethods()
    {
        $class = new \ReflectionClass( $this );

        $methods = array_filter( $class->getMethods( \ReflectionMethod::IS_PUBLIC ), function ( $method ) {
            return $method->name !== '__construct'
                && $method->name !== 'view';
        } );

        $methods = array_reverse( $methods );

        foreach ( $methods as $method ) {
            $this->context[ $method->name ] = $this->{$method->name}();
        }
    }
}
