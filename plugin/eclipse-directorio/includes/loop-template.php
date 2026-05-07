<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_ed_build_loop_template', 'ed_build_loop_template_action' );

function ed_build_loop_template_action() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $template_id = ed_get_or_create_loop_template();
    $data        = ed_build_loop_item_data();

    update_post_meta( $template_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $template_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $template_id, '_elementor_template_type', 'loop-item' );
    update_post_meta( $template_id, '_elementor_version', '3.21.0' );
    update_post_meta( $template_id, '_wp_page_template', 'elementor_canvas' );

    wp_set_object_terms( $template_id, 'loop-item', 'elementor_library_type' );

    delete_post_meta( $template_id, '_elementor_css' );

    echo 'Loop Item template built successfully. ID: ' . $template_id;
    exit;
}

function ed_get_or_create_loop_template() {
    $existing = get_posts( array(
        'post_type'  => 'elementor_library',
        'meta_key'   => '_ed_loop_template',
        'meta_value' => 'proveedor_card',
        'numberposts' => 1,
        'post_status' => 'any',
    ) );

    if ( $existing ) {
        return $existing[0]->ID;
    }

    $id = wp_insert_post( array(
        'post_title'  => 'Proveedor Card',
        'post_type'   => 'elementor_library',
        'post_status' => 'publish',
    ) );

    update_post_meta( $id, '_ed_loop_template', 'proveedor_card' );

    return $id;
}

function ed_dynamic_tag( $name, $settings = array() ) {
    $id      = ed_el_id();
    $encoded = base64_encode( wp_json_encode( $settings ) );
    return '[elementor-tag id="' . $id . '" name="' . $name . '" settings="' . $encoded . '"]';
}

function ed_build_loop_item_data() {
    return array(
        ed_section(
            array(
                'layout'  => 'full_width',
                'padding' => ed_px( 0 ),
                'margin'  => ed_px( 0 ),
                'gap'     => 'no',
            ),
            array(
                ed_column( 100, array(

                    // Featured image with dynamic tag
                    ed_widget( 'image', array(
                        'image'      => array( 'url' => '', 'id' => '' ),
                        'image_size' => 'medium_large',
                        '__dynamic__' => array(
                            'image' => ed_dynamic_tag( 'post-featured-image' ),
                        ),
                        '_margin'    => ed_px( 0 ),
                        '_padding'   => ed_px( 0 ),
                        'width'      => ed_size( 100, '%' ),
                        'css_classes' => 'ed-loop-card-img',
                    ) ),

                    // Provider name (ACF nombre_negocio with post-title fallback)
                    ed_widget( 'heading', array(
                        'title'       => '',
                        'header_size' => 'h3',
                        '__dynamic__' => array(
                            'title' => ed_dynamic_tag( 'post-title' ),
                        ),
                        'title_color'                 => '#2D2D2D',
                        'typography_typography'        => 'custom',
                        'typography_font_family'       => 'Playfair Display',
                        'typography_font_size'         => ed_size( 20 ),
                        'typography_font_weight'       => '500',
                        'typography_line_height'       => ed_size( 1.3, 'em' ),
                        '_margin'                      => ed_px( 20, 20, 8, 20 ),
                    ) ),

                    // Category term
                    ed_widget( 'text-editor', array(
                        'editor' => '',
                        '__dynamic__' => array(
                            'editor' => ed_dynamic_tag( 'post-terms', array(
                                'taxonomy'  => 'categoria_servicio',
                                'separator' => ' · ',
                            ) ),
                        ),
                        'text_color'                  => '#C8A96E',
                        'typography_typography'        => 'custom',
                        'typography_font_family'       => 'DM Sans',
                        'typography_font_size'         => ed_size( 12 ),
                        'typography_font_weight'       => '500',
                        'typography_letter_spacing'    => ed_size( 1 ),
                        'typography_text_transform'    => 'uppercase',
                        '_margin'                      => ed_px( 0, 20, 8, 20 ),
                    ) ),

                    // Zone term
                    ed_widget( 'text-editor', array(
                        'editor' => '',
                        '__dynamic__' => array(
                            'editor' => ed_dynamic_tag( 'post-terms', array(
                                'taxonomy'  => 'zona_servicio',
                                'separator' => ', ',
                            ) ),
                        ),
                        'text_color'                  => '#999999',
                        'typography_typography'        => 'custom',
                        'typography_font_family'       => 'DM Sans',
                        'typography_font_size'         => ed_size( 13 ),
                        'typography_font_weight'       => '300',
                        '_margin'                      => ed_px( 0, 20, 12, 20 ),
                    ) ),

                    // Short description (ACF descripcion_corta)
                    ed_widget( 'text-editor', array(
                        'editor' => '',
                        '__dynamic__' => array(
                            'editor' => ed_dynamic_tag( 'post-excerpt' ),
                        ),
                        'text_color'                  => '#6B6B6B',
                        'typography_typography'        => 'custom',
                        'typography_font_family'       => 'DM Sans',
                        'typography_font_size'         => ed_size( 14 ),
                        'typography_font_weight'       => '300',
                        'typography_line_height'       => ed_size( 1.7, 'em' ),
                        '_margin'                      => ed_px( 0, 20, 20, 20 ),
                    ) ),

                    // Ver perfil button
                    ed_widget( 'button', array(
                        'text'  => 'Ver perfil',
                        'align' => 'stretch',
                        'link'  => array( 'url' => '' ),
                        '__dynamic__' => array(
                            'link' => ed_dynamic_tag( 'post-url' ),
                        ),
                        'button_type'                    => 'default',
                        'background_color'               => 'transparent',
                        'button_text_color'              => '#2D2D2D',
                        'border_border'                  => 'solid',
                        'border_width'                   => ed_px( 1 ),
                        'border_color'                   => '#E0DDD8',
                        'border_radius'                  => ed_px( 4 ),
                        'typography_typography'           => 'custom',
                        'typography_font_family'          => 'DM Sans',
                        'typography_font_size'            => ed_size( 13 ),
                        'typography_font_weight'          => '500',
                        'typography_letter_spacing'       => ed_size( 1 ),
                        'text_padding'                   => ed_px( 12, 24, 12, 24 ),
                        'hover_color'                    => '#FFFFFF',
                        'button_background_hover_color'  => '#C8A96E',
                        'button_hover_border_color'      => '#C8A96E',
                        '_margin'                        => ed_px( 0, 20, 20, 20 ),
                    ) ),

                ), array(
                    'background_background' => 'classic',
                    'background_color'      => '#FFFFFF',
                    'border_border'         => 'solid',
                    'border_width'          => ed_px( 1 ),
                    'border_color'          => '#F0EDE8',
                    'border_radius'         => ed_px( 12 ),
                    'box_shadow_box_shadow_type' => 'yes',
                    'box_shadow_box_shadow' => array(
                        'horizontal' => 0,
                        'vertical'   => 2,
                        'blur'       => 20,
                        'spread'     => 0,
                        'color'      => 'rgba(0,0,0,0.04)',
                    ),
                    '_css_classes' => 'ed-loop-card',
                    'padding'      => ed_px( 0 ),
                    'overflow'     => 'hidden',
                ) ),
            )
        ),
    );
}
