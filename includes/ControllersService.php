<?php

namespace Habitat;

use Stringy\Stringy;

class ControllersService
{
    public static function init()
    {
        $self = new self();

        add_filter( 'template_include', [ $self, 'handleTemplateInclude' ] );
    }


    public function handleTemplateInclude( $template )
    {
        if ( $template ) {
            include $template;
        }

        $controllerName = $this->getControllerClassFromTemplate( $template );

        if ( class_exists( $controllerName ) ) {
            $controllerName::init();
        }

        return;
    }


    public function getControllerClassFromTemplate( $template )
    {
        $controllerName = Stringy::create( basename( $template, '.php' ) )->upperCamelize() . 'Controller';

        // Classes can't start with a number
        if ( $controllerName === '404Controller' ) {
            $controllerName = 'Error' . $controllerName;
        }

        return __NAMESPACE__ . '\\' . $controllerName;
    }
}
