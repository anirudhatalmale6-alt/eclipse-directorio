<?php
/**
 * One-time ACF field group importer.
 * Imports field groups from JSON into the database so they're editable in the ACF admin.
 * Runs once via admin_init, then disables itself by setting an option.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_init', 'ed_maybe_import_acf_fields' );

function ed_maybe_import_acf_fields() {
    if ( get_option( 'ed_acf_fields_imported' ) ) {
        return;
    }

    if ( ! function_exists( 'acf_import_field_group' ) ) {
        return;
    }

    $json_file = ED_PLUGIN_DIR . 'includes/acf-fields.json';
    if ( ! file_exists( $json_file ) ) {
        return;
    }

    $json = file_get_contents( $json_file );
    $field_groups = json_decode( $json, true );

    if ( ! is_array( $field_groups ) ) {
        return;
    }

    foreach ( $field_groups as $field_group ) {
        acf_import_field_group( $field_group );
    }

    update_option( 'ed_acf_fields_imported', true );
}
