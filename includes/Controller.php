<?php

namespace Habitat;

use Timber\Timber;

class Controller
{
    protected $context = [];


    public function __construct()
    {
        $this->context = Timber::get_context();
        $this->context['page_title'] = $this->getPageTitle();

        if ( is_home() || is_archive() ) {
            $this->context['posts'] = Timber::get_posts();
            $this->context['pagination'] = Timber::get_pagination(4);
        } else if ( is_singular() ) {
            $this->context['post'] = Timber::get_post();
        }
    }


    public static function init()
    {
        $self = new self();
        $self->handle();
    }


    public function handle()
    {
        Timber::render( $this->getTemplates(), $this->context );
    }


    public function getPageTitle()
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


    public function getTemplates()
    {
        $templates = [ 'index.php.twig' ];

        if ( is_404() ) {
            array_unshift( $templates, '404.php.twig' );
        } if ( is_search() ) {
            array_unshift( $templates, 'search.php.twig' );
        } else if ( is_singular() ) {
            array_unshift( $templates, 'singular.php.twig' );

            if ( is_attachment() ) {
                array_unshift( $templates, 'attachment.php.twig' );
            } else if ( is_page() ) {
                array_unshift(
                    $templates,
                    sprintf( 'page-%s.php.twig', get_queried_object()->post_name ),
                    sprintf( 'page-%s.php.twig', get_queried_object()->ID ),
                    'page.php.twig'
                );
            } else if ( is_single() ) {
                array_unshift(
                    $templates,
                    sprintf( 'single-%s-%s.php.twig', get_queried_object()->post_type, get_queried_object()->post_name ),
                    sprintf( 'single-%s.php.twig', get_queried_object()->post_type ),
                    'single.php.twig'
                );
            }

            if ( is_page_template() ) {
                array_unshift( $templates , basename( get_page_template() ) . '.twig' );
            }
        } else if ( is_archive() ) {
            array_unshift( $templates, 'archive.php.twig' );

            if ( is_category() ) {
                array_unshift(
                    $templates,
                    sprintf( 'category-%s.php.twig', get_queried_object()->slug ),
                    sprintf( 'category-%s.php.twig', get_queried_object()->term_id ),
                    'category.php.twig'
                );
            } else if ( is_tag() ) {
                array_unshift(
                    $templates,
                    sprintf( 'tag-%s.php.twig', get_queried_object()->slug ),
                    sprintf( 'tag-%s.php.twig', get_queried_object()->term_id ),
                    'tag.php.twig'
                );
            } else if ( is_tax() ) {
                array_unshift(
                    $templates,
                    sprintf( 'taxonomy-%s-%s.php.twig', get_queried_object()->taxonomy, get_queried_object()->slug ),
                    sprintf( 'taxonomy-%s.php.twig', get_queried_object()->taxonomy ),
                    'taxonomy.php.twig'
                );
            } else if ( is_post_type_archive() ) {
                array_unshift( $templates, 'archive-' . get_post_type() . '.php.twig' );
            } else if ( is_author() ) {
                array_unshift(
                    $templates,
                    sprintf( 'author-%s.php.twig', get_queried_object()->user_nicename ),
                    sprintf( 'author-%s.php.twig', get_queried_object()->ID ),
                    'author.php.twig'
                );
            } else if ( is_date() ) {
                array_unshift( $templates, 'date.php.twig' );
            }
        }

        return $templates;
    }
}
