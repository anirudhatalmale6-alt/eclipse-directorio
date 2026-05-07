<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_buscar', 'ed_render_buscar_shortcode' );

function ed_render_buscar_shortcode( $atts ) {
    $categorias = get_terms( array(
        'taxonomy'   => 'categoria_servicio',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
    ) );

    $zonas = get_terms( array(
        'taxonomy'   => 'zona_servicio',
        'hide_empty' => false,
        'orderby'    => 'name',
    ) );

    ob_start();
    ?>
    <form class="ed-search-bar" action="<?php echo esc_url( home_url( '/directorio/' ) ); ?>" method="get">
        <div class="ed-search-field">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="buscar" placeholder="&iquest;Qu&eacute; buscas?">
        </div>
        <div class="ed-search-field">
            <i class="fa-solid fa-layer-group"></i>
            <select name="categoria">
                <option value="" disabled selected>Categor&iacute;a</option>
                <?php if ( ! is_wp_error( $categorias ) ) : foreach ( $categorias as $cat ) : ?>
                    <option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <div class="ed-search-field">
            <i class="fa-solid fa-location-dot"></i>
            <select name="zona">
                <option value="" disabled selected>Zona</option>
                <?php if ( ! is_wp_error( $zonas ) ) : foreach ( $zonas as $zona ) : ?>
                    <option value="<?php echo esc_attr( $zona->slug ); ?>"><?php echo esc_html( $zona->name ); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        <button type="submit" class="ed-search-btn">Buscar</button>
    </form>
    <?php
    return ob_get_clean();
}
