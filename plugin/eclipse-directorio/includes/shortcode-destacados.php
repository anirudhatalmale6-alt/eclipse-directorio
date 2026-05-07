<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_destacados', 'ed_render_destacados_shortcode' );

function ed_render_destacados_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'limit' => 3 ), $atts );

    $query = new WP_Query( array(
        'post_type'      => 'proveedor',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $placeholder_gradients = array(
        'linear-gradient(145deg, #5A4A3A 0%, #C8A080 100%)',
        'linear-gradient(145deg, #4A3A2A 0%, #B89070 100%)',
        'linear-gradient(145deg, #3A3A4A 0%, #8888B8 100%)',
    );

    ob_start();
    ?>
    <div class="ed-featured-grid">
        <?php if ( $query->have_posts() ) : ?>
            <?php $i = 0; while ( $query->have_posts() ) : $query->the_post();
                $nombre = '';
                if ( function_exists( 'get_field' ) ) {
                    $nombre = get_field( 'nombre_negocio' );
                }
                if ( ! $nombre ) {
                    $nombre = get_the_title();
                }

                $cat_terms  = get_the_terms( get_the_ID(), 'categoria_servicio' );
                $zona_terms = get_the_terms( get_the_ID(), 'zona_servicio' );
                $cat_name   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';
                $zona_name  = ( $zona_terms && ! is_wp_error( $zona_terms ) ) ? $zona_terms[0]->name : '';
                $thumbnail  = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                $excerpt    = get_the_excerpt();
                $grad       = $placeholder_gradients[ $i % 3 ];
            ?>
            <div class="ed-provider-card">
                <div class="ed-provider-card-img">
                    <?php if ( $thumbnail ) : ?>
                        <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $nombre ); ?>">
                    <?php else : ?>
                        <div class="ed-provider-placeholder" style="background: <?php echo esc_attr( $grad ); ?>"></div>
                    <?php endif; ?>
                    <span class="ed-provider-badge">Recomendado</span>
                </div>
                <div class="ed-provider-card-body">
                    <h3><?php echo esc_html( $nombre ); ?></h3>
                    <?php if ( $cat_name || $zona_name ) : ?>
                    <div class="ed-provider-meta">
                        <?php if ( $cat_name ) : ?><span><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
                        <?php if ( $cat_name && $zona_name ) : ?><span class="ed-dot"></span><?php endif; ?>
                        <?php if ( $zona_name ) : ?><span><?php echo esc_html( $zona_name ); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ( $excerpt ) : ?>
                    <p><?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?></p>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="ed-btn-outline">Ver perfil</a>
                </div>
            </div>
            <?php $i++; endwhile; ?>
        <?php else : ?>
            <div class="ed-no-providers">
                <p>Pr&oacute;ximamente a&ntilde;adiremos proveedores destacados a esta secci&oacute;n.</p>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
