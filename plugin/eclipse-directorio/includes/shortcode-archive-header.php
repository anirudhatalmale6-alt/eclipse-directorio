<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_archive_header', 'ed_render_archive_header_shortcode' );

function ed_render_archive_header_shortcode( $atts ) {
    $term = get_queried_object();
    if ( ! $term || ! is_a( $term, 'WP_Term' ) ) {
        return '';
    }

    $taxonomy = $term->taxonomy;
    $name     = $term->name;
    $desc     = $term->description;
    $count    = $term->count;

    $imagen        = '';
    $color_overlay = '';
    $icono         = '';
    if ( function_exists( 'get_field' ) && $taxonomy === 'categoria_servicio' ) {
        $imagen        = get_field( 'categoria_imagen', 'categoria_servicio_' . $term->term_id );
        $color_overlay = get_field( 'categoria_color_overlay', 'categoria_servicio_' . $term->term_id );
        $icono         = get_field( 'categoria_icono', 'categoria_servicio_' . $term->term_id );
    }

    $children = get_terms( array(
        'taxonomy'   => $taxonomy,
        'parent'     => $term->term_id,
        'hide_empty' => false,
        'orderby'    => 'name',
    ) );

    ob_start();
    ?>
    <div class="ed-archive-header">
        <?php if ( $imagen ) : ?>
            <div class="ed-archive-header-bg">
                <img src="<?php echo esc_url( $imagen ); ?>" alt="<?php echo esc_attr( $name ); ?>">
            </div>
        <?php endif; ?>
        <div class="ed-archive-header-overlay"<?php
            if ( $color_overlay ) {
                echo ' style="background: linear-gradient(to bottom, ' . esc_attr( $color_overlay ) . 'dd 0%, ' . esc_attr( $color_overlay ) . 'f5 100%);"';
            }
        ?>></div>
        <div class="ed-archive-header-content">
            <div class="ed-archive-breadcrumbs">
                <a href="<?php echo esc_url( home_url( '/directorio/' ) ); ?>">Directorio</a>
                <span class="ed-sep">/</span>
                <?php if ( $term->parent ) :
                    $parent = get_term( $term->parent, $taxonomy );
                    if ( $parent && ! is_wp_error( $parent ) ) :
                ?>
                    <a href="<?php echo esc_url( get_term_link( $parent ) ); ?>"><?php echo esc_html( $parent->name ); ?></a>
                    <span class="ed-sep">/</span>
                <?php endif; endif; ?>
                <span><?php echo esc_html( $name ); ?></span>
            </div>

            <?php if ( $icono ) : ?>
                <div class="ed-archive-icon"><i class="<?php echo esc_attr( $icono ); ?>"></i></div>
            <?php endif; ?>

            <h1><?php echo esc_html( $name ); ?></h1>

            <?php if ( $desc ) : ?>
                <p class="ed-archive-desc"><?php echo esc_html( $desc ); ?></p>
            <?php endif; ?>

            <span class="ed-archive-count"><?php echo intval( $count ); ?> proveedores</span>

            <?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
            <div class="ed-subcategories">
                <?php foreach ( $children as $child ) :
                    $child_link = get_term_link( $child );
                    if ( is_wp_error( $child_link ) ) continue;
                ?>
                    <a href="<?php echo esc_url( $child_link ); ?>" class="ed-subcategory-tag"><?php echo esc_html( $child->name ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
