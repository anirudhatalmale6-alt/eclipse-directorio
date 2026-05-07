<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_init', 'ed_maybe_import_taxonomy_acf_fields' );

function ed_maybe_import_taxonomy_acf_fields() {
    if ( get_option( 'ed_acf_taxonomy_fields_v1' ) ) {
        return;
    }
    if ( ! function_exists( 'acf_import_field_group' ) ) {
        return;
    }

    acf_import_field_group( array(
        'key'      => 'group_ed_categoria_visual',
        'title'    => 'Datos Visuales de Categor&iacute;a',
        'fields'   => array(
            array(
                'key'           => 'field_ed_cat_imagen',
                'label'         => 'Imagen de Categor&iacute;a',
                'name'          => 'categoria_imagen',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'instructions'  => 'Imagen que se muestra en la tarjeta de categor&iacute;a del directorio (recomendado: 800x600px).',
                'required'      => 0,
                'library'       => 'all',
                'min_width'     => 400,
                'min_height'    => 300,
            ),
            array(
                'key'           => 'field_ed_cat_color',
                'label'         => 'Color de Overlay',
                'name'          => 'categoria_color_overlay',
                'type'          => 'color_picker',
                'default_value' => '',
                'instructions'  => 'Color de overlay sobre la imagen (se aplica con transparencia). Si no se establece, se usa el overlay por defecto.',
                'required'      => 0,
            ),
            array(
                'key'           => 'field_ed_cat_icono',
                'label'         => 'Icono (clase Font Awesome)',
                'name'          => 'categoria_icono',
                'type'          => 'text',
                'default_value' => '',
                'placeholder'   => 'fa-solid fa-hotel',
                'instructions'  => 'Clase de Font Awesome para el icono (opcional). Ej: fa-solid fa-hotel, fa-solid fa-utensils.',
                'required'      => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'taxonomy',
                    'operator' => '==',
                    'value'    => 'categoria_servicio',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
        'active'     => true,
    ) );

    update_option( 'ed_acf_taxonomy_fields_v1', true );
}
