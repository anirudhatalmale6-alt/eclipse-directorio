<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_proveedor_perfil', 'ed_render_proveedor_perfil' );

function ed_render_proveedor_perfil( $atts ) {
    if ( ! is_singular( 'proveedor' ) ) {
        return '';
    }

    $post_id = get_the_ID();
    if ( ! function_exists( 'get_field' ) ) {
        return '<p>ACF PRO is required.</p>';
    }

    $nombre       = get_field( 'nombre_negocio', $post_id ) ?: get_the_title();
    $titulo       = get_field( 'titulo_profesional', $post_id );
    $logo         = get_field( 'logo', $post_id );
    $galeria      = get_field( 'galeria_fotos', $post_id );
    $telefono_1   = get_field( 'telefono_1', $post_id );
    $telefono_2   = get_field( 'telefono_2', $post_id );
    $email        = get_field( 'email', $post_id );
    $whatsapp     = get_field( 'whatsapp', $post_id );
    $sitio_web    = get_field( 'sitio_web', $post_id );
    $instagram    = get_field( 'instagram', $post_id );
    $facebook     = get_field( 'facebook', $post_id );
    $tiktok       = get_field( 'tiktok', $post_id );
    $youtube      = get_field( 'youtube', $post_id );
    $linkedin     = get_field( 'linkedin', $post_id );
    $direccion    = get_field( 'direccion', $post_id );
    $cod_postal   = get_field( 'codigo_postal', $post_id );
    $desc_corta   = get_field( 'descripcion_corta', $post_id );
    $precio_min   = get_field( 'rango_precio_min', $post_id );
    $precio_max   = get_field( 'rango_precio_max', $post_id );
    $horario      = get_field( 'horario', $post_id );
    $verificado   = get_field( 'verificado', $post_id );
    $destacado    = get_field( 'destacado', $post_id );
    $contenido    = get_the_content();
    $thumbnail    = get_the_post_thumbnail_url( $post_id, 'large' );

    $cat_terms  = get_the_terms( $post_id, 'categoria_servicio' );
    $zona_terms = get_the_terms( $post_id, 'zona_servicio' );
    $cat_name   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';
    $cat_slug   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->slug : '';
    $cat_link   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? get_term_link( $cat_terms[0] ) : '';
    $zona_name  = ( $zona_terms && ! is_wp_error( $zona_terms ) ) ? $zona_terms[0]->name : '';

    ob_start();
    ?>
    <div class="ed-single-proveedor">

        <!-- Hero Banner -->
        <div class="ed-single-hero">
            <?php if ( $thumbnail ) : ?>
                <div class="ed-single-hero-bg">
                    <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $nombre ); ?>">
                </div>
            <?php endif; ?>
            <div class="ed-single-hero-overlay"></div>
            <div class="ed-single-hero-content">
                <div class="ed-single-breadcrumbs">
                    <a href="<?php echo esc_url( home_url( '/directorio/' ) ); ?>">Directorio</a>
                    <span class="ed-sep">/</span>
                    <?php if ( $cat_name && ! is_wp_error( $cat_link ) ) : ?>
                        <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                        <span class="ed-sep">/</span>
                    <?php endif; ?>
                    <span><?php echo esc_html( $nombre ); ?></span>
                </div>
                <div class="ed-single-hero-badges">
                    <?php if ( $destacado ) : ?>
                        <span class="ed-badge ed-badge-gold"><i class="fa-solid fa-star"></i> Recomendado</span>
                    <?php endif; ?>
                    <?php if ( $verificado ) : ?>
                        <span class="ed-badge ed-badge-green"><i class="fa-solid fa-circle-check"></i> Verificado</span>
                    <?php endif; ?>
                </div>
                <h1><?php echo esc_html( $nombre ); ?></h1>
                <?php if ( $titulo ) : ?>
                    <p class="ed-single-titulo"><?php echo esc_html( $titulo ); ?></p>
                <?php endif; ?>
                <div class="ed-single-hero-meta">
                    <?php if ( $cat_name ) : ?>
                        <span class="ed-single-cat"><?php echo esc_html( $cat_name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $zona_name ) : ?>
                        <span class="ed-single-zona"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $zona_name ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ed-single-body">
            <div class="ed-single-main">

                <!-- Description -->
                <?php if ( $desc_corta || $contenido ) : ?>
                <div class="ed-single-section">
                    <h2>Sobre <?php echo esc_html( $nombre ); ?></h2>
                    <?php if ( $desc_corta ) : ?>
                        <p class="ed-single-desc-short"><?php echo esc_html( $desc_corta ); ?></p>
                    <?php endif; ?>
                    <?php if ( $contenido ) : ?>
                        <div class="ed-single-desc-full"><?php echo wp_kses_post( $contenido ); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Category-Specific Details -->
                <?php echo ed_render_category_fields( $post_id, $cat_slug ); ?>

                <!-- Gallery -->
                <?php if ( $galeria && is_array( $galeria ) ) : ?>
                <div class="ed-single-section">
                    <h2>Galer&iacute;a</h2>
                    <div class="ed-single-gallery">
                        <?php foreach ( $galeria as $img ) :
                            $url = is_array( $img ) ? $img['url'] : $img;
                            $alt = is_array( $img ) ? ( $img['alt'] ?: $nombre ) : $nombre;
                        ?>
                            <div class="ed-gallery-item">
                                <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Schedule -->
                <?php if ( $horario && is_array( $horario ) ) : ?>
                <div class="ed-single-section">
                    <h2>Horario</h2>
                    <div class="ed-single-schedule">
                        <?php foreach ( $horario as $row ) : ?>
                        <div class="ed-schedule-row<?php echo ! empty( $row['cerrado'] ) ? ' ed-closed' : ''; ?>">
                            <span class="ed-schedule-day"><?php echo esc_html( $row['dia'] ); ?></span>
                            <span class="ed-schedule-hours">
                                <?php if ( ! empty( $row['cerrado'] ) ) : ?>
                                    Cerrado
                                <?php else : ?>
                                    <?php echo esc_html( $row['hora_apertura'] ); ?> – <?php echo esc_html( $row['hora_cierre'] ); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <aside class="ed-single-sidebar">

                <!-- Contact Card -->
                <div class="ed-sidebar-card ed-contact-card">
                    <?php if ( $logo ) :
                        $logo_url = is_array( $logo ) ? $logo['url'] : $logo;
                    ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $nombre ); ?>" class="ed-contact-logo">
                    <?php endif; ?>
                    <h3>Contacto</h3>

                    <?php if ( $telefono_1 ) : ?>
                    <a href="tel:<?php echo esc_attr( $telefono_1 ); ?>" class="ed-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo esc_html( $telefono_1 ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( $telefono_2 ) : ?>
                    <a href="tel:<?php echo esc_attr( $telefono_2 ); ?>" class="ed-contact-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?php echo esc_html( $telefono_2 ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( $whatsapp ) : ?>
                    <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp ) ); ?>" target="_blank" class="ed-contact-item ed-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    <?php endif; ?>

                    <?php if ( $email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="ed-contact-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?php echo esc_html( $email ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( $sitio_web ) : ?>
                    <a href="<?php echo esc_url( $sitio_web ); ?>" target="_blank" class="ed-contact-item">
                        <i class="fa-solid fa-globe"></i>
                        <span>Sitio web</span>
                    </a>
                    <?php endif; ?>

                    <?php
                    $socials = array();
                    if ( $instagram ) $socials[] = array( 'url' => $instagram, 'icon' => 'fa-brands fa-instagram', 'name' => 'Instagram' );
                    if ( $facebook )  $socials[] = array( 'url' => $facebook,  'icon' => 'fa-brands fa-facebook-f', 'name' => 'Facebook' );
                    if ( $tiktok )    $socials[] = array( 'url' => $tiktok,    'icon' => 'fa-brands fa-tiktok',     'name' => 'TikTok' );
                    if ( $youtube )   $socials[] = array( 'url' => $youtube,   'icon' => 'fa-brands fa-youtube',    'name' => 'YouTube' );
                    if ( $linkedin )  $socials[] = array( 'url' => $linkedin,  'icon' => 'fa-brands fa-linkedin-in','name' => 'LinkedIn' );
                    if ( $socials ) : ?>
                    <div class="ed-contact-social">
                        <?php foreach ( $socials as $s ) : ?>
                            <a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" aria-label="<?php echo esc_attr( $s['name'] ); ?>"><i class="<?php echo esc_attr( $s['icon'] ); ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Price Range -->
                <?php if ( $precio_min || $precio_max ) : ?>
                <div class="ed-sidebar-card">
                    <h3><i class="fa-solid fa-tag"></i> Rango de precios</h3>
                    <p class="ed-price-range">
                        <?php if ( $precio_min && $precio_max ) : ?>
                            <?php echo number_format( $precio_min, 0, ',', '.' ); ?>&euro; – <?php echo number_format( $precio_max, 0, ',', '.' ); ?>&euro;
                        <?php elseif ( $precio_min ) : ?>
                            Desde <?php echo number_format( $precio_min, 0, ',', '.' ); ?>&euro;
                        <?php else : ?>
                            Hasta <?php echo number_format( $precio_max, 0, ',', '.' ); ?>&euro;
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Location -->
                <?php if ( $direccion ) : ?>
                <div class="ed-sidebar-card">
                    <h3><i class="fa-solid fa-location-dot"></i> Ubicaci&oacute;n</h3>
                    <p class="ed-location-text">
                        <?php echo esc_html( $direccion ); ?>
                        <?php if ( $cod_postal ) : ?>
                            <br><span class="ed-postal"><?php echo esc_html( $cod_postal ); ?></span>
                        <?php endif; ?>
                        <?php if ( $zona_name ) : ?>
                            <br><span class="ed-zona-badge"><?php echo esc_html( $zona_name ); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>

            </aside>
        </div>

    </div>
    <?php
    return ob_get_clean();
}

function ed_render_category_fields( $post_id, $cat_slug ) {
    $fields_map = ed_get_category_fields_map();

    if ( ! isset( $fields_map[ $cat_slug ] ) ) {
        return '';
    }

    $config = $fields_map[ $cat_slug ];
    $has_data = false;
    $items = array();

    foreach ( $config['fields'] as $field_name => $label ) {
        $value = get_field( $field_name, $post_id );
        if ( $value === null || $value === '' || $value === false ) {
            continue;
        }
        $has_data = true;

        if ( is_array( $value ) ) {
            $items[] = array( 'label' => $label, 'value' => implode( ', ', $value ), 'type' => 'list' );
        } elseif ( $value === true || $value === 1 ) {
            $items[] = array( 'label' => $label, 'value' => true, 'type' => 'bool' );
        } else {
            $items[] = array( 'label' => $label, 'value' => $value, 'type' => 'text' );
        }
    }

    if ( ! $has_data ) {
        return '';
    }

    $html = '<div class="ed-single-section">';
    $html .= '<h2>' . esc_html( $config['title'] ) . '</h2>';
    $html .= '<div class="ed-details-grid">';

    foreach ( $items as $item ) {
        $html .= '<div class="ed-detail-item">';
        $html .= '<span class="ed-detail-label">' . esc_html( $item['label'] ) . '</span>';
        if ( $item['type'] === 'bool' ) {
            $html .= '<span class="ed-detail-value ed-detail-check"><i class="fa-solid fa-check"></i> S&iacute;</span>';
        } else {
            $html .= '<span class="ed-detail-value">' . esc_html( $item['value'] ) . '</span>';
        }
        $html .= '</div>';
    }

    $html .= '</div></div>';
    return $html;
}

function ed_get_category_fields_map() {
    return array(
        'espacios' => array(
            'title'  => 'Detalles del espacio',
            'fields' => array(
                'esp_tipo_espacio'        => 'Tipo de espacio',
                'esp_capacidad_maxima'    => 'Capacidad m&aacute;xima',
                'esp_metros_cuadrados'    => 'Metros cuadrados',
                'esp_aparcamiento'        => 'Aparcamiento',
                'esp_plazas_parking'      => 'Plazas de parking',
                'esp_accesibilidad'       => 'Accesibilidad',
                'esp_catering_propio'     => 'Catering propio',
                'esp_servicios_incluidos' => 'Servicios incluidos',
                'esp_tipo_eventos'        => 'Tipo de eventos',
            ),
        ),
        'catering' => array(
            'title'  => 'Detalles del catering',
            'fields' => array(
                'cat_tipo_cocina'         => 'Tipo de cocina',
                'cat_precio_persona'      => 'Precio por persona',
                'cat_minimo_comensales'   => 'M&iacute;nimo de comensales',
                'cat_menu_degustacion'    => 'Men&uacute; degustaci&oacute;n',
                'cat_servicio_camareros'  => 'Servicio de camareros',
                'cat_vajilla_incluida'    => 'Vajilla incluida',
                'cat_zona_desplazamiento' => 'Zona de desplazamiento',
                'cat_alergenos'           => 'Al&eacute;rgenos',
            ),
        ),
        'restaurantes' => array(
            'title'  => 'Detalles del restaurante',
            'fields' => array(
                'rest_tipo_cocina'      => 'Tipo de cocina',
                'rest_capacidad_local'  => 'Capacidad del local',
                'rest_salon_privado'    => 'Sal&oacute;n privado',
                'rest_capacidad_salon'  => 'Capacidad sal&oacute;n',
                'rest_terraza'          => 'Terraza',
                'rest_menu_grupos'      => 'Men&uacute; para grupos',
                'rest_precio_medio'     => 'Precio medio',
                'rest_carta_vinos'      => 'Carta de vinos',
                'rest_aparcamiento'     => 'Aparcamiento',
            ),
        ),
        'centros-de-belleza' => array(
            'title'  => 'Detalles del centro',
            'fields' => array(
                'bell_servicios'          => 'Servicios',
                'bell_domicilio'          => 'Servicio a domicilio',
                'bell_pack_novias'        => 'Pack novias',
                'bell_marcas'             => 'Marcas',
                'bell_num_profesionales'  => 'N&uacute;mero de profesionales',
            ),
        ),
        'animacion' => array(
            'title'  => 'Detalles de animaci&oacute;n',
            'fields' => array(
                'anim_tipo_espectaculo'   => 'Tipo de espect&aacute;culo',
                'anim_duracion'           => 'Duraci&oacute;n',
                'anim_rango_edad'         => 'Rango de edad',
                'anim_material_incluido'  => 'Material incluido',
                'anim_num_animadores'     => 'N&uacute;mero de animadores',
                'anim_desplazamiento'     => 'Desplazamiento',
            ),
        ),
        'grupos-musicales' => array(
            'title'  => 'Detalles del grupo',
            'fields' => array(
                'mus_genero'        => 'G&eacute;nero musical',
                'mus_integrantes'   => 'Integrantes',
                'mus_equipo_propio' => 'Equipo propio',
                'mus_duracion'      => 'Duraci&oacute;n',
                'mus_video_demo'    => 'V&iacute;deo demo',
            ),
        ),
        'ambientacion' => array(
            'title'  => 'Detalles de ambientaci&oacute;n',
            'fields' => array(
                'amb_tipo_decoracion'     => 'Tipo de decoraci&oacute;n',
                'amb_estilo'              => 'Estilo',
                'amb_montaje'             => 'Montaje incluido',
                'amb_alquiler_mobiliario' => 'Alquiler de mobiliario',
                'amb_personalizacion'     => 'Personalizaci&oacute;n',
                'amb_plazo_minimo'        => 'Plazo m&iacute;nimo',
            ),
        ),
        'servicios-audiovisuales' => array(
            'title'  => 'Detalles audiovisuales',
            'fields' => array(
                'aud_servicios'          => 'Servicios',
                'aud_cobertura'          => 'Cobertura',
                'aud_equipo_propio'      => 'Equipo propio',
                'aud_montaje_incluido'   => 'Montaje incluido',
                'aud_tecnico_incluido'   => 'T&eacute;cnico incluido',
                'aud_tiempo_respuesta'   => 'Tiempo de respuesta',
            ),
        ),
        'transportes' => array(
            'title'  => 'Detalles de transporte',
            'fields' => array(
                'trans_tipo_vehiculo'    => 'Tipo de veh&iacute;culo',
                'trans_capacidad'        => 'Capacidad',
                'trans_cobertura'        => 'Cobertura',
                'trans_conductor'        => 'Conductor incluido',
                'trans_decoracion'       => 'Decoraci&oacute;n',
                'trans_duracion_minima'  => 'Duraci&oacute;n m&iacute;nima',
            ),
        ),
        'fotografia-y-video' => array(
            'title'  => 'Detalles de fotograf&iacute;a',
            'fields' => array(
                'foto_servicios'           => 'Servicios',
                'foto_estilo'              => 'Estilo',
                'foto_horas_cobertura'     => 'Horas de cobertura',
                'foto_entregables'         => 'Entregables',
                'foto_plazo_entrega'       => 'Plazo de entrega',
                'foto_segundo_fotografo'   => 'Segundo fot&oacute;grafo',
                'foto_portfolio_url'       => 'Portfolio',
            ),
        ),
        'alojamiento' => array(
            'title'  => 'Detalles del alojamiento',
            'fields' => array(
                'aloj_num_habitaciones'     => 'N&uacute;mero de habitaciones',
                'aloj_capacidad_huespedes'  => 'Capacidad de hu&eacute;spedes',
                'aloj_precio_noche'         => 'Precio por noche',
                'aloj_servicios'            => 'Servicios',
                'aloj_distancia_centro'     => 'Distancia al centro',
                'aloj_checkin'              => 'Check-in',
                'aloj_checkout'             => 'Check-out',
                'aloj_accesibilidad'        => 'Accesibilidad',
            ),
        ),
    );
}
