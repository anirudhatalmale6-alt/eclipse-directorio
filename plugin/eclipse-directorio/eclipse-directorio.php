<?php
/**
 * Plugin Name: Eclipse Directorio
 * Plugin URI:  https://eclipsesevilla.com
 * Description: Directorio profesional de proveedores para la industria de eventos en Sevilla.
 * Version:     2.0.0
 * Author:      Eclipse Sevilla
 * Author URI:  https://eclipsesevilla.com
 * Text Domain: eclipse-directorio
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ED_VERSION', '2.0.0' );
define( 'ED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load plugin files.
 */
require_once ED_PLUGIN_DIR . 'includes/cpt.php';
require_once ED_PLUGIN_DIR . 'includes/taxonomies.php';
// One-time import: creates ACF field groups in the database (editable from ACF admin).
require_once ED_PLUGIN_DIR . 'includes/acf-import-once.php';
require_once ED_PLUGIN_DIR . 'includes/acf-taxonomy-fields.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-home.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-buscar.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-categorias.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-destacados.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-listado.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-archive-header.php';
require_once ED_PLUGIN_DIR . 'includes/shortcode-single-proveedor.php';
require_once ED_PLUGIN_DIR . 'includes/elementor-builder.php';
require_once ED_PLUGIN_DIR . 'includes/loop-template.php';
require_once ED_PLUGIN_DIR . 'includes/archive-template.php';
require_once ED_PLUGIN_DIR . 'includes/single-template.php';

/**
 * Enqueue plugin styles and fonts on the frontend.
 */
function ed_enqueue_assets() {
    wp_enqueue_style(
        'ed-directorio',
        ED_PLUGIN_URL . 'assets/css/directorio.css',
        array(),
        ED_VERSION
    );

    wp_enqueue_style(
        'ed-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'ed-font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}
add_action( 'wp_enqueue_scripts', 'ed_enqueue_assets' );

/**
 * Plugin activation: flush rewrite rules and create default taxonomy terms.
 */
function ed_plugin_activate() {
    // Register CPT and taxonomies so rewrite rules pick them up.
    ed_register_proveedor_cpt();
    ed_register_taxonomies();

    // Create default terms.
    ed_create_default_terms();

    // Flush rewrite rules.
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ed_plugin_activate' );

/**
 * Plugin deactivation: flush rewrite rules.
 */
function ed_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ed_plugin_deactivate' );

/**
 * Create default taxonomy terms for categoria_servicio and zona_servicio.
 */
function ed_create_default_terms() {

    // --- Categoría de Servicio ---
    $categorias = array(
        'Espacios' => array(
            'Hoteles',
            'Salones de Celebraciones',
            'Locales',
            'Haciendas y Cortijos',
        ),
        'Catering' => array(),
        'Restaurantes' => array(),
        'Centros de Belleza' => array(
            'Peluquerías',
            'Estética',
            'Spa y Masajes',
        ),
        'Animación' => array(
            'Adultos',
            'Infantiles',
        ),
        'Grupos Musicales' => array(),
        'Ambientación' => array(
            'Globos',
            'Flores',
        ),
        'Servicios Audiovisuales' => array(),
        'Transportes' => array(),
        'Fotografía y Vídeo' => array(),
        'Alojamiento' => array(
            'Hoteles',
            'Hostales',
            'Apartamentos',
            'Casas Rurales',
            'Hostels',
        ),
    );

    foreach ( $categorias as $parent_name => $children ) {
        $parent = term_exists( $parent_name, 'categoria_servicio' );
        if ( ! $parent ) {
            $parent = wp_insert_term( $parent_name, 'categoria_servicio' );
        }

        if ( ! is_wp_error( $parent ) ) {
            $parent_id = is_array( $parent ) ? $parent['term_id'] : $parent;
            foreach ( $children as $child_name ) {
                if ( ! term_exists( $child_name, 'categoria_servicio', $parent_id ) ) {
                    wp_insert_term( $child_name, 'categoria_servicio', array(
                        'parent' => (int) $parent_id,
                    ) );
                }
            }
        }
    }

    // --- Zona / Ciudad ---
    $zonas = array(
        'Sevilla Centro',
        'Triana',
        'Nervión',
        'Macarena',
        'Los Remedios',
        'Dos Hermanas',
        'Alcalá de Guadaíra',
        'Carmona',
        'Utrera',
        'Écija',
        'Provincia de Sevilla',
    );

    foreach ( $zonas as $zona ) {
        if ( ! term_exists( $zona, 'zona_servicio' ) ) {
            wp_insert_term( $zona, 'zona_servicio' );
        }
    }
}
