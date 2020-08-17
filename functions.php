<?php

namespace Habitat;


/**
 * Project constants
 */
define( 'DIST_PATH', '/assets/dist/' );

/**
 * Load dependencies
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once get_theme_file_path('includes/functions.php');


/**
 * Initialize Timber library
 */
$timber = new \Timber\Timber();


/**
 * Initialize theme
 */
Theme::init();


/**
 * Run WP filters
 */
Filters::init();


/**
 * Use template files as controllers
 */
ControllersService::init();


/**
 * Acf filters and configuration
 */
Acf::init();
