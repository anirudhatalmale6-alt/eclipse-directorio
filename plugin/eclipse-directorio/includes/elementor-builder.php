<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_ed_build_elementor_home', 'ed_build_elementor_home_action' );

function ed_build_elementor_home_action() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $page_id = ed_get_directorio_page_id();
    if ( ! $page_id ) {
        wp_die( 'Directorio page not found.' );
    }

    $data = ed_build_home_elementor_data();

    update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
    update_post_meta( $page_id, '_elementor_version', '3.21.0' );

    delete_post_meta( $page_id, '_elementor_css' );

    wp_update_post( array(
        'ID'            => $page_id,
        'page_template' => 'elementor_canvas',
    ) );

    echo 'Elementor home page built successfully for page ID: ' . $page_id;
    exit;
}

function ed_get_directorio_page_id() {
    $page = get_page_by_path( 'directorio' );
    if ( $page ) {
        return $page->ID;
    }

    $pages = get_posts( array(
        'post_type'  => 'page',
        'title'      => 'Directorio',
        'numberposts' => 1,
    ) );

    return $pages ? $pages[0]->ID : 0;
}

function ed_el_id() {
    return substr( bin2hex( random_bytes( 4 ) ), 0, 7 );
}

function ed_section( $settings, $columns ) {
    return array(
        'id'       => ed_el_id(),
        'elType'   => 'section',
        'settings' => $settings,
        'elements' => $columns,
    );
}

function ed_column( $size, $widgets, $settings = array() ) {
    return array(
        'id'       => ed_el_id(),
        'elType'   => 'column',
        'settings' => array_merge( array( '_column_size' => $size ), $settings ),
        'elements' => $widgets,
    );
}

function ed_widget( $type, $settings ) {
    return array(
        'id'         => ed_el_id(),
        'elType'     => 'widget',
        'widgetType' => $type,
        'settings'   => $settings,
        'elements'   => array(),
    );
}

function ed_px( $top, $right = null, $bottom = null, $left = null ) {
    if ( $right === null ) {
        return array( 'unit' => 'px', 'top' => (string) $top, 'right' => (string) $top, 'bottom' => (string) $top, 'left' => (string) $top, 'isLinked' => true );
    }
    return array( 'unit' => 'px', 'top' => (string) $top, 'right' => (string) $right, 'bottom' => (string) $bottom, 'left' => (string) $left, 'isLinked' => false );
}

function ed_size( $val, $unit = 'px' ) {
    return array( 'unit' => $unit, 'size' => $val, 'sizes' => array() );
}

function ed_label_widget( $text ) {
    return ed_widget( 'heading', array(
        'title'                       => $text,
        'header_size'                 => 'span',
        'align'                       => 'center',
        'title_color'                 => '#C8A96E',
        'typography_typography'        => 'custom',
        'typography_font_family'       => 'DM Sans',
        'typography_font_size'         => ed_size( 11 ),
        'typography_font_weight'       => '600',
        'typography_letter_spacing'    => ed_size( 3 ),
        'typography_text_transform'    => 'uppercase',
        '_margin'                      => ed_px( 0, 0, 16, 0 ),
    ) );
}

function ed_title_widget( $text, $color = '#2D2D2D' ) {
    return ed_widget( 'heading', array(
        'title'                       => $text,
        'header_size'                 => 'h2',
        'align'                       => 'center',
        'title_color'                 => $color,
        'typography_typography'        => 'custom',
        'typography_font_family'       => 'Playfair Display',
        'typography_font_size'         => ed_size( 42 ),
        'typography_font_size_tablet'  => ed_size( 34 ),
        'typography_font_size_mobile'  => ed_size( 26 ),
        'typography_font_weight'       => '500',
        'typography_line_height'       => ed_size( 1.2, 'em' ),
        '_margin'                      => ed_px( 0, 0, 18, 0 ),
    ) );
}

function ed_subtitle_widget( $text, $dark = false ) {
    $color = $dark ? '#6B6B6B' : 'rgba(255,255,255,0.6)';
    return ed_widget( 'text-editor', array(
        'editor' => '<p style="text-align:center;color:' . $color . ';font-size:16px;font-weight:300;max-width:560px;margin:0 auto;line-height:1.7;">' . $text . '</p>',
        'align'  => 'center',
    ) );
}

function ed_divider_widget() {
    return ed_widget( 'html', array(
        'html'    => '<div class="ed-deco-divider"><span></span><i class="fa-solid fa-diamond"></i><span></span></div>',
        '_margin' => ed_px( 0, 0, 64, 0 ),
    ) );
}

function ed_build_home_elementor_data() {
    $data = array();

    // ── SECTION 1: HERO ──
    $data[] = ed_section(
        array(
            'layout'                    => 'full_width',
            'height'                    => 'min-height',
            'custom_height'             => ed_size( 100, 'vh' ),
            'content_position'          => 'middle',
            'background_background'     => 'gradient',
            'background_color'          => '#1A1A2E',
            'background_color_stop'     => ed_size( 0, '%' ),
            'background_color_b'        => '#4A3028',
            'background_color_b_stop'   => ed_size( 100, '%' ),
            'background_gradient_angle' => ed_size( 165, 'deg' ),
            'padding'                   => ed_px( 120, 0, 80, 0 ),
            'css_classes'               => 'ed-hero-section',
        ),
        array( ed_column( 100, array(

            // Label
            ed_widget( 'heading', array(
                'title'                       => 'DIRECTORIO DE EVENTOS',
                'header_size'                 => 'span',
                'align'                       => 'center',
                'title_color'                 => '#C8A96E',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'DM Sans',
                'typography_font_size'         => ed_size( 11 ),
                'typography_font_weight'       => '600',
                'typography_letter_spacing'    => ed_size( 3 ),
                'typography_text_transform'    => 'uppercase',
                '_margin'                      => ed_px( 0, 0, 28, 0 ),
            ) ),

            // Main heading
            ed_widget( 'heading', array(
                'title'                         => 'Los mejores proveedores para tu evento en <i>Sevilla</i>',
                'header_size'                   => 'h1',
                'align'                         => 'center',
                'title_color'                   => '#FFFFFF',
                'typography_typography'          => 'custom',
                'typography_font_family'         => 'Playfair Display',
                'typography_font_size'           => ed_size( 52 ),
                'typography_font_size_tablet'    => ed_size( 40 ),
                'typography_font_size_mobile'    => ed_size( 28 ),
                'typography_font_weight'         => '500',
                'typography_line_height'         => ed_size( 1.15, 'em' ),
                '_margin'                        => ed_px( 0, 0, 24, 0 ),
            ) ),

            // Subtitle
            ed_widget( 'text-editor', array(
                'editor'                      => '<p style="text-align:center;color:rgba(255,255,255,0.6);font-size:17px;font-weight:300;max-width:540px;margin:0 auto 48px;line-height:1.7;">Una selecci&oacute;n curada de profesionales que har&aacute;n de tu celebraci&oacute;n una experiencia inolvidable</p>',
                'align'                       => 'center',
            ) ),

            // Search bar shortcode
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_buscar]',
            ) ),

        ) ) )
    );

    // ── SECTION 2: CATEGORIES ──
    $data[] = ed_section(
        array(
            'layout'  => 'boxed',
            'padding' => ed_px( 120, 0, 120, 0 ),
            'padding_tablet' => ed_px( 80, 0, 80, 0 ),
        ),
        array( ed_column( 100, array(

            // Label
            ed_widget( 'heading', array(
                'title'                       => 'CATEGOR&Iacute;AS',
                'header_size'                 => 'span',
                'align'                       => 'center',
                'title_color'                 => '#C8A96E',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'DM Sans',
                'typography_font_size'         => ed_size( 11 ),
                'typography_font_weight'       => '600',
                'typography_letter_spacing'    => ed_size( 3 ),
                'typography_text_transform'    => 'uppercase',
                '_margin'                      => ed_px( 0, 0, 16, 0 ),
            ) ),

            // Title
            ed_widget( 'heading', array(
                'title'                       => 'Explora por categor&iacute;a',
                'header_size'                 => 'h2',
                'align'                       => 'center',
                'title_color'                 => '#2D2D2D',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'Playfair Display',
                'typography_font_size'         => ed_size( 42 ),
                'typography_font_size_tablet'  => ed_size( 34 ),
                'typography_font_size_mobile'  => ed_size( 26 ),
                'typography_font_weight'       => '500',
                'typography_line_height'       => ed_size( 1.2, 'em' ),
                '_margin'                      => ed_px( 0, 0, 18, 0 ),
            ) ),

            // Subtitle
            ed_widget( 'text-editor', array(
                'editor' => '<p style="text-align:center;color:#6B6B6B;font-size:16px;font-weight:300;max-width:560px;margin:0 auto;line-height:1.7;">Encuentra el proveedor perfecto para cada detalle de tu evento</p>',
                'align'  => 'center',
            ) ),

            // Divider
            ed_widget( 'html', array(
                'html' => '<div class="ed-deco-divider"><span></span><i class="fa-solid fa-diamond"></i><span></span></div>',
                '_margin' => ed_px( 0, 0, 64, 0 ),
            ) ),

            // Categories shortcode
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_categorias]',
            ) ),

            // View all link
            ed_widget( 'html', array(
                'html' => '<div style="text-align:center;margin-top:48px;"><a href="#" class="ed-link-arrow">Ver todas las categor&iacute;as <i class="fa-solid fa-arrow-right"></i></a></div>',
            ) ),

        ) ) )
    );

    // ── SECTION 3: FEATURED PROVIDERS ──
    $data[] = ed_section(
        array(
            'layout'               => 'boxed',
            'background_background'=> 'classic',
            'background_color'     => '#F8F7F5',
            'padding'              => ed_px( 120, 0, 120, 0 ),
            'padding_tablet'       => ed_px( 80, 0, 80, 0 ),
        ),
        array( ed_column( 100, array(

            // Label
            ed_widget( 'heading', array(
                'title'                       => 'DESTACADOS',
                'header_size'                 => 'span',
                'align'                       => 'center',
                'title_color'                 => '#C8A96E',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'DM Sans',
                'typography_font_size'         => ed_size( 11 ),
                'typography_font_weight'       => '600',
                'typography_letter_spacing'    => ed_size( 3 ),
                'typography_text_transform'    => 'uppercase',
                '_margin'                      => ed_px( 0, 0, 16, 0 ),
            ) ),

            // Title
            ed_widget( 'heading', array(
                'title'                       => 'Proveedores recomendados',
                'header_size'                 => 'h2',
                'align'                       => 'center',
                'title_color'                 => '#2D2D2D',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'Playfair Display',
                'typography_font_size'         => ed_size( 42 ),
                'typography_font_size_tablet'  => ed_size( 34 ),
                'typography_font_size_mobile'  => ed_size( 26 ),
                'typography_font_weight'       => '500',
                'typography_line_height'       => ed_size( 1.2, 'em' ),
                '_margin'                      => ed_px( 0, 0, 18, 0 ),
            ) ),

            // Subtitle
            ed_widget( 'text-editor', array(
                'editor' => '<p style="text-align:center;color:#6B6B6B;font-size:16px;font-weight:300;max-width:560px;margin:0 auto;line-height:1.7;">Seleccionados por Eclipse Sevilla por su calidad y profesionalidad</p>',
                'align'  => 'center',
            ) ),

            // Divider
            ed_widget( 'html', array(
                'html'    => '<div class="ed-deco-divider"><span></span><i class="fa-solid fa-diamond"></i><span></span></div>',
                '_margin' => ed_px( 0, 0, 64, 0 ),
            ) ),

            // Featured providers shortcode
            ed_widget( 'shortcode', array(
                'shortcode' => '[eclipse_destacados]',
            ) ),

            // View all link
            ed_widget( 'html', array(
                'html' => '<div style="text-align:center;margin-top:56px;"><a href="#" class="ed-link-arrow">Ver todos los proveedores <i class="fa-solid fa-arrow-right"></i></a></div>',
            ) ),

        ) ) )
    );

    // ── SECTION 4: WHY ECLIPSE — Header ──
    $data[] = ed_section(
        array(
            'layout'         => 'boxed',
            'padding'        => ed_px( 120, 0, 0, 0 ),
            'padding_tablet' => ed_px( 80, 0, 0, 0 ),
        ),
        array( ed_column( 100, array(
            ed_label_widget( 'NUESTRA PROMESA' ),
            ed_title_widget( '&iquest;Por qu&eacute; Eclipse Sevilla?' ),
            ed_subtitle_widget( 'Porque tu evento merece lo mejor, y nosotros te ayudamos a encontrarlo', true ),
            ed_divider_widget(),
        ) ) )
    );

    // ── SECTION 4b: WHY ECLIPSE — 3 Icon Boxes ──
    $data[] = ed_section(
        array(
            'layout'         => 'boxed',
            'padding'        => ed_px( 0, 0, 120, 0 ),
            'padding_tablet' => ed_px( 0, 0, 80, 0 ),
        ),
        array(
            ed_column( 33, array(
                ed_widget( 'icon-box', array(
                    'selected_icon'    => array( 'value' => 'fas fa-gem', 'library' => 'fa-solid' ),
                    'title_text'       => 'Selecci&oacute;n curada',
                    'description_text' => 'Solo los mejores proveedores forman parte de nuestro directorio. Cada uno ha sido evaluado por su excelencia.',
                    'position'         => 'top',
                    'title_text_color'       => '#2D2D2D',
                    'description_text_color' => '#6B6B6B',
                    'primary_color'          => '#C8A96E',
                    'icon_space'             => ed_size( 20 ),
                    'title_bottom_space'     => ed_size( 14 ),
                    'title_typography_typography'    => 'custom',
                    'title_typography_font_family'   => 'Playfair Display',
                    'title_typography_font_size'     => ed_size( 20 ),
                    'title_typography_font_weight'   => '500',
                    'description_typography_typography'   => 'custom',
                    'description_typography_font_family'  => 'DM Sans',
                    'description_typography_font_size'    => ed_size( 14 ),
                    'description_typography_font_weight'  => '300',
                    'description_typography_line_height'  => ed_size( 1.7, 'em' ),
                ) ),
            ), array( 'align' => 'center', 'border_border' => 'solid', 'border_width' => ed_px( 0, 1, 0, 0 ), 'border_color' => '#E8E6E1' ) ),

            ed_column( 33, array(
                ed_widget( 'icon-box', array(
                    'selected_icon'    => array( 'value' => 'fas fa-shield-halved', 'library' => 'fa-solid' ),
                    'title_text'       => 'Confianza verificada',
                    'description_text' => 'Cada proveedor es revisado y verificado por nuestro equipo. Trabajamos solo con profesionales de confianza.',
                    'position'         => 'top',
                    'title_text_color'       => '#2D2D2D',
                    'description_text_color' => '#6B6B6B',
                    'primary_color'          => '#C8A96E',
                    'icon_space'             => ed_size( 20 ),
                    'title_bottom_space'     => ed_size( 14 ),
                    'title_typography_typography'    => 'custom',
                    'title_typography_font_family'   => 'Playfair Display',
                    'title_typography_font_size'     => ed_size( 20 ),
                    'title_typography_font_weight'   => '500',
                    'description_typography_typography'   => 'custom',
                    'description_typography_font_family'  => 'DM Sans',
                    'description_typography_font_size'    => ed_size( 14 ),
                    'description_typography_font_weight'  => '300',
                    'description_typography_line_height'  => ed_size( 1.7, 'em' ),
                ) ),
            ), array( 'align' => 'center', 'border_border' => 'solid', 'border_width' => ed_px( 0, 1, 0, 0 ), 'border_color' => '#E8E6E1' ) ),

            ed_column( 33, array(
                ed_widget( 'icon-box', array(
                    'selected_icon'    => array( 'value' => 'fas fa-heart', 'library' => 'fa-solid' ),
                    'title_text'       => 'Atenci&oacute;n personalizada',
                    'description_text' => 'Te ayudamos a encontrar el proveedor perfecto para tu evento. Estamos contigo en cada paso.',
                    'position'         => 'top',
                    'title_text_color'       => '#2D2D2D',
                    'description_text_color' => '#6B6B6B',
                    'primary_color'          => '#C8A96E',
                    'icon_space'             => ed_size( 20 ),
                    'title_bottom_space'     => ed_size( 14 ),
                    'title_typography_typography'    => 'custom',
                    'title_typography_font_family'   => 'Playfair Display',
                    'title_typography_font_size'     => ed_size( 20 ),
                    'title_typography_font_weight'   => '500',
                    'description_typography_typography'   => 'custom',
                    'description_typography_font_family'  => 'DM Sans',
                    'description_typography_font_size'    => ed_size( 14 ),
                    'description_typography_font_weight'  => '300',
                    'description_typography_line_height'  => ed_size( 1.7, 'em' ),
                ) ),
            ), array( 'align' => 'center' ) ),
        )
    );

    // ── SECTION 5: CTA ──
    $data[] = ed_section(
        array(
            'layout'                    => 'full_width',
            'background_background'     => 'gradient',
            'background_color'          => '#1A1A2E',
            'background_color_stop'     => ed_size( 0, '%' ),
            'background_color_b'        => '#3D2A32',
            'background_color_b_stop'   => ed_size( 100, '%' ),
            'background_gradient_angle' => ed_size( 135, 'deg' ),
            'padding'                   => ed_px( 120, 0, 120, 0 ),
            'padding_tablet'            => ed_px( 80, 0, 80, 0 ),
            'css_classes'               => 'ed-cta-section',
        ),
        array( ed_column( 100, array(

            // Label
            ed_widget( 'heading', array(
                'title'                       => 'PARA PROVEEDORES',
                'header_size'                 => 'span',
                'align'                       => 'center',
                'title_color'                 => '#C8A96E',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'DM Sans',
                'typography_font_size'         => ed_size( 11 ),
                'typography_font_weight'       => '600',
                'typography_letter_spacing'    => ed_size( 3 ),
                'typography_text_transform'    => 'uppercase',
                '_margin'                      => ed_px( 0, 0, 16, 0 ),
            ) ),

            // Title
            ed_widget( 'heading', array(
                'title'                       => '&iquest;Eres proveedor de eventos en Sevilla?',
                'header_size'                 => 'h2',
                'align'                       => 'center',
                'title_color'                 => '#FFFFFF',
                'typography_typography'        => 'custom',
                'typography_font_family'       => 'Playfair Display',
                'typography_font_size'         => ed_size( 42 ),
                'typography_font_size_tablet'  => ed_size( 34 ),
                'typography_font_size_mobile'  => ed_size( 26 ),
                'typography_font_weight'       => '500',
                '_margin'                      => ed_px( 0, 0, 20, 0 ),
            ) ),

            // Subtitle
            ed_widget( 'text-editor', array(
                'editor' => '<p style="text-align:center;color:rgba(255,255,255,0.55);font-size:16px;font-weight:300;max-width:560px;margin:0 auto;line-height:1.7;">&Uacute;nete a la selecci&oacute;n de profesionales recomendados por Eclipse Sevilla y conecta con clientes que buscan calidad</p>',
                'align'  => 'center',
                '_margin' => ed_px( 0, 0, 44, 0 ),
            ) ),

            // CTA Button
            ed_widget( 'button', array(
                'text'                           => 'Quiero aparecer en el directorio',
                'align'                          => 'center',
                'button_type'                    => 'default',
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

    // ── SECTION 6: FOOTER — Main ──
    $data[] = ed_section(
        array(
            'layout'                => 'boxed',
            'background_background' => 'classic',
            'background_color'      => '#12121F',
            'padding'               => ed_px( 80, 24, 0, 24 ),
        ),
        array(
            // Column 1: Brand
            ed_column( 35, array(
                ed_widget( 'html', array(
                    'html' => '<a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:20px;"><span style="width:32px;height:32px;border:1.5px solid #C8A96E;border-radius:50%;display:flex;align-items:center;justify-content:center;"><span style="width:8px;height:8px;background:#C8A96E;border-radius:50%;display:block;"></span></span><span style="font-family:Playfair Display,serif;font-size:20px;color:#fff;">Eclipse <em style="color:#C8A96E;">Sevilla</em></span></a>',
                ) ),
                ed_widget( 'text-editor', array(
                    'editor'       => '<p>El directorio de referencia para proveedores de eventos en Sevilla. Calidad, confianza y profesionalidad en cada recomendaci&oacute;n.</p>',
                    'text_color'   => 'rgba(255,255,255,0.4)',
                    'typography_typography'   => 'custom',
                    'typography_font_family'  => 'DM Sans',
                    'typography_font_size'    => ed_size( 14 ),
                    'typography_font_weight'  => '300',
                    'typography_line_height'  => ed_size( 1.7, 'em' ),
                    '_margin'                 => ed_px( 0, 0, 24, 0 ),
                ) ),
                ed_widget( 'social-icons', array(
                    'social_icon_list' => array(
                        array( 'social_icon' => array( 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ), 'link' => array( 'url' => '#' ) ),
                        array( 'social_icon' => array( 'value' => 'fab fa-facebook-f', 'library' => 'fa-brands' ), 'link' => array( 'url' => '#' ) ),
                        array( 'social_icon' => array( 'value' => 'fab fa-linkedin-in', 'library' => 'fa-brands' ), 'link' => array( 'url' => '#' ) ),
                        array( 'social_icon' => array( 'value' => 'fab fa-pinterest-p', 'library' => 'fa-brands' ), 'link' => array( 'url' => '#' ) ),
                    ),
                    'icon_color'   => 'custom',
                    'icon_primary_color' => 'rgba(255,255,255,0.4)',
                    'icon_secondary_color' => 'transparent',
                    'icon_size'    => ed_size( 14 ),
                    'icon_spacing' => ed_size( 12 ),
                    'skin'         => 'framed',
                    'shape'        => 'circle',
                    'columns'      => '4',
                    'align'        => 'left',
                ) ),
            ) ),

            // Column 2: Categories
            ed_column( 22, array(
                ed_widget( 'heading', array(
                    'title'       => 'CATEGOR&Iacute;AS',
                    'header_size' => 'h4',
                    'title_color' => 'rgba(255,255,255,0.5)',
                    'typography_typography'     => 'custom',
                    'typography_font_family'    => 'DM Sans',
                    'typography_font_size'      => ed_size( 11 ),
                    'typography_font_weight'    => '600',
                    'typography_letter_spacing' => ed_size( 2 ),
                    '_margin'                   => ed_px( 0, 0, 24, 0 ),
                ) ),
                ed_widget( 'icon-list', array(
                    'icon_list' => array(
                        array( 'text' => 'Espacios', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Catering', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Fotograf&iacute;a y V&iacute;deo', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'M&uacute;sica y Animaci&oacute;n', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Ambientaci&oacute;n', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Belleza', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                    ),
                    'icon_color'  => 'rgba(255,255,255,0.35)',
                    'text_color'  => 'rgba(255,255,255,0.35)',
                    'text_color_hover' => '#C8A96E',
                    'typography_typography'  => 'custom',
                    'typography_font_family' => 'DM Sans',
                    'typography_font_size'   => ed_size( 14 ),
                    'typography_font_weight' => '300',
                    'icon_self_align'        => 'flex-start',
                    'space_between'          => ed_size( 12 ),
                ) ),
            ) ),

            // Column 3: For Providers
            ed_column( 22, array(
                ed_widget( 'heading', array(
                    'title'       => 'PARA PROVEEDORES',
                    'header_size' => 'h4',
                    'title_color' => 'rgba(255,255,255,0.5)',
                    'typography_typography'     => 'custom',
                    'typography_font_family'    => 'DM Sans',
                    'typography_font_size'      => ed_size( 11 ),
                    'typography_font_weight'    => '600',
                    'typography_letter_spacing' => ed_size( 2 ),
                    '_margin'                   => ed_px( 0, 0, 24, 0 ),
                ) ),
                ed_widget( 'icon-list', array(
                    'icon_list' => array(
                        array( 'text' => 'An&uacute;nciate', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Planes y precios', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Preguntas frecuentes', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                        array( 'text' => 'Contacto', 'link' => array( 'url' => '#' ), 'selected_icon' => array( 'value' => '' ) ),
                    ),
                    'icon_color'  => 'rgba(255,255,255,0.35)',
                    'text_color'  => 'rgba(255,255,255,0.35)',
                    'text_color_hover' => '#C8A96E',
                    'typography_typography'  => 'custom',
                    'typography_font_family' => 'DM Sans',
                    'typography_font_size'   => ed_size( 14 ),
                    'typography_font_weight' => '300',
                    'space_between'          => ed_size( 12 ),
                ) ),
            ) ),

            // Column 4: Contact
            ed_column( 21, array(
                ed_widget( 'heading', array(
                    'title'       => 'CONTACTO',
                    'header_size' => 'h4',
                    'title_color' => 'rgba(255,255,255,0.5)',
                    'typography_typography'     => 'custom',
                    'typography_font_family'    => 'DM Sans',
                    'typography_font_size'      => ed_size( 11 ),
                    'typography_font_weight'    => '600',
                    'typography_letter_spacing' => ed_size( 2 ),
                    '_margin'                   => ed_px( 0, 0, 24, 0 ),
                ) ),
                ed_widget( 'icon-list', array(
                    'icon_list' => array(
                        array( 'text' => 'hola@eclipsesevilla.com', 'selected_icon' => array( 'value' => 'fas fa-envelope', 'library' => 'fa-solid' ), 'link' => array( 'url' => 'mailto:hola@eclipsesevilla.com' ) ),
                        array( 'text' => '+34 654 123 456', 'selected_icon' => array( 'value' => 'fas fa-phone', 'library' => 'fa-solid' ), 'link' => array( 'url' => 'tel:+34654123456' ) ),
                        array( 'text' => 'Sevilla, Espa&ntilde;a', 'selected_icon' => array( 'value' => 'fas fa-location-dot', 'library' => 'fa-solid' ), 'link' => array( 'url' => '' ) ),
                    ),
                    'icon_color'  => '#C8A96E',
                    'text_color'  => 'rgba(255,255,255,0.35)',
                    'text_color_hover' => 'rgba(255,255,255,0.55)',
                    'typography_typography'  => 'custom',
                    'typography_font_family' => 'DM Sans',
                    'typography_font_size'   => ed_size( 14 ),
                    'typography_font_weight' => '300',
                    'space_between'          => ed_size( 14 ),
                ) ),
            ) ),
        )
    );

    // ── SECTION 7: FOOTER — Bottom bar ──
    $data[] = ed_section(
        array(
            'layout'                => 'boxed',
            'background_background' => 'classic',
            'background_color'      => '#12121F',
            'padding'               => ed_px( 24, 24, 24, 24 ),
            'border_border'         => 'solid',
            'border_width'          => ed_px( 1, 0, 0, 0 ),
            'border_color'          => 'rgba(255,255,255,0.06)',
        ),
        array(
            ed_column( 50, array(
                ed_widget( 'text-editor', array(
                    'editor'     => '<p>&copy; 2026 Eclipse Sevilla. Todos los derechos reservados.</p>',
                    'text_color' => 'rgba(255,255,255,0.25)',
                    'typography_typography'  => 'custom',
                    'typography_font_family' => 'DM Sans',
                    'typography_font_size'   => ed_size( 12 ),
                    'typography_font_weight' => '300',
                ) ),
            ) ),
            ed_column( 50, array(
                ed_widget( 'text-editor', array(
                    'editor' => '<p style="text-align:right;"><a href="#" style="color:rgba(255,255,255,0.25);text-decoration:none;font-size:12px;margin-left:24px;">Aviso legal</a><a href="#" style="color:rgba(255,255,255,0.25);text-decoration:none;font-size:12px;margin-left:24px;">Privacidad</a><a href="#" style="color:rgba(255,255,255,0.25);text-decoration:none;font-size:12px;margin-left:24px;">Cookies</a></p>',
                    'align'  => 'right',
                ) ),
            ) ),
        )
    );

    return $data;
}

function ed_get_why_html() {
    return '
    <style>
    .ed-why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
    .ed-why-item { text-align: center; padding: 48px 40px; position: relative; }
    .ed-why-item:not(:last-child)::after { content: ""; position: absolute; right: 0; top: 20%; height: 60%; width: 1px; background: #E8E6E1; }
    .ed-why-icon { width: 52px; height: 52px; margin: 0 auto 28px; display: flex; align-items: center; justify-content: center; border: 1px solid #C8A96E; border-radius: 50%; color: #C8A96E; font-size: 18px; opacity: 0.8; }
    .ed-why-item h3 { font-family: "Playfair Display", serif; font-size: 20px; font-weight: 500; color: #2D2D2D; margin-bottom: 14px; }
    .ed-why-item p { font-family: "DM Sans", sans-serif; font-size: 14px; color: #6B6B6B; line-height: 1.7; max-width: 280px; margin: 0 auto; font-weight: 300; }
    @media (max-width: 768px) {
        .ed-why-grid { grid-template-columns: 1fr; }
        .ed-why-item::after { display: none !important; }
        .ed-why-item { padding: 36px 20px; border-bottom: 1px solid #E8E6E1; }
        .ed-why-item:last-child { border-bottom: none; }
    }
    </style>
    <div class="ed-why-grid">
        <div class="ed-why-item">
            <div class="ed-why-icon"><i class="fa-solid fa-gem"></i></div>
            <h3>Selecci&oacute;n curada</h3>
            <p>Solo los mejores proveedores forman parte de nuestro directorio. Cada uno ha sido evaluado por su excelencia.</p>
        </div>
        <div class="ed-why-item">
            <div class="ed-why-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <h3>Confianza verificada</h3>
            <p>Cada proveedor es revisado y verificado por nuestro equipo. Trabajamos solo con profesionales de confianza.</p>
        </div>
        <div class="ed-why-item">
            <div class="ed-why-icon"><i class="fa-solid fa-heart"></i></div>
            <h3>Atenci&oacute;n personalizada</h3>
            <p>Te ayudamos a encontrar el proveedor perfecto para tu evento. Estamos contigo en cada paso.</p>
        </div>
    </div>';
}

function ed_get_footer_html() {
    return '
    <style>
    .ed-footer { font-family: "DM Sans", sans-serif; }
    .ed-footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 48px; padding-bottom: 64px; border-bottom: 1px solid rgba(255,255,255,0.06); }
    .ed-footer .ed-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 20px; }
    .ed-footer .ed-logo-icon { width: 32px; height: 32px; border: 1.5px solid #C8A96E; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .ed-footer .ed-logo-icon::after { content: ""; width: 8px; height: 8px; background: #C8A96E; border-radius: 50%; }
    .ed-footer .ed-logo-text { font-family: "Playfair Display", serif; font-size: 20px; font-weight: 500; color: #fff; letter-spacing: 1px; }
    .ed-footer .ed-logo-text em { font-style: italic; color: #C8A96E; font-weight: 400; }
    .ed-footer-brand p { font-size: 14px; color: rgba(255,255,255,0.4); line-height: 1.7; margin-bottom: 24px; max-width: 280px; font-weight: 300; }
    .ed-footer-social { display: flex; gap: 16px; }
    .ed-footer-social a { width: 36px; height: 36px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.4); font-size: 14px; text-decoration: none; transition: border-color 0.3s, color 0.3s; }
    .ed-footer-social a:hover { border-color: #C8A96E; color: #C8A96E; }
    .ed-footer-col h4 { font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 24px; }
    .ed-footer-col ul { list-style: none; padding: 0; margin: 0; }
    .ed-footer-col ul li { margin-bottom: 12px; }
    .ed-footer-col ul a { font-size: 14px; color: rgba(255,255,255,0.35); text-decoration: none; font-weight: 300; transition: color 0.3s; }
    .ed-footer-col ul a:hover { color: #C8A96E; }
    .ed-footer-col .ed-contact-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .ed-footer-col .ed-contact-item i { color: #C8A96E; font-size: 13px; margin-top: 3px; opacity: 0.6; }
    .ed-footer-col .ed-contact-item span { font-size: 14px; color: rgba(255,255,255,0.35); font-weight: 300; line-height: 1.5; }
    .ed-footer-bottom { padding: 24px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .ed-footer-bottom p { font-size: 12px; color: rgba(255,255,255,0.25); font-weight: 300; }
    .ed-footer-legal { display: flex; gap: 24px; }
    .ed-footer-legal a { font-size: 12px; color: rgba(255,255,255,0.25); text-decoration: none; font-weight: 300; transition: color 0.3s; }
    .ed-footer-legal a:hover { color: rgba(255,255,255,0.5); }
    @media (max-width: 1024px) { .ed-footer-grid { grid-template-columns: 1fr 1fr; gap: 40px; } }
    @media (max-width: 768px) { .ed-footer-grid { grid-template-columns: 1fr; gap: 36px; } .ed-footer-bottom { flex-direction: column; text-align: center; } }
    </style>
    <div class="ed-footer">
      <div class="ed-footer-grid">
        <div class="ed-footer-brand">
          <a href="#" class="ed-logo">
            <div class="ed-logo-icon"></div>
            <span class="ed-logo-text">Eclipse <em>Sevilla</em></span>
          </a>
          <p>El directorio de referencia para proveedores de eventos en Sevilla. Calidad, confianza y profesionalidad en cada recomendaci&oacute;n.</p>
          <div class="ed-footer-social">
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
          </div>
        </div>
        <div class="ed-footer-col">
          <h4>Categor&iacute;as</h4>
          <ul>
            <li><a href="#">Espacios</a></li>
            <li><a href="#">Catering</a></li>
            <li><a href="#">Fotograf&iacute;a y V&iacute;deo</a></li>
            <li><a href="#">M&uacute;sica y Animaci&oacute;n</a></li>
            <li><a href="#">Ambientaci&oacute;n</a></li>
            <li><a href="#">Belleza</a></li>
          </ul>
        </div>
        <div class="ed-footer-col">
          <h4>Para proveedores</h4>
          <ul>
            <li><a href="#">An&uacute;nciate</a></li>
            <li><a href="#">Planes y precios</a></li>
            <li><a href="#">Preguntas frecuentes</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>
        <div class="ed-footer-col">
          <h4>Contacto</h4>
          <div class="ed-contact-item"><i class="fa-solid fa-envelope"></i><span>hola@eclipsesevilla.com</span></div>
          <div class="ed-contact-item"><i class="fa-solid fa-phone"></i><span>+34 654 123 456</span></div>
          <div class="ed-contact-item"><i class="fa-solid fa-location-dot"></i><span>Sevilla, Espa&ntilde;a</span></div>
        </div>
      </div>
      <div class="ed-footer-bottom">
        <p>&copy; 2026 Eclipse Sevilla. Todos los derechos reservados.</p>
        <div class="ed-footer-legal">
          <a href="#">Aviso legal</a>
          <a href="#">Privacidad</a>
          <a href="#">Cookies</a>
        </div>
      </div>
    </div>';
}
