<?php

namespace Habitat;

function asset_uri( $path ) {
    return get_theme_file_uri( get_dist_asset( $path ) );
}

function asset_path( $path ) {
    return get_theme_file_path( get_dist_asset( $path ) );
}

function get_dist_asset( $path ) {
    $manifestPath = get_theme_file_path('assets/dist/manifest.json');
    $manifest = file_exists( $manifestPath ) ? json_decode( file_get_contents( $manifestPath ), true ) : [];
    return isset( $manifest[ $path ] ) ? $manifest[ $path ] : $path;
}
