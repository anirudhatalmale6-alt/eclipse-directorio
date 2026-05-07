<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_post_ed_load_sample_data', 'ed_load_sample_data_action' );

function ed_load_sample_data_action() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    ed_ensure_taxonomies();
    $providers = ed_get_sample_providers();
    $created   = 0;

    foreach ( $providers as $p ) {
        $existing = get_posts( array(
            'post_type'  => 'proveedor',
            'title'      => $p['title'],
            'numberposts' => 1,
            'post_status' => 'any',
        ) );
        if ( $existing ) {
            wp_delete_post( $existing[0]->ID, true );
        }

        $post_id = wp_insert_post( array(
            'post_title'   => $p['title'],
            'post_type'    => 'proveedor',
            'post_status'  => 'publish',
            'post_content' => $p['content'] ?? '',
        ) );

        if ( is_wp_error( $post_id ) ) {
            continue;
        }

        if ( ! empty( $p['thumbnail_url'] ) ) {
            $thumb_id = ed_sideload_image( $p['thumbnail_url'], $post_id, $p['title'] );
            if ( $thumb_id ) {
                set_post_thumbnail( $post_id, $thumb_id );
            }
        }

        if ( ! empty( $p['categoria'] ) ) {
            wp_set_object_terms( $post_id, $p['categoria'], 'categoria_servicio' );
        }
        if ( ! empty( $p['zona'] ) ) {
            wp_set_object_terms( $post_id, $p['zona'], 'zona_servicio' );
        }

        if ( ! empty( $p['acf'] ) ) {
            foreach ( $p['acf'] as $key => $value ) {
                if ( $key === 'galeria_fotos' && is_array( $value ) ) {
                    $gallery_ids = array();
                    foreach ( $value as $img_url ) {
                        $img_id = ed_sideload_image( $img_url, $post_id, $p['title'] . ' gallery' );
                        if ( $img_id ) {
                            $gallery_ids[] = $img_id;
                        }
                    }
                    update_field( 'galeria_fotos', $gallery_ids, $post_id );
                } elseif ( $key === 'logo' && filter_var( $value, FILTER_VALIDATE_URL ) ) {
                    $logo_id = ed_sideload_image( $value, $post_id, $p['title'] . ' logo' );
                    if ( $logo_id ) {
                        update_field( 'logo', $logo_id, $post_id );
                    }
                } elseif ( $key === 'horario' && is_array( $value ) ) {
                    update_field( 'horario', $value, $post_id );
                } else {
                    update_field( $key, $value, $post_id );
                }
            }
        }

        $created++;
    }

    echo "Sample data loaded: {$created} providers created.<br><br>";
    echo '<a href="' . admin_url( 'edit.php?post_type=proveedor' ) . '">View Providers</a> | ';
    echo '<a href="' . home_url( '/directorio.html' ) . '">View Directory</a>';
    exit;
}

function ed_sideload_image( $url, $post_id, $desc = '' ) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url( $url );
    if ( is_wp_error( $tmp ) ) {
        return 0;
    }

    $ext  = pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );
    if ( ! $ext ) {
        $ext = 'jpg';
    }
    $file = array(
        'name'     => sanitize_file_name( $desc . '.' . $ext ),
        'tmp_name' => $tmp,
    );

    $id = media_handle_sideload( $file, $post_id, $desc );
    if ( is_wp_error( $id ) ) {
        @unlink( $tmp );
        return 0;
    }

    return $id;
}

function ed_ensure_taxonomies() {
    $categories = array(
        'Espacios'               => 'espacios',
        'Catering'               => 'catering',
        'Restaurantes'           => 'restaurantes',
        'Centros de Belleza'     => 'centros-de-belleza',
        'Animación'              => 'animacion',
        'Grupos Musicales'       => 'grupos-musicales',
        'Ambientación'           => 'ambientacion',
        'Servicios Audiovisuales' => 'servicios-audiovisuales',
        'Transportes'            => 'transportes',
        'Fotografía y Vídeo'     => 'fotografia-y-video',
        'Alojamiento'            => 'alojamiento',
    );

    foreach ( $categories as $name => $slug ) {
        if ( ! term_exists( $slug, 'categoria_servicio' ) ) {
            wp_insert_term( $name, 'categoria_servicio', array( 'slug' => $slug ) );
        }
    }

    $subcategories = array(
        'espacios' => array(
            'Hoteles'              => 'hoteles',
            'Salones'              => 'salones',
            'Locales'              => 'locales',
            'Haciendas y Cortijos' => 'haciendas-y-cortijos',
        ),
        'centros-de-belleza' => array(
            'Peluquerías'    => 'peluquerias',
            'Estética'       => 'estetica',
            'Spa y Masajes'  => 'spa-y-masajes',
        ),
        'animacion' => array(
            'Adultos'    => 'adultos',
            'Infantiles' => 'infantiles',
        ),
        'ambientacion' => array(
            'Globos' => 'globos',
            'Flores' => 'flores',
        ),
        'alojamiento' => array(
            'Hoteles'       => 'hoteles-aloj',
            'Hostales'      => 'hostales',
            'Apartamentos'  => 'apartamentos',
            'Casas Rurales' => 'casas-rurales',
            'Hostels'       => 'hostels',
        ),
    );

    foreach ( $subcategories as $parent_slug => $children ) {
        $parent = get_term_by( 'slug', $parent_slug, 'categoria_servicio' );
        if ( ! $parent ) continue;
        foreach ( $children as $name => $slug ) {
            if ( ! term_exists( $slug, 'categoria_servicio' ) ) {
                wp_insert_term( $name, 'categoria_servicio', array(
                    'slug'   => $slug,
                    'parent' => $parent->term_id,
                ) );
            }
        }
    }

    $zonas = array(
        'Sevilla Centro'       => 'sevilla-centro',
        'Triana'               => 'triana',
        'Nervión'              => 'nervion',
        'Los Remedios'         => 'los-remedios',
        'Macarena'             => 'macarena',
        'Santa Cruz'           => 'santa-cruz',
        'Dos Hermanas'         => 'dos-hermanas',
        'Alcalá de Guadaíra'   => 'alcala-de-guadaira',
        'Carmona'              => 'carmona',
        'Utrera'               => 'utrera',
        'Camas'                => 'camas',
        'Bollullos de la Mitación' => 'bollullos',
    );

    foreach ( $zonas as $name => $slug ) {
        if ( ! term_exists( $slug, 'zona_servicio' ) ) {
            wp_insert_term( $name, 'zona_servicio', array( 'slug' => $slug ) );
        }
    }
}

function ed_standard_horario() {
    return array(
        array( 'dia' => 'Lunes',     'hora_apertura' => '10:00', 'hora_cierre' => '20:00', 'cerrado' => false ),
        array( 'dia' => 'Martes',    'hora_apertura' => '10:00', 'hora_cierre' => '20:00', 'cerrado' => false ),
        array( 'dia' => 'Miércoles', 'hora_apertura' => '10:00', 'hora_cierre' => '20:00', 'cerrado' => false ),
        array( 'dia' => 'Jueves',    'hora_apertura' => '10:00', 'hora_cierre' => '20:00', 'cerrado' => false ),
        array( 'dia' => 'Viernes',   'hora_apertura' => '10:00', 'hora_cierre' => '21:00', 'cerrado' => false ),
        array( 'dia' => 'Sábado',    'hora_apertura' => '11:00', 'hora_cierre' => '14:00', 'cerrado' => false ),
        array( 'dia' => 'Domingo',   'hora_apertura' => '',      'hora_cierre' => '',      'cerrado' => true ),
    );
}

function ed_restaurant_horario() {
    return array(
        array( 'dia' => 'Lunes',     'hora_apertura' => '',      'hora_cierre' => '',      'cerrado' => true ),
        array( 'dia' => 'Martes',    'hora_apertura' => '13:00', 'hora_cierre' => '16:00', 'cerrado' => false ),
        array( 'dia' => 'Miércoles', 'hora_apertura' => '13:00', 'hora_cierre' => '16:00', 'cerrado' => false ),
        array( 'dia' => 'Jueves',    'hora_apertura' => '13:00', 'hora_cierre' => '23:30', 'cerrado' => false ),
        array( 'dia' => 'Viernes',   'hora_apertura' => '13:00', 'hora_cierre' => '00:00', 'cerrado' => false ),
        array( 'dia' => 'Sábado',    'hora_apertura' => '13:00', 'hora_cierre' => '00:00', 'cerrado' => false ),
        array( 'dia' => 'Domingo',   'hora_apertura' => '13:00', 'hora_cierre' => '17:00', 'cerrado' => false ),
    );
}

function ed_get_sample_providers() {
    return array(

        // ===== 1. ESPACIOS =====
        array(
            'title'     => 'Hacienda Los Olivares',
            'content'   => 'Hacienda del siglo XVIII rodeada de olivos centenarios en la campiña sevillana. Un espacio único donde la historia y la naturaleza se fusionan para crear el escenario perfecto de tu evento. Con más de 3.000 m² de jardines, patios empedrados y salones con techos abovedados, ofrecemos una experiencia inolvidable en el corazón de Andalucía.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=800&h=600&fit=crop',
            'categoria' => array( 'espacios', 'haciendas-y-cortijos' ),
            'zona'      => 'carmona',
            'acf'       => array(
                'nombre_negocio'     => 'Hacienda Los Olivares',
                'titulo_profesional' => 'Espacio para Eventos y Celebraciones',
                'telefono_1'         => '+34 954 123 456',
                'telefono_2'         => '+34 600 111 222',
                'email'              => 'info@haciendalosolivares.es',
                'whatsapp'           => '+34600111222',
                'sitio_web'          => 'https://www.haciendalosolivares.es',
                'instagram'          => 'https://www.instagram.com/haciendalosolivares',
                'facebook'           => 'https://www.facebook.com/haciendalosolivares',
                'direccion'          => 'Carretera Carmona-Lora, km 5, 41410 Carmona, Sevilla',
                'codigo_postal'      => '41410',
                'descripcion_corta'  => 'Hacienda histórica del siglo XVIII con jardines de olivos, patios andaluces y salones señoriales. Bodas, eventos corporativos y celebraciones exclusivas hasta 350 invitados.',
                'rango_precio_min'   => 3500,
                'rango_precio_max'   => 12000,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'esp_capacidad_maxima'  => 350,
                'esp_metros_cuadrados'  => 3200,
                'esp_tipo_espacio'      => 'Mixto',
                'esp_aparcamiento'      => true,
                'esp_plazas_parking'    => 120,
                'esp_accesibilidad'     => true,
                'esp_catering_propio'   => false,
                'esp_servicios_incluidos' => array( 'Cocina propia', 'Iluminación', 'Coordinación', 'Parking', 'Wi-Fi' ),
                'esp_tipo_eventos'      => array( 'Bodas', 'Bautizos', 'Comuniones', 'Eventos corporativos', 'Fiestas privadas' ),
                'galeria_fotos' => array(
                    'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1505236858219-8359eb29e329?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=600&fit=crop',
                ),
            ),
        ),

        array(
            'title'     => 'Salón Real Alcázar',
            'content'   => 'Salón de eventos en pleno centro de Sevilla, con una decoración elegante inspirada en la arquitectura mudéjar. Ideal para bodas íntimas, cenas de gala y presentaciones corporativas. Capacidad hasta 180 personas con servicio integral.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=800&h=533&fit=crop',
            'categoria' => array( 'espacios', 'salones' ),
            'zona'      => 'santa-cruz',
            'acf'       => array(
                'nombre_negocio'     => 'Salón Real Alcázar',
                'titulo_profesional' => 'Salón de Eventos Premium',
                'telefono_1'         => '+34 954 234 567',
                'email'              => 'reservas@salonrealalcazar.es',
                'whatsapp'           => '+34611222333',
                'sitio_web'          => 'https://www.salonrealalcazar.es',
                'instagram'          => 'https://www.instagram.com/salonrealalcazar',
                'direccion'          => 'Calle Ximénez de Enciso 15, 41004 Sevilla',
                'codigo_postal'      => '41004',
                'descripcion_corta'  => 'Salón con encanto mudéjar en el barrio de Santa Cruz. Bodas íntimas, cenas de gala y eventos exclusivos en el corazón histórico de Sevilla.',
                'rango_precio_min'   => 2000,
                'rango_precio_max'   => 8000,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'esp_capacidad_maxima'  => 180,
                'esp_metros_cuadrados'  => 450,
                'esp_tipo_espacio'      => 'Interior',
                'esp_aparcamiento'      => false,
                'esp_accesibilidad'     => true,
                'esp_catering_propio'   => true,
                'esp_servicios_incluidos' => array( 'Cocina propia', 'DJ y música', 'Decoración', 'Iluminación', 'Coordinación', 'Barra libre', 'Wi-Fi' ),
                'esp_tipo_eventos'      => array( 'Bodas', 'Eventos corporativos', 'Fiestas privadas', 'Conferencias' ),
            ),
        ),

        // ===== 2. CATERING =====
        array(
            'title'     => 'Delicias del Sur Catering',
            'content'   => 'Servicio de catering artesanal especializado en cocina andaluza contemporánea. Utilizamos productos de proximidad y de temporada para crear menús personalizados que sorprenden en cada bocado. Desde tapas creativas hasta menús degustación completos para eventos de cualquier envergadura.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1555244162-803834f70033?w=800&h=600&fit=crop',
            'categoria' => 'catering',
            'zona'      => 'triana',
            'acf'       => array(
                'nombre_negocio'     => 'Delicias del Sur Catering',
                'titulo_profesional' => 'Catering Gourmet para Eventos',
                'telefono_1'         => '+34 954 345 678',
                'telefono_2'         => '+34 622 333 444',
                'email'              => 'info@deliciasdelsur.es',
                'whatsapp'           => '+34622333444',
                'sitio_web'          => 'https://www.deliciasdelsur.es',
                'instagram'          => 'https://www.instagram.com/deliciasdelsur',
                'facebook'           => 'https://www.facebook.com/deliciasdelsur',
                'tiktok'             => 'https://www.tiktok.com/@deliciasdelsur',
                'direccion'          => 'Calle Betis 42, 41010 Sevilla',
                'codigo_postal'      => '41010',
                'descripcion_corta'  => 'Catering gourmet de cocina andaluza contemporánea. Menús personalizados con productos de temporada y kilómetro cero. Bodas, corporativos y celebraciones desde 30 hasta 500 comensales.',
                'rango_precio_min'   => 45,
                'rango_precio_max'   => 120,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'cat_tipo_cocina'        => array( 'Mediterránea', 'Andaluza' ),
                'cat_precio_persona'     => 55,
                'cat_minimo_comensales'  => 30,
                'cat_menu_degustacion'   => true,
                'cat_servicio_camareros' => true,
                'cat_vajilla_incluida'   => true,
                'cat_zona_desplazamiento' => 'Sevilla y provincia (hasta 80 km)',
                'cat_alergenos'          => array( 'Gluten', 'Lactosa', 'Frutos secos', 'Mariscos', 'Celíaco' ),
                'galeria_fotos' => array(
                    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
                ),
            ),
        ),

        // ===== 3. RESTAURANTES =====
        array(
            'title'     => 'La Terraza de Triana',
            'content'   => 'Restaurante con terraza panorámica sobre el Guadalquivir, especializado en cocina mediterránea con toques andaluces. Nuestro chef combina tradición e innovación para ofrecer una experiencia gastronómica única con las mejores vistas de Sevilla. Salón privado disponible para eventos y celebraciones.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&h=600&fit=crop',
            'categoria' => 'restaurantes',
            'zona'      => 'triana',
            'acf'       => array(
                'nombre_negocio'     => 'La Terraza de Triana',
                'titulo_profesional' => 'Restaurante Mediterráneo con Vistas',
                'telefono_1'         => '+34 954 456 789',
                'email'              => 'reservas@terrazadetriana.es',
                'whatsapp'           => '+34633444555',
                'sitio_web'          => 'https://www.terrazadetriana.es',
                'instagram'          => 'https://www.instagram.com/terrazadetriana',
                'facebook'           => 'https://www.facebook.com/terrazadetriana',
                'direccion'          => 'Calle Betis 68, 41010 Sevilla',
                'codigo_postal'      => '41010',
                'descripcion_corta'  => 'Cocina mediterránea de autor con terraza sobre el Guadalquivir. Salón privado para eventos hasta 60 personas. La mejor experiencia gastronómica del barrio de Triana.',
                'rango_precio_min'   => 35,
                'rango_precio_max'   => 65,
                'horario'            => ed_restaurant_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'rest_tipo_cocina'      => array( 'Mediterránea', 'Andaluza', 'Tapas' ),
                'rest_capacidad_local'  => 120,
                'rest_salon_privado'    => true,
                'rest_capacidad_salon'  => 60,
                'rest_terraza'          => true,
                'rest_menu_grupos'      => true,
                'rest_precio_medio'     => 42,
                'rest_carta_vinos'      => true,
                'rest_aparcamiento'     => false,
            ),
        ),

        // ===== 4. CENTROS DE BELLEZA =====
        array(
            'title'     => 'Atelier Beauty Studio',
            'content'   => 'Centro de belleza integral en el corazón de Sevilla, especializado en novias y eventos especiales. Nuestro equipo de profesionales ofrece servicios de maquillaje, peluquería, tratamientos faciales y corporales con las mejores marcas del mercado. Pack novias con prueba incluida.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&h=600&fit=crop',
            'categoria' => array( 'centros-de-belleza', 'peluquerias', 'estetica' ),
            'zona'      => 'nervion',
            'acf'       => array(
                'nombre_negocio'     => 'Atelier Beauty Studio',
                'titulo_profesional' => 'Centro de Belleza Integral',
                'telefono_1'         => '+34 954 567 890',
                'email'              => 'citas@atelierbeauty.es',
                'whatsapp'           => '+34644555666',
                'sitio_web'          => 'https://www.atelierbeauty.es',
                'instagram'          => 'https://www.instagram.com/atelierbeautysevilla',
                'facebook'           => 'https://www.facebook.com/atelierbeautysevilla',
                'direccion'          => 'Avenida Luis de Morales 32, 41018 Sevilla',
                'codigo_postal'      => '41018',
                'descripcion_corta'  => 'Centro de belleza premium especializado en novias y eventos. Maquillaje profesional, peluquería, tratamientos faciales y pack novias con prueba incluida.',
                'rango_precio_min'   => 40,
                'rango_precio_max'   => 350,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'bell_servicios'         => array( 'Maquillaje', 'Peinado', 'Manicura', 'Pedicura', 'Tratamientos faciales', 'Extensiones de pestañas' ),
                'bell_domicilio'         => true,
                'bell_pack_novias'       => true,
                'bell_marcas'            => 'MAC Cosmetics, Charlotte Tilbury, GHD, Kérastase, Moroccanoil',
                'bell_num_profesionales' => 6,
                'galeria_fotos' => array(
                    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=800&h=600&fit=crop',
                ),
            ),
        ),

        // ===== 5. ANIMACIÓN =====
        array(
            'title'     => 'FiestaViva Animaciones',
            'content'   => 'Empresa de animación y entretenimiento para todo tipo de eventos en Sevilla. Desde espectáculos infantiles con magia y pintacaras hasta shows para adultos con DJ, karaoke y photocall. Más de 10 años de experiencia haciendo que cada celebración sea inolvidable.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop',
            'categoria' => array( 'animacion', 'infantiles' ),
            'zona'      => 'los-remedios',
            'acf'       => array(
                'nombre_negocio'     => 'FiestaViva Animaciones',
                'titulo_profesional' => 'Animación y Entretenimiento para Eventos',
                'telefono_1'         => '+34 955 678 901',
                'email'              => 'hola@fiestaviva.es',
                'whatsapp'           => '+34655666777',
                'sitio_web'          => 'https://www.fiestaviva.es',
                'instagram'          => 'https://www.instagram.com/fiestaviva',
                'youtube'            => 'https://www.youtube.com/@fiestaviva',
                'direccion'          => 'Calle Virgen de Luján 18, 41011 Sevilla',
                'codigo_postal'      => '41011',
                'descripcion_corta'  => 'Animación infantil y adultos para todo tipo de eventos. Magia, payasos, DJ, photocall, hinchables y shows temáticos. Más de 10 años de experiencia en Sevilla.',
                'rango_precio_min'   => 150,
                'rango_precio_max'   => 800,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'anim_tipo_espectaculo' => array( 'Magia', 'Payasos', 'DJ', 'Photocall', 'Hinchables', 'Pintacaras', 'Globoflexia' ),
                'anim_duracion'         => '2-4 horas',
                'anim_rango_edad'       => 'Todas las edades',
                'anim_material_incluido' => true,
                'anim_num_animadores'   => 8,
                'anim_desplazamiento'   => 'Sevilla capital y provincia',
            ),
        ),

        // ===== 6. GRUPOS MUSICALES =====
        array(
            'title'     => 'Alma Flamenca Band',
            'content'   => 'Grupo musical versátil que fusiona flamenco, pop y sevillanas con un toque contemporáneo. Perfectos para bodas, fiestas y eventos corporativos. Desde un dúo íntimo hasta una banda completa de 7 músicos con bailaora. Creamos la banda sonora perfecta para tu evento.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=600&fit=crop',
            'categoria' => 'grupos-musicales',
            'zona'      => 'sevilla-centro',
            'acf'       => array(
                'nombre_negocio'     => 'Alma Flamenca Band',
                'titulo_profesional' => 'Grupo Musical para Eventos',
                'telefono_1'         => '+34 654 789 012',
                'email'              => 'booking@almaflamenca.es',
                'whatsapp'           => '+34654789012',
                'sitio_web'          => 'https://www.almaflamenca.es',
                'instagram'          => 'https://www.instagram.com/almaflamencaband',
                'youtube'            => 'https://www.youtube.com/@almaflamenca',
                'facebook'           => 'https://www.facebook.com/almaflamenca',
                'direccion'          => 'Calle Feria 92, 41003 Sevilla',
                'codigo_postal'      => '41003',
                'descripcion_corta'  => 'Grupo musical versátil: flamenco, pop, sevillanas y covers. Desde dúo íntimo hasta banda completa de 7 músicos con bailaora. Bodas, eventos y fiestas en Sevilla.',
                'rango_precio_min'   => 600,
                'rango_precio_max'   => 3000,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'mus_genero'         => array( 'Pop/Rock', 'Flamenco', 'Covers', 'Sevillanas' ),
                'mus_integrantes'    => 7,
                'mus_equipo_propio'  => true,
                'mus_duracion'       => '3-5 horas',
                'mus_video_demo'     => 'https://www.youtube.com/watch?v=demo',
            ),
        ),

        // ===== 7. AMBIENTACIÓN =====
        array(
            'title'     => 'Flora & Evento',
            'content'   => 'Estudio de diseño floral y ambientación para eventos en Sevilla. Creamos atmósferas únicas combinando flores frescas, iluminación y elementos decorativos personalizados. Desde centros de mesa elegantes hasta arcos florales monumentales, transformamos cualquier espacio en un escenario de ensueño.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?w=800&h=600&fit=crop',
            'categoria' => array( 'ambientacion', 'flores' ),
            'zona'      => 'sevilla-centro',
            'acf'       => array(
                'nombre_negocio'     => 'Flora & Evento',
                'titulo_profesional' => 'Diseño Floral y Ambientación',
                'telefono_1'         => '+34 954 890 123',
                'email'              => 'hola@floraevento.es',
                'whatsapp'           => '+34666777888',
                'sitio_web'          => 'https://www.floraevento.es',
                'instagram'          => 'https://www.instagram.com/floraevento',
                'tiktok'             => 'https://www.tiktok.com/@floraevento',
                'direccion'          => 'Calle Sierpes 74, 41004 Sevilla',
                'codigo_postal'      => '41004',
                'descripcion_corta'  => 'Diseño floral y ambientación artística para bodas y eventos. Arcos florales, centros de mesa, ramos de novia e instalaciones personalizadas. Estilo romántico, bohemio y clásico.',
                'rango_precio_min'   => 500,
                'rango_precio_max'   => 5000,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'amb_tipo_decoracion'   => array( 'Flores', 'Iluminación', 'Centros de mesa', 'Arcos' ),
                'amb_estilo'            => array( 'Romántico', 'Bohemio', 'Clásico', 'Rústico' ),
                'amb_montaje'           => true,
                'amb_alquiler_mobiliario' => true,
                'amb_personalizacion'   => true,
                'amb_plazo_minimo'      => '2 semanas',
            ),
        ),

        // ===== 8. SERVICIOS AUDIOVISUALES =====
        array(
            'title'     => 'SoundLight Producciones',
            'content'   => 'Empresa de producción audiovisual especializada en sonido, iluminación y efectos especiales para eventos. Disponemos de equipo profesional propio y técnicos cualificados para garantizar la excelencia técnica en bodas, conciertos, conferencias y eventos corporativos en toda Andalucía.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=600&fit=crop',
            'categoria' => 'servicios-audiovisuales',
            'zona'      => 'dos-hermanas',
            'acf'       => array(
                'nombre_negocio'     => 'SoundLight Producciones',
                'titulo_profesional' => 'Producción Audiovisual para Eventos',
                'telefono_1'         => '+34 955 012 345',
                'email'              => 'info@soundlight.es',
                'whatsapp'           => '+34677888999',
                'sitio_web'          => 'https://www.soundlight.es',
                'instagram'          => 'https://www.instagram.com/soundlightprod',
                'linkedin'           => 'https://www.linkedin.com/company/soundlight',
                'direccion'          => 'Polígono Industrial Carretera de la Isla, Nave 14, 41700 Dos Hermanas',
                'codigo_postal'      => '41700',
                'descripcion_corta'  => 'Producción audiovisual completa: sonido profesional, iluminación escénica, pantallas LED y streaming. Equipo propio y técnicos en plantilla. Cobertura en toda Andalucía.',
                'rango_precio_min'   => 400,
                'rango_precio_max'   => 6000,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'aud_servicios'          => array( 'Sonido', 'Iluminación', 'Pantallas LED', 'Streaming', 'Microfonía' ),
                'aud_cobertura'          => 'Andalucía completa',
                'aud_equipo_propio'      => true,
                'aud_montaje_incluido'   => true,
                'aud_tecnico_incluido'   => true,
                'aud_tiempo_respuesta'   => '48 horas',
            ),
        ),

        // ===== 9. TRANSPORTES =====
        array(
            'title'     => 'Coches de Época Sevilla',
            'content'   => 'Servicio de coches clásicos y de época para bodas y eventos especiales en Sevilla. Nuestra flota incluye Rolls Royce Silver Cloud, Jaguar E-Type, Mercedes 280SE y coches de caballo tradicionales sevillanos. Conductores uniformados y vehículos impecablemente mantenidos.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&h=600&fit=crop',
            'categoria' => 'transportes',
            'zona'      => 'sevilla-centro',
            'acf'       => array(
                'nombre_negocio'     => 'Coches de Época Sevilla',
                'titulo_profesional' => 'Coches Clásicos para Bodas y Eventos',
                'telefono_1'         => '+34 954 123 789',
                'email'              => 'reservas@cochesdeepoca.es',
                'whatsapp'           => '+34688999000',
                'sitio_web'          => 'https://www.cochesdeepocasevilla.es',
                'instagram'          => 'https://www.instagram.com/cochesdeepocasevilla',
                'direccion'          => 'Calle Resolana 28, 41009 Sevilla',
                'codigo_postal'      => '41009',
                'descripcion_corta'  => 'Flota de coches clásicos para bodas: Rolls Royce, Jaguar, Mercedes y coches de caballo. Conductores uniformados, decoración floral incluida y servicio premium en Sevilla.',
                'rango_precio_min'   => 300,
                'rango_precio_max'   => 1200,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'trans_tipo_vehiculo'    => array( 'Coche clásico', 'Caballo y carruaje', 'Descapotable', 'Limusina' ),
                'trans_capacidad'        => 4,
                'trans_cobertura'        => 'Sevilla capital y provincia',
                'trans_conductor'        => true,
                'trans_decoracion'       => true,
                'trans_duracion_minima'  => '2 horas',
            ),
        ),

        // ===== 10. FOTOGRAFÍA Y VÍDEO =====
        array(
            'title'     => 'Luz de Sevilla Fotografía',
            'content'   => 'Estudio de fotografía y vídeo especializado en bodas, bautizos y eventos sociales. Nuestro estilo combina el reportaje documental con retratos artísticos, capturando momentos auténticos con la luz única de Sevilla. Incluimos edición profesional, galería online y álbum de diseño.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1554048612-b6a482bc67e5?w=800&h=600&fit=crop',
            'categoria' => 'fotografia-y-video',
            'zona'      => 'macarena',
            'acf'       => array(
                'nombre_negocio'     => 'Luz de Sevilla Fotografía',
                'titulo_profesional' => 'Fotógrafo de Bodas y Eventos',
                'telefono_1'         => '+34 654 321 098',
                'email'              => 'hola@luzdesevilla.es',
                'whatsapp'           => '+34654321098',
                'sitio_web'          => 'https://www.luzdesevilla.es',
                'instagram'          => 'https://www.instagram.com/luzdesevilla',
                'facebook'           => 'https://www.facebook.com/luzdesevilla',
                'youtube'            => 'https://www.youtube.com/@luzdesevilla',
                'direccion'          => 'Calle Relator 46, 41002 Sevilla',
                'codigo_postal'      => '41002',
                'descripcion_corta'  => 'Fotografía y vídeo de bodas con estilo documental y artístico. Segundo fotógrafo, drone, álbum de diseño y galería online. Capturamos la luz única de Sevilla en cada momento.',
                'rango_precio_min'   => 900,
                'rango_precio_max'   => 3500,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => true,
                'foto_servicios'         => array( 'Fotografía', 'Vídeo', 'Drone', 'Álbum', 'Postproducción' ),
                'foto_estilo'            => array( 'Reportaje', 'Artístico', 'Documental', 'Natural' ),
                'foto_horas_cobertura'   => 10,
                'foto_entregables'       => array( 'Fotos editadas', 'Vídeo corto (highlights)', 'Álbum impreso', 'Galería online', 'USB' ),
                'foto_plazo_entrega'     => '6-8 semanas',
                'foto_segundo_fotografo' => true,
                'foto_portfolio_url'     => 'https://www.luzdesevilla.es/portfolio',
                'galeria_fotos' => array(
                    'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=800&h=600&fit=crop',
                ),
            ),
        ),

        // ===== 11. ALOJAMIENTO =====
        array(
            'title'     => 'Hotel Palacio de la Condesa',
            'content'   => 'Hotel boutique de 4 estrellas ubicado en un palacio restaurado del siglo XVII en el centro de Sevilla. 28 habitaciones con decoración única, patio interior con fuente, terraza con vistas a la Giralda y desayuno gourmet andaluz incluido. El alojamiento perfecto para invitados de eventos y bodas.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
            'categoria' => array( 'alojamiento', 'hoteles-aloj' ),
            'zona'      => 'santa-cruz',
            'acf'       => array(
                'nombre_negocio'     => 'Hotel Palacio de la Condesa',
                'titulo_profesional' => 'Hotel Boutique 4 Estrellas',
                'telefono_1'         => '+34 954 987 654',
                'email'              => 'reservas@palaciocondesa.es',
                'whatsapp'           => '+34699000111',
                'sitio_web'          => 'https://www.palaciocondesa.es',
                'instagram'          => 'https://www.instagram.com/palaciocondesa',
                'facebook'           => 'https://www.facebook.com/palaciocondesa',
                'linkedin'           => 'https://www.linkedin.com/company/palaciocondesa',
                'direccion'          => 'Calle Lope de Rueda 7, 41004 Sevilla',
                'codigo_postal'      => '41004',
                'descripcion_corta'  => 'Hotel boutique en palacio del siglo XVII. 28 habitaciones únicas, patio andaluz, terraza con vistas a la Giralda. Ideal para invitados de bodas y eventos en el centro de Sevilla.',
                'rango_precio_min'   => 120,
                'rango_precio_max'   => 350,
                'horario'            => array(
                    array( 'dia' => 'Lunes',     'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Martes',    'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Miércoles', 'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Jueves',    'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Viernes',   'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Sábado',    'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                    array( 'dia' => 'Domingo',   'hora_apertura' => '00:00', 'hora_cierre' => '23:59', 'cerrado' => false ),
                ),
                'verificado'         => true,
                'destacado'          => false,
                'aloj_num_habitaciones'    => 28,
                'aloj_capacidad_huespedes' => 56,
                'aloj_precio_noche'        => 120,
                'aloj_servicios'           => array( 'Wi-Fi', 'Desayuno incluido', 'Aire acondicionado', 'Terraza' ),
                'aloj_distancia_centro'    => 'En pleno centro histórico, a 200m de la Catedral',
                'aloj_checkin'             => '14:00',
                'aloj_checkout'            => '12:00',
                'aloj_accesibilidad'       => true,
            ),
        ),

        // ===== EXTRA: Second catering for variety =====
        array(
            'title'     => 'Sabores de Andalucía',
            'content'   => 'Servicio de catering vegano y vegetariano para eventos conscientes. Apostamos por ingredientes ecológicos, de temporada y de proximidad. Menús creativos que demuestran que la cocina plant-based puede ser sofisticada, deliciosa y perfecta para cualquier celebración.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1543362906-acfc16c67564?w=800&h=600&fit=crop',
            'categoria' => 'catering',
            'zona'      => 'nervion',
            'acf'       => array(
                'nombre_negocio'     => 'Sabores de Andalucía',
                'titulo_profesional' => 'Catering Vegano y Ecológico',
                'telefono_1'         => '+34 955 111 222',
                'email'              => 'hola@saboresandalucia.es',
                'whatsapp'           => '+34611223344',
                'sitio_web'          => 'https://www.saboresandalucia.es',
                'instagram'          => 'https://www.instagram.com/saboresandalucia',
                'direccion'          => 'Calle José Laguillo 8, 41003 Sevilla',
                'codigo_postal'      => '41003',
                'descripcion_corta'  => 'Catering 100% vegano y ecológico. Menús creativos plant-based para bodas, eventos corporativos y celebraciones. Productos de proximidad y de temporada.',
                'rango_precio_min'   => 35,
                'rango_precio_max'   => 85,
                'horario'            => ed_standard_horario(),
                'verificado'         => false,
                'destacado'          => false,
                'cat_tipo_cocina'        => array( 'Vegana/Vegetariana', 'Mediterránea', 'Fusión' ),
                'cat_precio_persona'     => 40,
                'cat_minimo_comensales'  => 20,
                'cat_menu_degustacion'   => true,
                'cat_servicio_camareros' => true,
                'cat_vajilla_incluida'   => false,
                'cat_zona_desplazamiento' => 'Sevilla capital',
                'cat_alergenos'          => array( 'Gluten', 'Frutos secos', 'Soja' ),
            ),
        ),

        // ===== EXTRA: Second photographer for variety =====
        array(
            'title'     => 'Momentos Films',
            'content'   => 'Productora audiovisual especializada en vídeos cinematográficos para bodas. Nuestro equipo utiliza cámaras de cine, drones y estabilizadores para crear cortometrajes que cuentan tu historia de amor. Desde el teaser de 60 segundos hasta el largometraje completo de tu día.',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800&h=600&fit=crop',
            'categoria' => 'fotografia-y-video',
            'zona'      => 'los-remedios',
            'acf'       => array(
                'nombre_negocio'     => 'Momentos Films',
                'titulo_profesional' => 'Videógrafo Cinematográfico de Bodas',
                'telefono_1'         => '+34 666 543 210',
                'email'              => 'info@momentosfilms.es',
                'whatsapp'           => '+34666543210',
                'sitio_web'          => 'https://www.momentosfilms.es',
                'instagram'          => 'https://www.instagram.com/momentosfilms',
                'youtube'            => 'https://www.youtube.com/@momentosfilms',
                'direccion'          => 'Avenida República Argentina 25, 41011 Sevilla',
                'codigo_postal'      => '41011',
                'descripcion_corta'  => 'Vídeo cinematográfico de bodas. Cámaras de cine, drone y estabilizadores. Teaser, highlights y largometraje completo. Edición profesional con banda sonora personalizada.',
                'rango_precio_min'   => 1200,
                'rango_precio_max'   => 4500,
                'horario'            => ed_standard_horario(),
                'verificado'         => true,
                'destacado'          => false,
                'foto_servicios'         => array( 'Vídeo', 'Drone', 'Postproducción' ),
                'foto_estilo'            => array( 'Cinematográfico', 'Documental', 'Natural' ),
                'foto_horas_cobertura'   => 12,
                'foto_entregables'       => array( 'Vídeo corto (highlights)', 'Vídeo largo', 'Galería online' ),
                'foto_plazo_entrega'     => '8-10 semanas',
                'foto_segundo_fotografo' => false,
                'foto_portfolio_url'     => 'https://www.momentosfilms.es/showreel',
            ),
        ),

    );
}
