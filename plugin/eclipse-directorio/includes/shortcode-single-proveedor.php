<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'eclipse_proveedor_perfil', 'ed_render_proveedor_perfil' );

function ed_mask_phone( $phone ) {
    $digits = preg_replace( '/[^0-9]/', '', $phone );
    $len    = strlen( $digits );
    if ( $len < 6 ) return str_repeat( '*', $len );
    $visible_start = substr( $digits, 0, 3 );
    $visible_end   = substr( $digits, -2 );
    $masked_middle = str_repeat( '* ', max( 1, $len - 5 ) );
    return $visible_start . ' ' . trim( $masked_middle ) . ' ' . $visible_end;
}

function ed_mask_email( $email ) {
    $parts = explode( '@', $email );
    if ( count( $parts ) !== 2 ) return '****@******.es';
    $prefix = $parts[0];
    $show   = strlen( $prefix ) > 2 ? substr( $prefix, 0, 2 ) : $prefix[0];
    return $show . '***@******.es';
}

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
    $thumbnail    = get_the_post_thumbnail_url( $post_id, 'full' );

    $is_premium = (bool) $destacado;

    $cat_terms  = get_the_terms( $post_id, 'categoria_servicio' );
    $zona_terms = get_the_terms( $post_id, 'zona_servicio' );
    $cat_name   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';
    $cat_slug   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->slug : '';
    $cat_link   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? get_term_link( $cat_terms[0] ) : '';
    $zona_name  = ( $zona_terms && ! is_wp_error( $zona_terms ) ) ? $zona_terms[0]->name : '';

    $logo_url = '';
    if ( $logo ) {
        $logo_url = is_array( $logo ) ? $logo['url'] : wp_get_attachment_url( $logo );
    }

    ob_start();
    ?>
    <div class="ed-single ed-single--<?php echo $is_premium ? 'premium' : 'basico'; ?>">

        <!-- Hero Image -->
        <div class="ed-single__hero">
            <?php if ( $is_premium && $thumbnail ) : ?>
                <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $nombre ); ?>" class="ed-single__hero-img">
            <?php elseif ( ! $is_premium ) : ?>
                <div class="ed-single__hero-placeholder">
                    <i class="fa-regular fa-image"></i>
                </div>
            <?php elseif ( $thumbnail ) : ?>
                <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $nombre ); ?>" class="ed-single__hero-img">
            <?php endif; ?>
            <div class="ed-single__hero-badge">
                <?php if ( $is_premium ) : ?>
                    <span class="ed-badge ed-badge--premium">Proveedor Recomendado</span>
                <?php else : ?>
                    <span class="ed-badge ed-badge--basico">Perfil B&aacute;sico</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Header Card -->
        <div class="ed-single__header">
            <div class="ed-single__header-info">
                <?php if ( $logo_url ) : ?>
                    <div class="ed-single__logo">
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $nombre ); ?>">
                    </div>
                <?php else : ?>
                    <div class="ed-single__logo ed-single__logo--placeholder">
                        <i class="fa-solid fa-building"></i>
                    </div>
                <?php endif; ?>
                <div class="ed-single__header-text">
                    <h1 class="ed-single__name"><?php echo esc_html( $nombre ); ?></h1>
                    <?php if ( $titulo ) : ?>
                        <p class="ed-single__subtitle"><?php echo esc_html( $titulo ); ?></p>
                    <?php endif; ?>
                    <div class="ed-single__meta">
                        <?php if ( $zona_name ) : ?>
                            <span class="ed-single__location"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $zona_name ); ?><?php echo $cat_name ? ', Sevilla' : ''; ?></span>
                        <?php endif; ?>
                        <?php if ( $verificado ) : ?>
                            <span class="ed-single__verified"><i class="fa-solid fa-circle-check"></i> Verificado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="ed-single__header-actions">
                <?php if ( $is_premium ) : ?>
                    <?php if ( $whatsapp ) : ?>
                        <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp ) ); ?>" target="_blank" class="ed-btn ed-btn--whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                    <?php if ( $telefono_1 ) : ?>
                        <a href="tel:<?php echo esc_attr( $telefono_1 ); ?>" class="ed-btn ed-btn--outline">
                            <i class="fa-solid fa-phone"></i> Llamar
                        </a>
                    <?php endif; ?>
                    <a href="#solicitar-presupuesto" class="ed-btn ed-btn--gold">Solicitar presupuesto</a>
                <?php else : ?>
                    <a href="#contactar-eclipse" class="ed-btn ed-btn--gold">Contactar</a>
                <?php endif; ?>
                <button class="ed-btn ed-btn--heart" aria-label="Guardar"><i class="fa-regular fa-heart"></i> Guardar</button>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <div class="ed-single__breadcrumbs">
            <a href="<?php echo esc_url( home_url( '/directorio.html' ) ); ?>">Inicio</a>
            <span class="ed-sep">&rsaquo;</span>
            <?php if ( $cat_name && ! is_wp_error( $cat_link ) ) : ?>
                <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                <span class="ed-sep">&rsaquo;</span>
            <?php endif; ?>
            <span><?php echo esc_html( $nombre ); ?></span>
        </div>

        <!-- Feature Icons (category-specific highlights) -->
        <?php echo ed_render_feature_icons( $post_id, $cat_slug ); ?>

        <!-- Main Content Area -->
        <div class="ed-single__body">
            <div class="ed-single__main">

                <!-- About -->
                <?php if ( $desc_corta || $contenido ) : ?>
                <div class="ed-single__section">
                    <h2>Sobre <?php echo esc_html( $nombre ); ?></h2>
                    <?php if ( $desc_corta ) : ?>
                        <p class="ed-single__desc-short"><?php echo esc_html( $desc_corta ); ?></p>
                    <?php endif; ?>
                    <?php if ( $contenido ) : ?>
                        <div class="ed-single__desc-full" id="ed-desc-full">
                            <?php echo wp_kses_post( $contenido ); ?>
                        </div>
                        <button class="ed-link ed-toggle-desc" onclick="document.getElementById('ed-desc-full').classList.toggle('ed-expanded');this.textContent=this.textContent==='Ver más'?'Ver menos':'Ver más'">Ver m&aacute;s</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Services Icons -->
                <?php echo ed_render_services_icons( $post_id, $cat_slug ); ?>

                <!-- Category-Specific Details -->
                <?php if ( $is_premium ) : ?>
                    <?php echo ed_render_category_fields( $post_id, $cat_slug ); ?>
                <?php endif; ?>

                <!-- Gallery (Premium only) -->
                <?php if ( $is_premium && $galeria && is_array( $galeria ) ) : ?>
                <div class="ed-single__section">
                    <h2>Galer&iacute;a</h2>
                    <div class="ed-single__gallery">
                        <?php foreach ( $galeria as $img ) :
                            $url = is_array( $img ) ? $img['url'] : wp_get_attachment_url( $img );
                            $alt = is_array( $img ) ? ( $img['alt'] ?: $nombre ) : $nombre;
                            if ( ! $url ) continue;
                        ?>
                            <div class="ed-gallery-item">
                                <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Schedule -->
                <?php if ( $is_premium && $horario && is_array( $horario ) ) : ?>
                <div class="ed-single__section">
                    <h2>Horario</h2>
                    <div class="ed-single__schedule">
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

                <!-- Reviews placeholder -->
                <?php if ( $is_premium ) : ?>
                <div class="ed-single__section" id="opiniones">
                    <div class="ed-single__section-header">
                        <h2>Opiniones de parejas</h2>
                    </div>
                    <div class="ed-reviews-placeholder">
                        <p class="ed-text-muted">A&uacute;n no hay opiniones para este proveedor.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Contact Form -->
                <div class="ed-single__section" id="solicitar-presupuesto">
                    <h2><?php echo $is_premium ? 'Solicitar presupuesto' : 'Contactar a trav&eacute;s de Eclipse'; ?></h2>
                    <form class="ed-contact-form" method="post" action="">
                        <input type="hidden" name="ed_contact_provider" value="<?php echo esc_attr( $post_id ); ?>">
                        <input type="hidden" name="ed_contact_type" value="<?php echo $is_premium ? 'direct' : 'intermediated'; ?>">
                        <div class="ed-form-grid">
                            <div class="ed-form-field">
                                <input type="text" name="ed_nombre" placeholder="Nombre y apellidos" required>
                            </div>
                            <div class="ed-form-field">
                                <input type="email" name="ed_email" placeholder="Email" required>
                            </div>
                            <div class="ed-form-field">
                                <input type="tel" name="ed_telefono" placeholder="Tel&eacute;fono">
                            </div>
                            <div class="ed-form-field">
                                <select name="ed_tipo_evento">
                                    <option value="">Tipo de evento</option>
                                    <option value="boda">Boda</option>
                                    <option value="bautizo">Bautizo</option>
                                    <option value="comunion">Comuni&oacute;n</option>
                                    <option value="corporativo">Evento corporativo</option>
                                    <option value="fiesta">Fiesta privada</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="ed-form-field">
                                <input type="date" name="ed_fecha" placeholder="Fecha del evento">
                            </div>
                            <div class="ed-form-field ed-form-field--full">
                                <textarea name="ed_mensaje" rows="4" placeholder="&iquest;En qu&eacute; podemos ayudarte? Cu&eacute;ntanos los detalles de tu evento..."><?php echo $is_premium ? '' : 'Me interesa contactar con ' . esc_attr( $nombre ) . ' para mi evento.'; ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="ed-btn ed-btn--gold ed-btn--full">
                            <?php echo $is_premium ? 'Enviar solicitud' : 'Enviar solicitud a Eclipse'; ?>
                        </button>
                        <?php if ( ! $is_premium ) : ?>
                            <p class="ed-form-note">Tu solicitud ser&aacute; gestionada por Eclipse Sevilla, que contactar&aacute; al proveedor en tu nombre.</p>
                        <?php endif; ?>
                    </form>
                </div>

            </div>

            <!-- Sidebar -->
            <aside class="ed-single__sidebar">

                <!-- Contact Info -->
                <div class="ed-sidebar-card">
                    <h3>Informaci&oacute;n de contacto</h3>
                    <?php if ( $is_premium ) : ?>
                        <?php if ( $telefono_1 ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-solid fa-phone"></i> Tel&eacute;fono</div>
                            <a href="tel:<?php echo esc_attr( $telefono_1 ); ?>" class="ed-contact-row__value"><?php echo esc_html( $telefono_1 ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ( $telefono_2 ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-solid fa-phone"></i> Tel. 2</div>
                            <a href="tel:<?php echo esc_attr( $telefono_2 ); ?>" class="ed-contact-row__value"><?php echo esc_html( $telefono_2 ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ( $email ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-solid fa-envelope"></i> Email</div>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>" class="ed-contact-row__value"><?php echo esc_html( $email ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ( $whatsapp ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-brands fa-whatsapp"></i> WhatsApp</div>
                            <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp ) ); ?>" target="_blank" class="ed-contact-row__value">Enviar mensaje</a>
                        </div>
                        <?php endif; ?>
                        <?php if ( $sitio_web ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-solid fa-globe"></i> Web</div>
                            <a href="<?php echo esc_url( $sitio_web ); ?>" target="_blank" class="ed-contact-row__value"><?php echo esc_html( str_replace( array( 'https://', 'http://', 'www.' ), '', $sitio_web ) ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ( $instagram ) : ?>
                        <div class="ed-contact-row">
                            <div class="ed-contact-row__label"><i class="fa-brands fa-instagram"></i> Instagram</div>
                            <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" class="ed-contact-row__value">@<?php echo esc_html( basename( rtrim( $instagram, '/' ) ) ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php
                        $other_socials = array();
                        if ( $facebook )  $other_socials[] = array( 'url' => $facebook,  'icon' => 'fa-brands fa-facebook-f' );
                        if ( $tiktok )    $other_socials[] = array( 'url' => $tiktok,    'icon' => 'fa-brands fa-tiktok' );
                        if ( $youtube )   $other_socials[] = array( 'url' => $youtube,   'icon' => 'fa-brands fa-youtube' );
                        if ( $linkedin )  $other_socials[] = array( 'url' => $linkedin,  'icon' => 'fa-brands fa-linkedin-in' );
                        if ( $other_socials ) : ?>
                        <div class="ed-contact-socials">
                            <?php foreach ( $other_socials as $s ) : ?>
                                <a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank"><i class="<?php echo esc_attr( $s['icon'] ); ?>"></i></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="ed-contact-limited">
                            <?php if ( $telefono_1 ) : ?>
                            <div class="ed-contact-row">
                                <div class="ed-contact-row__label"><i class="fa-solid fa-phone"></i> Tel&eacute;fono</div>
                                <span class="ed-contact-row__value ed-masked"><?php echo esc_html( ed_mask_phone( $telefono_1 ) ); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ( $email ) : ?>
                            <div class="ed-contact-row">
                                <div class="ed-contact-row__label"><i class="fa-solid fa-envelope"></i> Email</div>
                                <span class="ed-contact-row__value ed-masked"><?php echo esc_html( ed_mask_email( $email ) ); ?></span>
                            </div>
                            <?php endif; ?>
                            <p class="ed-limited-note"><i class="fa-solid fa-lock"></i> Informaci&oacute;n limitada</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Location -->
                <?php if ( $direccion ) : ?>
                <div class="ed-sidebar-card">
                    <h3>Ubicaci&oacute;n</h3>
                    <p class="ed-location-text">
                        <?php echo esc_html( $direccion ); ?>
                        <?php if ( $cod_postal ) : ?>
                            <br><?php echo esc_html( $cod_postal ); ?>
                        <?php endif; ?>
                    </p>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode( $direccion . ' ' . $cod_postal ); ?>" target="_blank" class="ed-btn ed-btn--outline ed-btn--sm ed-btn--full">
                        <i class="fa-solid fa-map-location-dot"></i> Ver en Google Maps
                    </a>
                </div>
                <?php endif; ?>

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

                <!-- Básico CTA -->
                <?php if ( ! $is_premium ) : ?>
                <div class="ed-sidebar-card ed-sidebar-card--cta" id="contactar-eclipse">
                    <h3>&iquest;Quieres mostrar tu perfil completo?</h3>
                    <p>Hazte Premium y consigue m&aacute;s visibilidad, muestra tu galer&iacute;a, datos de contacto completos y recibe m&aacute;s solicitudes.</p>
                    <a href="<?php echo esc_url( home_url( '/directorio.html#anunciate' ) ); ?>" class="ed-btn ed-btn--gold ed-btn--full">Me hago Premium</a>
                </div>
                <?php endif; ?>

            </aside>
        </div>

    </div>
    <?php
    return ob_get_clean();
}

function ed_render_feature_icons( $post_id, $cat_slug ) {
    $icons = array();

    switch ( $cat_slug ) {
        case 'espacios':
            $cap = get_field( 'esp_capacidad_maxima', $post_id );
            if ( $cap ) $icons[] = array( 'icon' => 'fa-solid fa-users', 'text' => 'Hasta ' . $cap . ' invitados' );
            $tipo = get_field( 'esp_tipo_espacio', $post_id );
            if ( $tipo ) $icons[] = array( 'icon' => 'fa-solid fa-building-columns', 'text' => $tipo );
            if ( get_field( 'esp_aparcamiento', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-square-parking', 'text' => 'Parking' );
            if ( get_field( 'esp_accesibilidad', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-wheelchair', 'text' => 'Accesible' );
            if ( get_field( 'esp_catering_propio', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-utensils', 'text' => 'Catering propio' );
            $m2 = get_field( 'esp_metros_cuadrados', $post_id );
            if ( $m2 ) $icons[] = array( 'icon' => 'fa-solid fa-ruler-combined', 'text' => number_format( $m2, 0, ',', '.' ) . ' m²' );
            break;

        case 'catering':
            $pp = get_field( 'cat_precio_persona', $post_id );
            if ( $pp ) $icons[] = array( 'icon' => 'fa-solid fa-euro-sign', 'text' => 'Desde ' . $pp . '€/persona' );
            if ( get_field( 'cat_menu_degustacion', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-wine-glass', 'text' => 'Menú degustación' );
            if ( get_field( 'cat_servicio_camareros', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-concierge-bell', 'text' => 'Camareros' );
            if ( get_field( 'cat_vajilla_incluida', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-plate-wheat', 'text' => 'Vajilla incluida' );
            $zona = get_field( 'cat_zona_desplazamiento', $post_id );
            if ( $zona ) $icons[] = array( 'icon' => 'fa-solid fa-truck', 'text' => $zona );
            break;

        case 'restaurantes':
            $cap = get_field( 'rest_capacidad_local', $post_id );
            if ( $cap ) $icons[] = array( 'icon' => 'fa-solid fa-users', 'text' => $cap . ' comensales' );
            if ( get_field( 'rest_salon_privado', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-door-closed', 'text' => 'Salón privado' );
            if ( get_field( 'rest_terraza', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-sun', 'text' => 'Terraza' );
            if ( get_field( 'rest_carta_vinos', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-wine-bottle', 'text' => 'Carta de vinos' );
            break;

        case 'centros-de-belleza':
            if ( get_field( 'bell_pack_novias', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-gem', 'text' => 'Pack novias' );
            if ( get_field( 'bell_domicilio', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-house', 'text' => 'A domicilio' );
            $num = get_field( 'bell_num_profesionales', $post_id );
            if ( $num ) $icons[] = array( 'icon' => 'fa-solid fa-user-group', 'text' => $num . ' profesionales' );
            break;

        case 'fotografia-y-video':
            $horas = get_field( 'foto_horas_cobertura', $post_id );
            if ( $horas ) $icons[] = array( 'icon' => 'fa-solid fa-clock', 'text' => $horas . 'h cobertura' );
            if ( get_field( 'foto_segundo_fotografo', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-camera', 'text' => '2º fotógrafo' );
            $plazo = get_field( 'foto_plazo_entrega', $post_id );
            if ( $plazo ) $icons[] = array( 'icon' => 'fa-solid fa-calendar-check', 'text' => $plazo );
            break;

        case 'grupos-musicales':
            $n = get_field( 'mus_integrantes', $post_id );
            if ( $n ) $icons[] = array( 'icon' => 'fa-solid fa-user-group', 'text' => $n . ' integrantes' );
            if ( get_field( 'mus_equipo_propio', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-speaker', 'text' => 'Equipo propio' );
            $dur = get_field( 'mus_duracion', $post_id );
            if ( $dur ) $icons[] = array( 'icon' => 'fa-solid fa-clock', 'text' => $dur );
            break;

        case 'transportes':
            $cap = get_field( 'trans_capacidad', $post_id );
            if ( $cap ) $icons[] = array( 'icon' => 'fa-solid fa-users', 'text' => $cap . ' pasajeros' );
            if ( get_field( 'trans_conductor', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-id-badge', 'text' => 'Conductor incluido' );
            if ( get_field( 'trans_decoracion', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-gift', 'text' => 'Decoración incluida' );
            break;

        case 'alojamiento':
            $hab = get_field( 'aloj_num_habitaciones', $post_id );
            if ( $hab ) $icons[] = array( 'icon' => 'fa-solid fa-bed', 'text' => $hab . ' habitaciones' );
            $pn = get_field( 'aloj_precio_noche', $post_id );
            if ( $pn ) $icons[] = array( 'icon' => 'fa-solid fa-euro-sign', 'text' => 'Desde ' . $pn . '€/noche' );
            if ( get_field( 'aloj_accesibilidad', $post_id ) ) $icons[] = array( 'icon' => 'fa-solid fa-wheelchair', 'text' => 'Accesible' );
            $dist = get_field( 'aloj_distancia_centro', $post_id );
            if ( $dist ) $icons[] = array( 'icon' => 'fa-solid fa-map-pin', 'text' => $dist );
            break;

        default:
            break;
    }

    if ( empty( $icons ) ) return '';

    $html = '<div class="ed-single__features">';
    foreach ( array_slice( $icons, 0, 6 ) as $ic ) {
        $html .= '<div class="ed-feature-icon">';
        $html .= '<i class="' . esc_attr( $ic['icon'] ) . '"></i>';
        $html .= '<span>' . esc_html( $ic['text'] ) . '</span>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

function ed_render_services_icons( $post_id, $cat_slug ) {
    $services = array();
    switch ( $cat_slug ) {
        case 'espacios':
            $s = get_field( 'esp_servicios_incluidos', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'centros-de-belleza':
            $s = get_field( 'bell_servicios', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'fotografia-y-video':
            $s = get_field( 'foto_servicios', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'animacion':
            $s = get_field( 'anim_tipo_espectaculo', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'ambientacion':
            $s = get_field( 'amb_tipo_decoracion', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'servicios-audiovisuales':
            $s = get_field( 'aud_servicios', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        case 'alojamiento':
            $s = get_field( 'aloj_servicios', $post_id );
            if ( $s && is_array( $s ) ) $services = $s;
            break;
        default:
            break;
    }
    if ( empty( $services ) ) return '';

    $icon_map = array(
        'Cocina propia'      => 'fa-solid fa-kitchen-set',
        'DJ y música'        => 'fa-solid fa-music',
        'Decoración'         => 'fa-solid fa-wand-magic-sparkles',
        'Iluminación'        => 'fa-solid fa-lightbulb',
        'Coordinación'       => 'fa-solid fa-clipboard-check',
        'Barra libre'        => 'fa-solid fa-champagne-glasses',
        'Photocall'          => 'fa-solid fa-camera-retro',
        'Parking'            => 'fa-solid fa-square-parking',
        'Wi-Fi'              => 'fa-solid fa-wifi',
        'Alojamiento'        => 'fa-solid fa-bed',
        'Fotografía'         => 'fa-solid fa-camera',
        'Vídeo'              => 'fa-solid fa-video',
        'Drone'              => 'fa-solid fa-helicopter',
        'Álbum'              => 'fa-solid fa-book-open',
        'Postproducción'     => 'fa-solid fa-wand-magic-sparkles',
        'Maquillaje'         => 'fa-solid fa-paintbrush',
        'Peinado'            => 'fa-solid fa-scissors',
        'Manicura'           => 'fa-solid fa-hand-sparkles',
        'Pedicura'           => 'fa-solid fa-spa',
        'Tratamientos faciales' => 'fa-solid fa-face-smile',
        'Masajes'            => 'fa-solid fa-spa',
        'Extensiones de pestañas' => 'fa-solid fa-eye',
        'Sonido'             => 'fa-solid fa-volume-high',
        'Pantallas LED'      => 'fa-solid fa-display',
        'Streaming'          => 'fa-solid fa-tower-broadcast',
        'Microfonía'         => 'fa-solid fa-microphone',
        'Piscina'            => 'fa-solid fa-person-swimming',
        'Desayuno incluido'  => 'fa-solid fa-mug-saucer',
        'Admite mascotas'    => 'fa-solid fa-paw',
        'Aire acondicionado' => 'fa-solid fa-snowflake',
        'Cocina'             => 'fa-solid fa-kitchen-set',
        'Terraza'            => 'fa-solid fa-sun',
        'Jardín'             => 'fa-solid fa-tree',
    );

    $html = '<div class="ed-single__section"><h2>Servicios</h2><div class="ed-services-grid">';
    foreach ( $services as $srv ) {
        $icon = isset( $icon_map[ $srv ] ) ? $icon_map[ $srv ] : 'fa-solid fa-circle-check';
        $html .= '<div class="ed-service-icon">';
        $html .= '<i class="' . esc_attr( $icon ) . '"></i>';
        $html .= '<span>' . esc_html( $srv ) . '</span>';
        $html .= '</div>';
    }
    $html .= '</div></div>';
    return $html;
}

function ed_render_category_fields( $post_id, $cat_slug ) {
    $fields_map = ed_get_category_fields_map();

    if ( ! isset( $fields_map[ $cat_slug ] ) ) {
        return '';
    }

    $config   = $fields_map[ $cat_slug ];
    $has_data = false;
    $items    = array();

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

    $html = '<div class="ed-single__section">';
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
                'esp_capacidad_maxima'    => 'Capacidad máxima',
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
                'cat_minimo_comensales'   => 'Mínimo de comensales',
                'cat_menu_degustacion'    => 'Menú degustación',
                'cat_servicio_camareros'  => 'Servicio de camareros',
                'cat_vajilla_incluida'    => 'Vajilla incluida',
                'cat_zona_desplazamiento' => 'Zona de desplazamiento',
                'cat_alergenos'           => 'Alérgenos',
            ),
        ),
        'restaurantes' => array(
            'title'  => 'Detalles del restaurante',
            'fields' => array(
                'rest_tipo_cocina'      => 'Tipo de cocina',
                'rest_capacidad_local'  => 'Capacidad del local',
                'rest_salon_privado'    => 'Salón privado',
                'rest_capacidad_salon'  => 'Capacidad salón',
                'rest_terraza'          => 'Terraza',
                'rest_menu_grupos'      => 'Menú para grupos',
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
                'bell_num_profesionales'  => 'Número de profesionales',
            ),
        ),
        'animacion' => array(
            'title'  => 'Detalles de animación',
            'fields' => array(
                'anim_tipo_espectaculo'   => 'Tipo de espectáculo',
                'anim_duracion'           => 'Duración',
                'anim_rango_edad'         => 'Rango de edad',
                'anim_material_incluido'  => 'Material incluido',
                'anim_num_animadores'     => 'Número de animadores',
                'anim_desplazamiento'     => 'Desplazamiento',
            ),
        ),
        'grupos-musicales' => array(
            'title'  => 'Detalles del grupo',
            'fields' => array(
                'mus_genero'        => 'Género musical',
                'mus_integrantes'   => 'Integrantes',
                'mus_equipo_propio' => 'Equipo propio',
                'mus_duracion'      => 'Duración',
                'mus_video_demo'    => 'Vídeo demo',
            ),
        ),
        'ambientacion' => array(
            'title'  => 'Detalles de ambientación',
            'fields' => array(
                'amb_tipo_decoracion'     => 'Tipo de decoración',
                'amb_estilo'              => 'Estilo',
                'amb_montaje'             => 'Montaje incluido',
                'amb_alquiler_mobiliario' => 'Alquiler de mobiliario',
                'amb_personalizacion'     => 'Personalización',
                'amb_plazo_minimo'        => 'Plazo mínimo',
            ),
        ),
        'servicios-audiovisuales' => array(
            'title'  => 'Detalles audiovisuales',
            'fields' => array(
                'aud_servicios'          => 'Servicios',
                'aud_cobertura'          => 'Cobertura',
                'aud_equipo_propio'      => 'Equipo propio',
                'aud_montaje_incluido'   => 'Montaje incluido',
                'aud_tecnico_incluido'   => 'Técnico incluido',
                'aud_tiempo_respuesta'   => 'Tiempo de respuesta',
            ),
        ),
        'transportes' => array(
            'title'  => 'Detalles de transporte',
            'fields' => array(
                'trans_tipo_vehiculo'    => 'Tipo de vehículo',
                'trans_capacidad'        => 'Capacidad',
                'trans_cobertura'        => 'Cobertura',
                'trans_conductor'        => 'Conductor incluido',
                'trans_decoracion'       => 'Decoración',
                'trans_duracion_minima'  => 'Duración mínima',
            ),
        ),
        'fotografia-y-video' => array(
            'title'  => 'Detalles de fotografía',
            'fields' => array(
                'foto_servicios'           => 'Servicios',
                'foto_estilo'              => 'Estilo',
                'foto_horas_cobertura'     => 'Horas de cobertura',
                'foto_entregables'         => 'Entregables',
                'foto_plazo_entrega'       => 'Plazo de entrega',
                'foto_segundo_fotografo'   => 'Segundo fotógrafo',
                'foto_portfolio_url'       => 'Portfolio',
            ),
        ),
        'alojamiento' => array(
            'title'  => 'Detalles del alojamiento',
            'fields' => array(
                'aloj_num_habitaciones'     => 'Número de habitaciones',
                'aloj_capacidad_huespedes'  => 'Capacidad de huéspedes',
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
