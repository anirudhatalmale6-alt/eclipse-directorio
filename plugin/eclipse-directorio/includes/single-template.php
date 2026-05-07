<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_ed_build_single_template', 'ed_build_single_template_action' );

function ed_build_single_template_action() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $template_id = ed_get_or_create_single_template();
    $data        = ed_build_single_template_data();

    update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $template_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $template_id, '_elementor_template_type', 'single' );
    update_post_meta( $template_id, '_elementor_version', '3.21.0' );
    update_post_meta( $template_id, '_wp_page_template', 'elementor_canvas' );

    wp_set_object_terms( $template_id, 'single', 'elementor_library_type' );

    $conditions = array( 'include/singular/proveedor' );
    update_post_meta( $template_id, '_elementor_conditions', $conditions );

    $option_key     = 'elementor_pro_theme_builder_conditions';
    $all_conditions = get_option( $option_key, array() );
    if ( ! is_array( $all_conditions ) ) {
        $all_conditions = array();
    }
    if ( ! isset( $all_conditions['single'] ) || ! is_array( $all_conditions['single'] ) ) {
        $all_conditions['single'] = array();
    }
    $all_conditions['single'][ $template_id ] = $conditions;

    // Remove any conflicting single templates for proveedor
    foreach ( $all_conditions['single'] as $existing_id => $existing_conds ) {
        if ( (int) $existing_id !== (int) $template_id && is_array( $existing_conds ) ) {
            foreach ( $existing_conds as $c ) {
                if ( strpos( $c, 'singular/proveedor' ) !== false ) {
                    unset( $all_conditions['single'][ $existing_id ] );
                    delete_post_meta( $existing_id, '_elementor_conditions' );
                }
            }
        }
    }

    update_option( $option_key, $all_conditions );

    delete_post_meta( $template_id, '_elementor_css' );

    echo 'Single proveedor template built successfully. ID: ' . $template_id;
    exit;
}

function ed_get_or_create_single_template() {
    $existing = get_posts( array(
        'post_type'   => 'elementor_library',
        'meta_key'    => '_ed_single_template',
        'meta_value'  => 'proveedor',
        'numberposts' => 1,
        'post_status' => 'any',
    ) );

    if ( $existing ) {
        return $existing[0]->ID;
    }

    $id = wp_insert_post( array(
        'post_title'  => 'Ficha Proveedor',
        'post_type'   => 'elementor_library',
        'post_status' => 'publish',
    ) );

    update_post_meta( $id, '_ed_single_template', 'proveedor' );

    return $id;
}

function ed_build_single_template_data() {
    $data = array();

    // Full-width provider profile shortcode
    $data[] = ed_section(
        array(
            'layout'  => 'full_width',
            'padding' => ed_px( 0 ),
            'margin'  => ed_px( 0, 0, 0, 0 ),
        ),
        array( ed_column( 100, array(
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_proveedor_perfil]',
            ) ),
        ) ) )
    );

    // CTA
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
