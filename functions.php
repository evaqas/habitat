<?php

namespace Habitat;


/**
 * Project constants
 */
define( 'ASSETS_PATH', '/assets' );
define( 'DIST_PATH', '/dist' );


/**
 * Load dependencies
 */
require_once get_theme_file_path('vendor/autoload.php');
require_once get_theme_file_path('includes/functions.php');


/**
 * Initialize Timber library
 */
\Timber\Timber::init();


/**
 * Timber configuration
 */
Timber::setup();


/**
 * Initialize theme
 */
Theme::init();


/**
 * Run WP filters
 */
Filters::init();


/**
 * Add to global Timber context
 */
App::init();


/**
 * Acf filters and configuration
 */
Acf::init();


/**
 * Initialize WooCommerce configuration
 */
WooCommerce::init();
