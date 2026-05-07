<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_directorio_home', 'ed_render_home_shortcode' );

function ed_render_home_shortcode() {
    $file = ED_PLUGIN_DIR . 'templates/home.html';
    if ( ! file_exists( $file ) ) {
        return '<p>Template not found.</p>';
    }
    $html = file_get_contents( $file );

    // Extract content between <style> and </style>
    $style = '';
    if ( preg_match( '/<style>(.*?)<\/style>/s', $html, $matches ) ) {
        $style = '<style>' . $matches[1] . '</style>';
    }

    // Extract content between <body> and </body>
    $body = '';
    if ( preg_match( '/<body[^>]*>(.*?)<\/body>/s', $html, $matches ) ) {
        $body = $matches[1];
    }

    // Extract any inline <script> tags (for scroll animations etc.)
    $scripts = '';
    if ( preg_match_all( '/<script>(.*?)<\/script>/s', $html, $matches ) ) {
        foreach ( $matches[0] as $script ) {
            $scripts .= $script;
        }
    }

    // Get Google Fonts link
    $fonts = '';
    if ( preg_match_all( '/<link[^>]*fonts\.googleapis\.com[^>]*>/s', $html, $matches ) ) {
        $fonts = implode( "\n", $matches[0] );
    }
    if ( preg_match_all( '/<link[^>]*fonts\.gstatic\.com[^>]*>/s', $html, $matches ) ) {
        $fonts .= "\n" . implode( "\n", $matches[0] );
    }

    // Get Font Awesome link
    $fa = '';
    if ( preg_match( '/<link[^>]*font-awesome[^>]*>/s', $html, $matches ) ) {
        $fa = $matches[0];
    }

    return $fonts . "\n" . $fa . "\n" . $style . "\n" . $body . "\n" . $scripts;
}
