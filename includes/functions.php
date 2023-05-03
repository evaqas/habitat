<?php

namespace Habitat;

function asset_uri( $path ) {
    return get_theme_file_uri( get_asset( $path ) );
}

function asset_path( $path ) {
    return get_theme_file_path( get_asset( $path ) );
}

function get_asset( $path ) {
    $manifest_path = get_theme_file_path( DIST_PATH . '/manifest.json' );
    $manifest = file_exists( $manifest_path ) ? json_decode( file_get_contents( $manifest_path ), true ) : [];
    $asset_path = isset( $manifest[ $path ] ) ? $manifest[ $path ] : trailingslashit( ASSETS_PATH ) . ltrim( $path, '/\\' );
    return get_relative_asset_path( $asset_path );
}

function get_relative_asset_path( $absolute_path ) {
    $relative_path = substr( $absolute_path, strpos( $absolute_path, DIST_PATH ) );
    return empty( $relative_path ) || $relative_path === false ? $absolute_path : $relative_path;
}
