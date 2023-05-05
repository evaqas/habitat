<?php

namespace Habitat;

class MenuItem extends \Timber\MenuItem
{
    public function __construct( \WP_Post $data, $menu = null )
    {
        // init
        parent::__construct( $data, $menu );

        // reset
        $this->classes = [];
        $this->update_class();

        $item = $this->wp_object;

        // add custom class, which is always a first value
        if ( ! empty( $item->classes[0] ) ) $this->add_class( $item->classes[0] );
        if ( $this->is_current() )          $this->add_class('is-current');
        if ( $this->is_parent() )           $this->add_class('is-parent');
        if ( $this->is_ancestor() )         $this->add_class('is-ancestor');
        if ( $this->has_children() )        $this->add_class('has-children');
        if ( $this->is_expanded() )         $this->add_class('is-expanded');
    }


    public function atts()
    {
        $atts = [];

        if ( ! empty( $this->class ) ) {
            $atts['class'] = esc_attr( $this->class );
        }

        if ( $this->has_children() ) {

            $atts['x-bind:class'] = esc_attr("{ 'is-expanded': expanded }");

            $atts['x-data'] = esc_attr( json_encode( [
                'expanded' => $this->is_expanded(),
            ] ) );
        }

        return $atts;
    }


    public function is_expanded()
    {
        return $this->has_children() && $this->is_current() || $this->is_parent() || $this->is_ancestor();
    }


    public function link_atts()
    {
        $atts = [
            'href' => esc_url( $this->link() ),
        ];

        if ( $this->is_target_blank() ) {
            $atts['target'] = '_blank';
        }

        return $atts;
    }


    public function has_children()
    {
        return in_array( 'menu-item-has-children', $this->wp_object->classes );
    }


    public function is_ancestor()
    {
        return $this->wp_object->current_item_ancestor;
    }


    public function is_parent()
    {
        return $this->wp_object->current_item_parent;
    }


    public function is_current()
    {
        $parsed_url = parse_url( $this->wp_object->url );
        return $this->wp_object->current && empty( $parsed_url['fragment'] );
    }


    public function add_class( string $class_name )
    {
        if ( ! in_array( $class_name, $this->classes, true ) ) {
            $this->classes[] = $class_name;
            $this->update_class();
        }
    }
}
