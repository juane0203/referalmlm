<?php
/**
 * Funcionalidad para la Exportación de Datos a CSV desde el Admin.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Maneja la solicitud de exportación de la red de referidos a CSV.
 * Incluye ID y Título de la Iniciativa de Origen.
 */
function wrs_handle_network_data_export() { // Nombre de función más genérico
    // 1. Verificar Nonce y Permisos
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'export_wrs_network_nonce' ) ) {
         wp_die( esc_html__( 'Error de seguridad (Nonce inválido).', 'woodmart-referral-system' ) );
    }
     if ( ! current_user_can( 'manage_options' ) ) { // O una capacidad más específica si la defines
        wp_die( esc_html__( 'No tienes permisos para realizar esta acción.', 'woodmart-referral-system' ) );
    }

    // 2. Preparar nombre del archivo y cabeceras HTTP
    $filename = 'wrs_network_export_' . date('Y-m-d_H-i') . '.csv';
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=' . $filename );
    header( 'Pragma: no-cache' ); 
    header( 'Expires: 0' );

    // 3. Abrir flujo de salida PHP y añadir BOM UTF-8
    $output_stream = fopen( 'php://output', 'w' );
    if ($output_stream === false) {
        wp_die( esc_html__( 'No se pudo abrir el flujo de salida para el CSV.', 'woodmart-referral-system' ) );
    }
    fprintf( $output_stream, chr(0xEF) . chr(0xBB) . chr(0xBF) ); 

    // 4. Escribir fila de cabecera actualizada
    $header_row = array(
        'ID Usuario', 'Nombres WRS', 'Apellidos WRS', 'Nombre Usuario WP', 'Email WP',
        'Tipo Doc', 'Num Doc', 'Teléfono WRS', 'Género', 'Fecha Nacimiento',
        'Departamento Vivienda (Clave)', 'Municipio Vivienda (Clave)', 'Localidad Bogotá (Clave)',
        'Barrio', 'Vereda', 'Dirección WRS',
        'Estado Civil', 'Nivel Educativo', 'Profesión', 'Ocupación',
        'Es Cuidador', 'Num Hijos', 'Temas Interés', 'Observaciones',
        'ID Iniciativa Origen', 'Título Iniciativa Origen',
        'Fecha Registro WP', 'Puede Referir?',
        'ID Referente', 'Nombre Referente', 'Email Referente'
    );
    fputcsv( $output_stream, $header_row );

    // 5. Obtener TODOS los usuarios
    $all_users = get_users( array('number' => -1, 'fields' => 'all_with_meta') );

    // 6. Recorrer usuarios y escribir filas de datos
    if ( ! empty( $all_users ) ) {
        foreach ( $all_users as $user_obj ) { // Renombrar variable para evitar conflicto con $user en otros contextos
            $user_id = $user_obj->ID;

            $get_meta_value = function($key, $current_user_id) {
                $meta = get_user_meta($current_user_id, $key, true);
                return ($meta !== '') ? $meta : ''; 
            };

            $user_data_row = array();
            $user_data_row[] = $user_id;
            $user_data_row[] = $get_meta_value('wrs_nombres', $user_id);
            $user_data_row[] = $get_meta_value('wrs_apellidos', $user_id);
            $user_data_row[] = $user_obj->user_login;
            $user_data_row[] = $user_obj->user_email;
            $user_data_row[] = $get_meta_value('wrs_tipo_doc', $user_id);
            $user_data_row[] = $get_meta_value('wrs_numero_doc', $user_id);
            $user_data_row[] = $get_meta_value('wrs_telefono', $user_id);
            $user_data_row[] = $get_meta_value('wrs_genero', $user_id);
            $user_data_row[] = $get_meta_value('wrs_fecha_nacimiento', $user_id);
            $user_data_row[] = $get_meta_value('wrs_departamento_viv', $user_id);
            $user_data_row[] = $get_meta_value('wrs_municipio_viv', $user_id);
            $user_data_row[] = $get_meta_value('wrs_localidad', $user_id);
            $user_data_row[] = $get_meta_value('wrs_barrio', $user_id);
            $user_data_row[] = $get_meta_value('wrs_vereda', $user_id);
            $user_data_row[] = $get_meta_value('wrs_direccion', $user_id);
            $user_data_row[] = $get_meta_value('wrs_estado_civil', $user_id);
            $user_data_row[] = $get_meta_value('wrs_nivel_educativo', $user_id);
            $user_data_row[] = $get_meta_value('wrs_profesion', $user_id);
            $user_data_row[] = $get_meta_value('wrs_ocupacion', $user_id);
            $user_data_row[] = $get_meta_value('wrs_es_cuidador', $user_id);
            $num_hijos = $get_meta_value('wrs_num_hijos', $user_id);
            $user_data_row[] = ($num_hijos !== '') ? $num_hijos : '';
            $temas = $get_meta_value('wrs_temas_interes', $user_id);
            $user_data_row[] = is_array($temas) ? implode('; ', $temas) : ($temas ?: '');
            $user_data_row[] = $get_meta_value('wrs_observaciones', $user_id);

            $origin_initiative_id_val = $get_meta_value('_wrs_origin_landing_id', $user_id);
            $user_data_row[] = $origin_initiative_id_val ? $origin_initiative_id_val : '';
            $origin_initiative_title_val = '';
            if ($origin_initiative_id_val && is_numeric($origin_initiative_id_val)) {
                $initiative_post_obj = get_post(absint($origin_initiative_id_val));
                if ($initiative_post_obj && $initiative_post_obj->post_type === 'wrs_landing_page') {
                    $origin_initiative_title_val = $initiative_post_obj->post_title;
                }
            }
            $user_data_row[] = $origin_initiative_title_val;

            $user_data_row[] = $user_obj->user_registered;
            $user_data_row[] = ($get_meta_value('_wrs_can_refer', $user_id) === 'yes') ? 'Sí' : 'No';

            $referrer_id_val = $get_meta_value('_wrs_referrer_id', $user_id);
            $referrer_name_display_val = ''; $referrer_email_val = ''; $referrer_id_display_val = '';
            if (!empty($referrer_id_val) && absint($referrer_id_val) > 0) {
                 $referrer_user_data = get_userdata(absint($referrer_id_val));
                 if ( $referrer_user_data ) {
                     $ref_first_name = $referrer_user_data->first_name; $ref_last_name = $referrer_user_data->last_name;
                     $ref_full_name = trim($ref_first_name . ' ' . $ref_last_name);
                     $referrer_name_display_val = !empty($ref_full_name) ? $ref_full_name : $referrer_user_data->display_name;
                     $referrer_email_val = $referrer_user_data->user_email;
                     $referrer_id_display_val = absint($referrer_id_val);
                 } else { $referrer_id_display_val = 'ID Inválido (' . $referrer_id_val . ')'; }
            }
            $user_data_row[] = $referrer_id_display_val;
            $user_data_row[] = $referrer_name_display_val;
            $user_data_row[] = $referrer_email_val;

            fputcsv( $output_stream, $user_data_row );
        }
    }
    fclose( $output_stream );
    exit;
}
// El add_action se moverá al archivo principal o a un cargador de hooks.