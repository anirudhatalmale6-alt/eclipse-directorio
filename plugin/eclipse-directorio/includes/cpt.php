<?php
/**
 * Eclipse Directorio - Custom Post Type: Proveedor
 *
 * @package EclipseDirectorio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'ed_register_proveedor_cpt' );

/**
 * Register the "proveedor" Custom Post Type.
 */
function ed_register_proveedor_cpt() {

    $labels = array(
        'name'                  => 'Proveedores',
        'singular_name'         => 'Proveedor',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Proveedor',
        'edit_item'             => 'Editar Proveedor',
        'new_item'              => 'Nuevo Proveedor',
        'view_item'             => 'Ver Proveedor',
        'view_items'            => 'Ver Proveedores',
        'search_items'          => 'Buscar Proveedores',
        'not_found'             => 'No se encontraron proveedores',
        'not_found_in_trash'    => 'No se encontraron proveedores en la papelera',
        'all_items'             => 'Todos los Proveedores',
        'archives'              => 'Archivo de Proveedores',
        'attributes'            => 'Atributos del Proveedor',
        'insert_into_item'      => 'Insertar en proveedor',
        'uploaded_to_this_item' => 'Subido a este proveedor',
        'featured_image'        => 'Imagen Destacada',
        'set_featured_image'    => 'Establecer imagen destacada',
        'remove_featured_image' => 'Eliminar imagen destacada',
        'use_featured_image'    => 'Usar como imagen destacada',
        'menu_name'             => 'Proveedores',
        'filter_items_list'     => 'Filtrar proveedores',
        'items_list_navigation' => 'Navegación de proveedores',
        'items_list'            => 'Lista de proveedores',
    );

    $args = array(
        'labels'              => $labels,
        'description'         => 'Proveedores del directorio de eventos de Sevilla.',
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'proveedor', 'with_front' => false ),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-businessperson',
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    );

    register_post_type( 'proveedor', $args );
}
