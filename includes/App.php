<?php

namespace Habitat;

class App extends Context
{
    public static function init()
    {
        add_action( 'wp', function () {
            $self = new self();
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
}
