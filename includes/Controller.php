<?php

namespace Habitat;

use Timber\Timber;
use Brain\Hierarchy\Hierarchy;

class Controller extends Context
{
    public function __construct()
    {
        $this->excludeMethods( [ 'view' ] );
        parent::__construct();
    }


    public function view()
    {
        Timber::render( $this->getTemplates(), $this->mergeContext( Timber::context() ) );
    }


    protected function getTemplates()
    {
        global $wp_query;

        $hierarchy = new Hierarchy();

        $templates = array_map( function ( $filename ) {
            return basename( $filename, '.php' ) . '.php.twig';
        }, $hierarchy->templates( $wp_query ) );

        return $templates;
    }
}
