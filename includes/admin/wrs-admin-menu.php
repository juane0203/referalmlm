<?php
/**
 * Funciones para añadir páginas al menú de administración de WordPress.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Añade la página de la red de referidos al menú de administración (bajo Usuarios).
 */
function wrs_add_referral_network_admin_menu() {
    // La función que renderiza la página ('wrs_render_admin_network_page')
    // se definirá en 'wrs-admin-network-page.php'
    add_users_page(
        __( 'Red de Referidos Completa', 'woodmart-referral-system' ), // Título de la página
        __( 'Red de Referidos', 'woodmart-referral-system' ),       // Título del menú
        'manage_options',                                           // Capacidad requerida
        'wrs-referral-network',                                     // Slug de la página
        'wrs_render_admin_network_page_content'                     // Nueva función callback para el contenido
    );
}
// El add_action se moverá al archivo principal o a un cargador de hooks.

// Nota: La página de ajustes de Zoho CRM ya tiene su propia función para añadirla al menú
// en el archivo 'includes/integrations/wrs-zoho-integration.php'.
// No es necesario repetirla aquí.