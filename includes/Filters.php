<?php

namespace Habitat;

class Filters
{
    public static function init()
    {
        $self = new self();

        add_filter( 'get_the_archive_title', [ $self, 'alterArchiveTitle' ] );
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
