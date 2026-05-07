<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_ed_build_archive_template', 'ed_build_archive_template_action' );

function ed_build_archive_template_action() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $template_id = ed_get_or_create_archive_template();
    $data        = ed_build_archive_template_data();

    update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $template_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $template_id, '_elementor_template_type', 'archive' );
    update_post_meta( $template_id, '_elementor_version', '3.21.0' );
    update_post_meta( $template_id, '_wp_page_template', 'elementor_canvas' );

    wp_set_object_terms( $template_id, 'archive', 'elementor_library_type' );

    $conditions = array( 'include/archive/categoria_servicio' );
    update_post_meta( $template_id, '_elementor_conditions', $conditions );

    update_option( 'elementor_conditions_archive_' . $template_id, $conditions );

    ed_save_elementor_theme_conditions( $template_id, $conditions );

    delete_post_meta( $template_id, '_elementor_css' );

    echo 'Archive template built successfully. ID: ' . $template_id;
    exit;
}

function ed_get_or_create_archive_template() {
    $existing = get_posts( array(
        'post_type'   => 'elementor_library',
        'meta_key'    => '_ed_archive_template',
        'meta_value'  => 'categoria_servicio',
        'numberposts' => 1,
        'post_status' => 'any',
    ) );

    if ( $existing ) {
        return $existing[0]->ID;
    }

    $id = wp_insert_post( array(
        'post_title'  => 'Archivo Categoría de Servicio',
        'post_type'   => 'elementor_library',
        'post_status' => 'publish',
    ) );

    update_post_meta( $id, '_ed_archive_template', 'categoria_servicio' );

    return $id;
}

function ed_save_elementor_theme_conditions( $template_id, $conditions ) {
    $option_key    = 'elementor_pro_theme_builder_conditions';
    $all_conditions = get_option( $option_key, array() );

    if ( ! is_array( $all_conditions ) ) {
        $all_conditions = array();
    }

    if ( ! isset( $all_conditions['archive'] ) || ! is_array( $all_conditions['archive'] ) ) {
        $all_conditions['archive'] = array();
    }

    $all_conditions['archive'][ $template_id ] = $conditions;

    update_option( $option_key, $all_conditions );
}

function ed_build_archive_template_data() {
    $data = array();

    // Section 1: Archive Header (shortcode renders category info dynamically)
    $data[] = ed_section(
        array(
            'layout'                => 'full_width',
            'padding'               => ed_px( 0 ),
            'margin'                => ed_px( 0, 0, 0, 0 ),
            'background_background' => 'classic',
            'background_color'      => '#1A1A2E',
        ),
        array( ed_column( 100, array(
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_archive_header]',
            ) ),
        ) ) )
    );

    // Section 2: Search bar
    $data[] = ed_section(
        array(
            'layout'               => 'boxed',
            'background_background'=> 'classic',
            'background_color'     => '#F8F7F5',
            'padding'              => ed_px( 40, 0, 40, 0 ),
        ),
        array( ed_column( 100, array(
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_buscar]',
            ) ),
        ) ) )
    );

    // Section 3: Provider listing
    $data[] = ed_section(
        array(
            'layout'  => 'boxed',
            'padding' => ed_px( 60, 0, 80, 0 ),
        ),
        array( ed_column( 100, array(
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_listado per_page="12" columns="3"]',
            ) ),
        ) ) )
    );

    // Section 4: CTA
    $data[] = ed_section(
        array(
            'layout'                    => 'full_width',
            'background_background'     => 'gradient',
            'background_color'          => '#1A1A2E',
            'background_color_stop'     => ed_size( 0, '%' ),
            'background_color_b'        => '#3D2A32',
            'background_color_b_stop'   => ed_size( 100, '%' ),
            'background_gradient_angle' => ed_size( 135, 'deg' ),
            'padding'                   => ed_px( 80, 0, 80, 0 ),
            'css_classes'               => 'ed-cta-section',
        ),
        array( ed_column( 100, array(
            ed_label_widget( 'PARA PROVEEDORES' ),
            ed_title_widget( '&iquest;Eres proveedor de eventos en Sevilla?', '#FFFFFF' ),
            ed_widget( 'text-editor', array(
                'editor' => '<p style="text-align:center;color:rgba(255,255,255,0.55);font-size:16px;font-weight:300;max-width:560px;margin:0 auto;line-height:1.7;">&Uacute;nete a la selecci&oacute;n de profesionales recomendados por Eclipse Sevilla</p>',
                'align'  => 'center',
                '_margin' => ed_px( 0, 0, 36, 0 ),
            ) ),
            ed_widget( 'button', array(
                'text'                           => 'Quiero aparecer en el directorio',
                'align'                          => 'center',
                'link'                           => array( 'url' => '#', 'is_external' => '', 'nofollow' => '' ),
                'background_color'               => '#C8A96E',
                'button_text_color'              => '#1A1A2E',
                'border_radius'                  => ed_px( 4 ),
                'typography_typography'           => 'custom',
                'typography_font_family'          => 'DM Sans',
                'typography_font_size'            => ed_size( 14 ),
                'typography_font_weight'          => '600',
                'typography_letter_spacing'       => ed_size( 1 ),
                'text_padding'                   => ed_px( 16, 40, 16, 40 ),
                'button_background_color'        => '#C8A96E',
                'hover_color'                    => '#1A1A2E',
                'button_background_hover_color'  => '#D4BA85',
            ) ),
        ) ) )
    );

    return $data;
}
