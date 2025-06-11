<?php
/**
 * Manejadores AJAX del Plugin WRS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manejador AJAX para obtener municipios según el departamento seleccionado.
 * Utiliza los datos de wrs_get_colombia_geo_data() y decodifica entidades HTML.
 *
 * Esta función es llamada por el script assets/js/wrs-registration-dynamics.js
 */
function wrs_ajax_get_municipios_handler() { // Nombre de función ligeramente más específico
    // Opcional: Añadir verificación de nonce aquí si se implementa en el lado del cliente (JavaScript)
    // if ( !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wrs_municipio_nonce_action') ) {
    //     wp_send_json_error(array('message' => __('Error de seguridad.', 'woodmart-referral-system')), 403);
    //     return;
    // }

    if ( ! function_exists('wrs_get_colombia_geo_data') ) {
        wp_send_json_error(array('message' => __('Error interno: La función de datos geográficos no está disponible.', 'woodmart-referral-system')), 500);
        return;
    }

    $all_geo_data = wrs_get_colombia_geo_data();
    $department_key = isset($_POST['departamento']) ? sanitize_text_field(wp_unslash($_POST['departamento'])) : '';
    // error_log('WRS AJAX Handler: Received department key: ' . $department_key);

    $response_municipios = array();

    if ( !empty($department_key) && isset($all_geo_data[$department_key]['cities']) ) {
        $cities_data = $all_geo_data[$department_key]['cities'];
        $municipios_keys = array_keys($cities_data);
        sort($municipios_keys); // Ordenar alfabéticamente por clave (nombre del municipio)

        foreach ($municipios_keys as $municipio_key) {
             if (isset($cities_data[$municipio_key]['name'])) {
                 // Decodificar entidades HTML ANTES de añadir a la respuesta para asegurar que el texto se envíe correctamente
                 $decoded_name = html_entity_decode($cities_data[$municipio_key]['name']);
                 $response_municipios[$municipio_key] = $decoded_name;
             }
        }
        // error_log('WRS AJAX Handler: Prepared response: ' . print_r($response_municipios, true));
        wp_send_json_success( $response_municipios );

    } else {
         // Caso Especial Bogotá D.C. (si 'BOGOTA_DC' es una clave de departamento que solo tiene una "ciudad" con el mismo nombre)
         if ($department_key === 'BOGOTA_DC' && isset($all_geo_data[$department_key]['cities']['BOGOTA_DC']['name'])) {
              wp_send_json_success( array('BOGOTA_DC' => html_entity_decode($all_geo_data[$department_key]['cities']['BOGOTA_DC']['name'])) );
         } else {
              // error_log('WRS AJAX Handler: Department key not found or no cities: ' . $department_key);
              wp_send_json_error( array( 'message' => html_entity_decode( __('Departamento no v&aacute;lido o sin municipios.', 'woodmart-referral-system') ) ), 404 );
         }
    }
    // wp_die(); // Importante para terminar correctamente las peticiones AJAX en WordPress
}
// Los add_action para wp_ajax_* se moverán al archivo principal o a un cargador de hooks.
// add_action( 'wp_ajax_nopriv_wrs_get_municipios', 'wrs_ajax_get_municipios_handler' );
// add_action( 'wp_ajax_wrs_get_municipios', 'wrs_ajax_get_municipios_handler' );