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
 * Initialize theme
 */
Theme::init();


/**
 * Timber context and configuration
 */
Timber::setup();


/**
 * Run WP filters
 */
Filters::init();


/**
 * Use template files as controllers
 */
ControllerService::init();


/**
 * Acf filters and configuration
 */
Acf::init();
