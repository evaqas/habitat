<?php

namespace Habitat;

class WooCommerce
{
    public static function init()
    {
        $self = new self();

        add_action( 'after_setup_theme', [ $self, 'setup' ] );
        add_action( 'all',               [ $self, 'debugActions' ] );

        // Disable default styles
        // add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
        add_action( 'wp_enqueue_scripts',         [ $self, 'disableSelect2' ], 99 );
    }


    public function setup()
    {
        add_theme_support('woocommerce');
    }


    public function disableSelect2()
    {
        if ( class_exists('woocommerce') && ( \is_cart() || \is_checkout() || \is_account_page() ) ) {
            wp_dequeue_style('select2');
            wp_dequeue_script('selectWoo');
        }
    }


    public function debugActions( $tag )
    {
        static $hooks = [];

        $filters = $GLOBALS['wp_filter'];

        $is_woo_action = substr( $tag, 0, strlen('woocommerce') )       === 'woocommerce';
        $is_woo_block  = substr( $tag, 0, strlen('woocommerce_block') ) === 'woocommerce_block';

        if ( did_action( $tag ) && $is_woo_action && ! $is_woo_block && ! empty( $filters[ $tag ]->callbacks ) ) {

            $callbacks_formatted = [];

            foreach ( $filters[ $tag ]->callbacks as $priority => $callbacks ) {

                foreach ( $callbacks as $key => $callback ) {

                    if ( is_string( $callback['function'] ) ) { // static method
                        $callback_split = explode( '::', $callback['function'] );
                    } else if ( is_array( $callback['function'] ) ) { // callable array
                        $callback_split = $callback['function'];
                    } else if ( is_object( $callback['function'] ) ) { // closure
                        $callback_split[] = $callback['function'];
                    } else { // skip
                        continue;
                    }

                    $callback_class  = count( $callback_split ) > 1 ? $callback_split[0] : false;
                    $callback_method = count( $callback_split ) > 1 ? $callback_split[1] : $callback_split[0];

                    if ( $callback_class ) {

                        $class = new \ReflectionClass( $callback_class );
                        $callable = new \ReflectionMethod( $callback_class, $callback_method );

                        $name = sprintf( '%s%s%s',
                            $class->getShortName(),
                            $callable->isStatic() ? '::' : '->',
                            $callable->getShortName()
                        );
                    } else {
                        $callable = new \ReflectionFunction( $callback_method );
                        $name = $callable->getShortName();
                    }

                    $callbacks_formatted[] = sprintf( "\n\n\t[%s] %s()\n\t\t(%s:%d)",
                        $priority,
                        $name,
                        str_replace( ABSPATH, '', $callable->getFileName() ),
                        $callable->getStartLine()
                    );
                }

                $hooks[ $tag ] = $callbacks_formatted;
            }
        }

        if ( $tag === 'shutdown' ) {

            $debug_string = '';

            foreach ( $hooks as $action => $hooked_functions ) {
                $debug_string .= sprintf( "\n\n\n%s:%s", $action, implode( '', $hooked_functions ) );
            }

            do_action( 'qm/info', $debug_string );
        }
    }
}
