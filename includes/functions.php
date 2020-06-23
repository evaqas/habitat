<?php

namespace Habitat;

function asset( $path ) {
    $manifestPath = get_theme_file_path('assets/dist/manifest.json');
    $manifest = file_exists( $manifestPath ) ? json_decode( file_get_contents( $manifestPath ), true ) : [];
    $asset_path = isset( $manifest[ $path ] ) ? $manifest[ $path ] : $path;
    return get_theme_file_uri( $asset_path );
}
