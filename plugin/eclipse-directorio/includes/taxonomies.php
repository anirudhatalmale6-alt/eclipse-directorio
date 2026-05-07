<?php
/**
 * Eclipse Directorio - Taxonomies
 *
 * @package EclipseDirectorio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'ed_register_taxonomies' );

/**
 * Register taxonomies for the "proveedor" CPT.
 */
function ed_register_taxonomies() {

    // --- Categoría de Servicio ---
    $cat_labels = array(
        'name'              => 'Categorías de Servicio',
        'singular_name'     => 'Categoría de Servicio',
        'search_items'      => 'Buscar Categorías',
        'all_items'         => 'Todas las Categorías',
        'parent_item'       => 'Categoría Superior',
        'parent_item_colon' => 'Categoría Superior:',
        'edit_item'         => 'Editar Categoría',
        'update_item'       => 'Actualizar Categoría',
        'add_new_item'      => 'Añadir Nueva Categoría',
        'new_item_name'     => 'Nombre de Nueva Categoría',
        'menu_name'         => 'Categorías de Servicio',
        'not_found'         => 'No se encontraron categorías',
        'back_to_items'     => 'Volver a Categorías',
    );

    register_taxonomy( 'categoria_servicio', array( 'proveedor' ), array(
        'labels'            => $cat_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categoria-servicio', 'with_front' => false ),
    ) );

    // --- Zona / Ciudad ---
    $zona_labels = array(
        'name'              => 'Zonas / Ciudades',
        'singular_name'     => 'Zona / Ciudad',
        'search_items'      => 'Buscar Zonas',
        'all_items'         => 'Todas las Zonas',
        'parent_item'       => 'Zona Superior',
        'parent_item_colon' => 'Zona Superior:',
        'edit_item'         => 'Editar Zona',
        'update_item'       => 'Actualizar Zona',
        'add_new_item'      => 'Añadir Nueva Zona',
        'new_item_name'     => 'Nombre de Nueva Zona',
        'menu_name'         => 'Zonas / Ciudades',
        'not_found'         => 'No se encontraron zonas',
        'back_to_items'     => 'Volver a Zonas',
    );

    register_taxonomy( 'zona_servicio', array( 'proveedor' ), array(
        'labels'            => $zona_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'zona-servicio', 'with_front' => false ),
    ) );
}
