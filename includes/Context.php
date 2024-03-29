<?php

namespace Habitat;

class Context
{
    protected $context = [];

    protected $methods_to_exclude = [];


    public function __construct()
    {
        $this->excludeMethods( [ '__construct', 'mergeContext' ] );
        $this->setContextFromMethods();
    }


    public function mergeContext( $context )
    {
        $this->context = array_merge( $context, $this->context );
        return $this->getContext();
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
            return ! in_array( $method->name, $this->methods_to_exclude ) && ! $method->isStatic();
        } );

        foreach ( $methods as $method ) {
            $this->addToContext( $method->name, $this->{$method->name}() );
        }
    }


    protected function addToContext( $key, $value )
    {
        $this->context[ $key ] = $value;
        do_action( 'qm/debug', [ $key => $value ] );
    }


    protected function excludeMethods( array $methods = [] )
    {
        $this->methods_to_exclude = array_merge( $this->methods_to_exclude, $methods );
    }
}
