<?php
/**
 * Shortcodes del Plugin WRS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Genera el HTML para un botón de registro que incluye dinámicamente el parámetro 'ref'.
 *
 * Uso: [wrs_register_button text="Regístrate Ahora" class="tu-clase-css"]
 *
 * @param array $atts Atributos del shortcode (text, class).
 * @return string HTML del botón.
 */
function wrs_register_button_shortcode_handler( $atts ) { // Renombrado para claridad (handler)
    // Valores por defecto para los atributos
    $atts = shortcode_atts( array(
        'text'  => __( 'Registrarse', 'woodmart-referral-system' ),
        'class' => '', 
    ), $atts, 'wrs_register_button' );

    // Obtener la URL base de la página Mi Cuenta
    $my_account_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
    if ( ! $my_account_url ) {
        return '';
    }

    // Construir la URL base de registro (endpoint de registro de WooCommerce)
    $register_url_base = add_query_arg( 'action', 'register', $my_account_url );

    // Comprobar si hay un código 'ref' en la URL actual de la página donde se muestra el shortcode
    $ref_code = null;
    if ( isset( $_GET['ref'] ) ) {
        $ref_code = sanitize_user( wp_unslash( $_GET['ref'] ), true );
    }

    // Construir la URL final del botón
    $final_button_url = $register_url_base;
    if ( $ref_code ) {
        // Añadir el parámetro ref a la URL base de registro
        $final_button_url = add_query_arg( 'ref', $ref_code, $register_url_base );
    }

    // Clases CSS para el botón
    $button_classes = 'wrs-register-shortcode-button button elementor-button elementor-button-link elementor-size-sm ';
    if ( ! empty( $atts['class'] ) ) {
        $button_classes .= sanitize_html_class( $atts['class'] );
    }

    // Generar el HTML del botón
    $button_html = '<a href="' . esc_url( $final_button_url ) . '" class="' . esc_attr( trim($button_classes) ) . '">';
    $button_html .= '<span>' . esc_html( $atts['text'] ) . '</span>';
    $button_html .= '</a>';

    return $button_html;
}
// El add_shortcode se moverá al archivo principal del plugin o a un cargador de hooks.