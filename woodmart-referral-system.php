<?php
/**
 * Plugin Name:        Referral System Elextor
 * Description:        Sistema de referidos con árbol genealógico para WordPress y WooCommerce.
 * Version:            1.1.0
 * Author:             Juan Esteban Lugo Rodriguez
 * License:            GPL v2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:        woodmart-referral-system
 * Domain Path:        /languages
 * WC requires at least: 6.0
 * WC tested up to: 8.7 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes del plugin
if ( ! defined( 'WRS_PLUGIN_VERSION' ) ) {
    define( 'WRS_PLUGIN_VERSION', '1.1.0' );
}
if ( ! defined( 'WRS_PLUGIN_DIR_PATH' ) ) {
    define( 'WRS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WRS_PLUGIN_DIR_URL' ) ) {
    define( 'WRS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WRS_PLUGIN_FILE' ) ) {
    define( 'WRS_PLUGIN_FILE', __FILE__ );
}

// Cargar archivos de inclusión del plugin
// -------------------------------------------------------------------------
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-core-functions.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-cpt-tax.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-shortcodes.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-ajax-handlers.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-user-registration.php'; // Contiene wrs_registration_completed_actions
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-user-account.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-referral-logic.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/admin/wrs-admin-menu.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/admin/wrs-admin-network-page.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/admin/wrs-admin-user-profile.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/admin/wrs-admin-export.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/integrations/wrs-zoho-integration.php';
require_once WRS_PLUGIN_DIR_PATH . 'includes/wrs-enqueue-assets.php';


// Registrar Hooks (acciones y filtros)
// -------------------------------------------------------------------------

// Con esta nueva línea:


add_action( 'template_redirect', 'wrs_force_redirect_to_thank_you_page', 1 );


// -- Hooks de Inicialización General --
add_action('init', 'wrs_start_session', 1); // De wrs-core-functions.php (MUY IMPORTANTE PARA SESIONES)
add_action('init', 'wrs_register_initiatives_cpt'); 
add_action('init', 'wrs_register_initiative_category_taxonomy', 0); 
add_action('init', 'wrs_add_account_endpoints'); // De wrs-user-account.php (Eliminada la duplicación)
add_action('template_redirect', 'wrs_capture_referrer_and_initiative_session', 5); // Cambiado de 'init' a 'template_redirect'

// -- Lógica de Registro de Usuario --
add_action( 'woocommerce_register_form', 'wrs_add_custom_registration_fields', 20 );
add_filter( 'woocommerce_registration_errors', 'wrs_validate_custom_registration_fields', 10, 3 );
add_action( 'woocommerce_created_customer', 'wrs_save_custom_registration_fields', 10, 1 ); 
add_action( 'woocommerce_created_customer', 'wrs_send_custom_welcome_email', 20, 2 ); 
// ELIMINADO: add_filter( 'woocommerce_registration_redirect', 'wrs_custom_registration_redirect', 99 );
// ELIMINADO: error_log('[WRS Main Plugin DEBUG] Filtro woocommerce_registration_redirect AÑADIDO...');

// -- Hooks para el Comportamiento Post-Registro --
add_action('wp_loaded', 'wrs_prevent_autologin_after_registration', 5); // Previene el auto-login
add_action('woocommerce_created_customer', 'wrs_registration_completed_actions', 90, 3); // Establece la bandera de sesión para la redirección

// -- Hooks para "Mi Cuenta" de WooCommerce --
add_filter('woocommerce_account_menu_items', 'wrs_add_account_menu_items', 20, 1); 
add_action('woocommerce_account_referral-links_endpoint', 'wrs_referral_links_endpoint_content'); 
add_action('woocommerce_account_genealogy-tree_endpoint', 'wrs_genealogy_tree_endpoint_content'); 
add_action('woocommerce_account_my-profile_endpoint', 'wrs_my_profile_endpoint_content'); 

// Hooks para validar y guardar los datos del formulario "Mi Perfil WRS"
add_action( 'woocommerce_save_account_details_errors', 'wrs_validate_my_profile_details_on_save', 10, 2 );
add_action( 'woocommerce_save_account_details', 'wrs_save_my_profile_details_wrs_fields', 10, 1 );

// -- Hooks de Administración --
add_action('admin_menu', 'wrs_add_referral_network_admin_menu'); 
add_action('show_user_profile', 'wrs_display_custom_user_profile_section'); 
add_action('edit_user_profile', 'wrs_display_custom_user_profile_section'); 
add_action('personal_options_update', 'wrs_save_custom_user_profile_fields'); 
add_action('edit_user_profile_update', 'wrs_save_custom_user_profile_fields'); 
add_action('admin_action_export_wrs_network', 'wrs_handle_network_data_export'); 

// -- Hooks para Encolar Scripts y Estilos --
// add_action('wp_enqueue_scripts', 'wrs_frontend_enqueue_assets'); // Actualmente comentado, usa wrs_force_frontend_scripts_for_debug
add_action('admin_enqueue_scripts', 'wrs_admin_enqueue_assets'); 

// -- Hooks para Shortcodes --
add_shortcode( 'wrs_register_button', 'wrs_register_button_shortcode_handler' ); 

// -- Hooks para Manejadores AJAX --
add_action( 'wp_ajax_nopriv_wrs_get_municipios', 'wrs_ajax_get_municipios_handler' ); 
add_action( 'wp_ajax_wrs_get_municipios', 'wrs_ajax_get_municipios_handler' ); 

// -- Hook para la Plantilla de Iniciativas --
add_filter( 'template_include', 'wrs_include_initiative_template' ); 

// -- NUEVA FUNCIÓN Y HOOK PARA REDIRECCIÓN FORZOSA --
/**
 * Intenta forzar la redirección a la página de inicio después del registro,
 * basándose en una bandera de sesión establecida por wrs_registration_completed_actions.
 */
function wrs_force_redirect_on_template_redirect() {
    // No hacer nada en admin, o si WC o la sesión no están disponibles/activas.
    if ( is_admin() || ! function_exists('WC') || ! WC()->session || ( function_exists('WC') && WC()->session && ! WC()->session->has_session() ) ) {
        if (is_admin()) return; // Salir si es admin
        // Si WC() o WC()->session no existen, o si existe la sesión pero no está iniciada, no hacer nada.
        // El chequeo WC()->session->has_session() es importante para evitar errores si la sesión no fue iniciada.
        // wrs_start_session() en 'init' debería haberla iniciado si no es una petición REST/AJAX/CRON.
        if (function_exists('WC') && WC()->session && !WC()->session->has_session()) {
             error_log('[WRS Force Redirect] Intento de acceso a sesión de WC, pero no está activa.');
        }
        return; 
    }

    $just_registered_customer_id = WC()->session->get('wrs_just_registered');
    $redirect_time = WC()->session->get('wrs_redirect_time');

    // Solo actuar si la bandera está presente y no ha pasado mucho tiempo (evitar bucles o redirects viejos)
    if ( $just_registered_customer_id && $redirect_time && (time() - $redirect_time < 30) ) { // Ventana de 30 segundos
        
        error_log('[WRS Force Redirect] Bandera de sesión detectada para customer ID: ' . $just_registered_customer_id . '. Intentando redirección forzosa a home.');

        // Limpiar la bandera INMEDIATAMENTE para que no redirija en la siguiente carga de página
        WC()->session->set('wrs_just_registered', null); 
        WC()->session->set('wrs_redirect_time', null);
        // Considera usar WC()->session->__unset('wrs_just_registered'); si set(null) no la elimina completamente.

        // wrs_prevent_autologin_after_registration debería haber prevenido el login.
        // No es necesario un wp_logout() aquí si ese sistema funciona, y podría causar doble redirección.

        $home_url = home_url('/');
        
        if (!headers_sent()) {
            wp_safe_redirect( $home_url );
            exit;
        } else {
            error_log('[WRS Force Redirect] ERROR: Headers already sent. No se pudo redirigir con PHP. Intentando JS fallback.');
            // Fallback a JS si las cabeceras ya se enviaron
            echo '<script type="text/javascript">window.location.replace("' . esc_url_raw($home_url) . '");</script>';
            // Es importante salir aquí también para detener más procesamiento de la página original.
            // Sin embargo, si se usa echo, el exit; de PHP podría no ser necesario si el script JS ya causó la redirección.
            // Para estar seguros, lo mantenemos.
            exit; 
        }
    }
}
add_action( 'template_redirect', 'wrs_force_redirect_on_template_redirect', 1 ); // Prioridad muy temprana


// -- Hooks de Activación/Desactivación --
function wrs_plugin_on_activate() {
    if (function_exists('wrs_register_initiatives_cpt')) { wrs_register_initiatives_cpt(); }
    if (function_exists('wrs_register_initiative_category_taxonomy')) { wrs_register_initiative_category_taxonomy(); }
    if (function_exists('wrs_add_account_endpoints')) { wrs_add_account_endpoints(); }
    flush_rewrite_rules();
}
register_activation_hook( WRS_PLUGIN_FILE, 'wrs_plugin_on_activate' );

function wrs_plugin_on_deactivate() {
    if (function_exists('wrs_register_initiatives_cpt')) { wrs_register_initiatives_cpt(); }
    if (function_exists('wrs_register_initiative_category_taxonomy')) { wrs_register_initiative_category_taxonomy(); }
    if (function_exists('wrs_add_account_endpoints')) { wrs_add_account_endpoints(); }
    flush_rewrite_rules();
}
register_deactivation_hook( WRS_PLUGIN_FILE, 'wrs_plugin_on_deactivate' );


// -- Función para encolar scripts del gráfico del frontend (Tooltips Manuales) --
function wrs_force_frontend_scripts_for_debug() {
    if ( function_exists('is_account_page') && is_account_page() && function_exists('is_wc_endpoint_url') && is_wc_endpoint_url( 'genealogy-tree' ) ) {
        
        $plugin_dir_url = WRS_PLUGIN_DIR_URL; 
        $plugin_version = WRS_PLUGIN_VERSION; 

        wp_enqueue_script('cytoscape-lib', 'https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.28.1/cytoscape.min.js', array(), '3.28.1', true );
        
        // Popper y cytoscape-popper ya no se encolan aquí.
            
        wp_enqueue_script( 
            'wrs-cytoscape-custom-chart-frontend', 
            $plugin_dir_url . 'assets/js/wrs-cytoscape-chart.js',
            array('jquery', 'cytoscape-lib'), // Solo jQuery y Cytoscape Lib
            $plugin_version . '_' . time(), true 
        );
        error_log('[WRS Frontend Enqueue - Tooltips Manuales] Scripts para frontend encolados.');
    }
}
add_action( 'wp_enqueue_scripts', 'wrs_force_frontend_scripts_for_debug', 9999 ); 

?>