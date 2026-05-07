<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_categorias', 'ed_render_categorias_shortcode' );

function ed_render_categorias_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'limit' => 6 ), $atts );

    $categorias = get_terms( array(
        'taxonomy'   => 'categoria_servicio',
        'hide_empty' => false,
        'parent'     => 0,
        'number'     => intval( $atts['limit'] ),
        'orderby'    => 'name',
    ) );

    if ( is_wp_error( $categorias ) || empty( $categorias ) ) {
        return '';
    }

    $fallback_gradients = array(
        'espacios'               => 'linear-gradient(135deg, #4A3628 0%, #8B6F52 100%)',
        'catering'               => 'linear-gradient(135deg, #3D2B28 0%, #8B4A38 100%)',
        'restaurantes'           => 'linear-gradient(135deg, #3D2B28 0%, #906048 100%)',
        'centros-de-belleza'     => 'linear-gradient(135deg, #3D2838 0%, #8B4880 100%)',
        'animacion'              => 'linear-gradient(135deg, #2E2838 0%, #6B4888 100%)',
        'grupos-musicales'       => 'linear-gradient(135deg, #2E2838 0%, #6B4888 100%)',
        'ambientacion'           => 'linear-gradient(135deg, #283D2E 0%, #4A8B50 100%)',
        'servicios-audiovisuales'=> 'linear-gradient(135deg, #2B2D3D 0%, #4A508B 100%)',
        'transportes'            => 'linear-gradient(135deg, #2B3D2D 0%, #4A7B50 100%)',
        'fotografia-y-video'     => 'linear-gradient(135deg, #2B2D3D 0%, #4A508B 100%)',
        'alojamiento'            => 'linear-gradient(135deg, #3A3628 0%, #7B6F52 100%)',
    );

    ob_start();
    ?>
    <div class="ed-categories-grid">
        <?php foreach ( $categorias as $cat ) :
            $count    = $cat->count;
            $link     = get_term_link( $cat );
            if ( is_wp_error( $link ) ) {
                $link = '#';
            }

            $imagen        = '';
            $color_overlay = '';
            $icono         = '';
            if ( function_exists( 'get_field' ) ) {
                $imagen        = get_field( 'categoria_imagen', 'categoria_servicio_' . $cat->term_id );
                $color_overlay = get_field( 'categoria_color_overlay', 'categoria_servicio_' . $cat->term_id );
                $icono         = get_field( 'categoria_icono', 'categoria_servicio_' . $cat->term_id );
            }

            $has_image = ! empty( $imagen );
            $gradient  = isset( $fallback_gradients[ $cat->slug ] ) ? $fallback_gradients[ $cat->slug ] : 'linear-gradient(135deg, #4A3628 0%, #A08060 100%)';

            $overlay_style = '';
            if ( $color_overlay ) {
                $overlay_style = 'background: linear-gradient(to top, ' . esc_attr( $color_overlay ) . 'cc 0%, ' . esc_attr( $color_overlay ) . '33 50%, ' . esc_attr( $color_overlay ) . '1a 100%);';
            }
        ?>
        <a href="<?php echo esc_url( $link ); ?>" class="ed-category-card<?php echo $has_image ? ' has-image' : ''; ?>">
            <?php if ( $has_image ) : ?>
                <div class="ed-category-card-bg">
                    <img src="<?php echo esc_url( $imagen ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy">
                </div>
            <?php else : ?>
                <div class="ed-category-card-bg" style="background: <?php echo esc_attr( $gradient ); ?>"></div>
            <?php endif; ?>
            <?php if ( $overlay_style ) : ?>
                <div class="ed-category-card-overlay" style="<?php echo $overlay_style; ?>"></div>
            <?php endif; ?>
            <div class="ed-category-card-content">
                <?php if ( $icono ) : ?>
                    <i class="<?php echo esc_attr( $icono ); ?> ed-category-icon"></i>
                <?php endif; ?>
                <h3><?php echo esc_html( $cat->name ); ?></h3>
                <span class="ed-count"><?php echo intval( $count ); ?> proveedores</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
