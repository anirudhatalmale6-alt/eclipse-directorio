<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_listado', 'ed_render_listado_shortcode' );

function ed_render_listado_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'per_page' => 12,
        'columns'  => 3,
    ), $atts );

    $paged    = max( 1, get_query_var( 'paged', 1 ) );
    $per_page = intval( $atts['per_page'] );
    $columns  = intval( $atts['columns'] );

    $args = array(
        'post_type'      => 'proveedor',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( is_tax( 'categoria_servicio' ) ) {
        $term = get_queried_object();
        if ( $term ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'categoria_servicio',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ),
            );
        }
    } elseif ( is_tax( 'zona_servicio' ) ) {
        $term = get_queried_object();
        if ( $term ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'zona_servicio',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ),
            );
        }
    }

    if ( ! empty( $_GET['buscar'] ) ) {
        $args['s'] = sanitize_text_field( wp_unslash( $_GET['buscar'] ) );
    }
    if ( ! empty( $_GET['zona'] ) ) {
        $zona_slug = sanitize_text_field( wp_unslash( $_GET['zona'] ) );
        if ( $zona_slug ) {
            $args['tax_query']   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
            $args['tax_query'][] = array(
                'taxonomy' => 'zona_servicio',
                'field'    => 'slug',
                'terms'    => $zona_slug,
            );
            if ( count( $args['tax_query'] ) > 1 ) {
                $args['tax_query']['relation'] = 'AND';
            }
        }
    }

    $query = new WP_Query( $args );

    ob_start();
    ?>
    <div class="ed-listado-container">
        <?php if ( $query->have_posts() ) : ?>
            <div class="ed-listado-count">
                <?php
                $total = $query->found_posts;
                printf(
                    '<span>%d %s encontrado%s</span>',
                    $total,
                    $total === 1 ? 'proveedor' : 'proveedores',
                    $total === 1 ? '' : 's'
                );
                ?>
            </div>
            <div class="ed-listado-grid ed-listado-cols-<?php echo esc_attr( $columns ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post();
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

                    $descripcion = '';
                    if ( function_exists( 'get_field' ) ) {
                        $descripcion = get_field( 'descripcion_corta' );
                    }
                    if ( ! $descripcion ) {
                        $descripcion = get_the_excerpt();
                    }

                    $es_recomendado = false;
                    if ( function_exists( 'get_field' ) ) {
                        $es_recomendado = get_field( 'destacado' );
                    }
                ?>
                <a href="<?php the_permalink(); ?>" class="ed-listado-card">
                    <div class="ed-listado-card-img">
                        <?php if ( $thumbnail ) : ?>
                            <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $nombre ); ?>" loading="lazy">
                        <?php else : ?>
                            <div class="ed-listado-card-placeholder">
                                <i class="fa-solid fa-building"></i>
                            </div>
                        <?php endif; ?>
                        <?php if ( $es_recomendado ) : ?>
                            <span class="ed-listado-badge">Recomendado</span>
                        <?php endif; ?>
                    </div>
                    <div class="ed-listado-card-body">
                        <h3><?php echo esc_html( $nombre ); ?></h3>
                        <?php if ( $cat_name || $zona_name ) : ?>
                        <div class="ed-listado-meta">
                            <?php if ( $cat_name ) : ?><span class="ed-listado-cat"><?php echo esc_html( $cat_name ); ?></span><?php endif; ?>
                            <?php if ( $zona_name ) : ?><span class="ed-listado-zona"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $zona_name ); ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ( $descripcion ) : ?>
                        <p><?php echo esc_html( wp_trim_words( $descripcion, 18 ) ); ?></p>
                        <?php endif; ?>
                        <span class="ed-listado-link">Ver perfil <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>

            <?php
            $total_pages = $query->max_num_pages;
            if ( $total_pages > 1 ) :
            ?>
            <div class="ed-listado-pagination">
                <?php
                echo paginate_links( array(
                    'total'     => $total_pages,
                    'current'   => $paged,
                    'prev_text' => '<i class="fa-solid fa-chevron-left"></i>',
                    'next_text' => '<i class="fa-solid fa-chevron-right"></i>',
                    'type'      => 'list',
                ) );
                ?>
            </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="ed-listado-empty">
                <i class="fa-solid fa-search"></i>
                <h3>No se encontraron proveedores</h3>
                <p>Intenta con otra b&uacute;squeda o categor&iacute;a.</p>
                <a href="<?php echo esc_url( home_url( '/directorio/' ) ); ?>" class="ed-btn-outline">Volver al directorio</a>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
