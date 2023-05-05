<?php

namespace Habitat;

class Acf
{
    public static function init()
    {
        $self = new self();

        add_action( 'acf/init', [ $self, 'addOptionsPage' ] );

        add_filter( 'acf/settings/default_language', [ $self, 'acfDefaultLanguage' ] );
        add_filter( 'acf/settings/current_language', [ $self, 'acfCurrentLanguage' ] );
    }


    public function addOptionsPage()
    {
        acf_add_options_sub_page([
            'page_title'      => esc_html__( 'Globalūs Nustatymai', 'habitat' ),
            'menu_title'      => esc_html( get_bloginfo('name') ),
            'menu_slug'       => 'globalus-nustatymai',
            'parent_slug'     => 'options-general.php',
            'capability'      => 'manage_options',
            'update_button'   => esc_html__( 'Išsaugoti', 'habitat' ),
            'updated_message' => esc_html__( 'Nustatymų pakeitimai išsaugoti.', 'habitat' ),
        ]);
    }


    public function acfDefaultLanguage( $lang )
    {
        return function_exists('pll_default_language') ? pll_default_language() : $lang;
    }


    public function acfCurrentLanguage( $lang )
    {
        return function_exists('pll_current_language') ? pll_current_language() : $lang;
    }
}
