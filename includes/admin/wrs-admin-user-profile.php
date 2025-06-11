<?php
/**
 * Funcionalidad para la página de Edición de Perfil de Usuario en el Admin WP.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Muestra una sección personalizada con la información de referidos y perfil WRS
 * en la página de edición de usuario del administrador.
 *
 * @param WP_User $user Objeto del usuario que se está editando.
 */
function wrs_display_custom_user_profile_section( $user ) { // Nombre de función más descriptivo
    if ( ! current_user_can( 'edit_users' ) ) { return; }

    // Obtener datos WRS del usuario
    $referrer_id     = get_user_meta( $user->ID, '_wrs_referrer_id', true );
    $can_refer       = get_user_meta( $user->ID, '_wrs_can_refer', true );
    $is_checked      = ( $can_refer === 'yes' );
    $origin_landing_id = get_user_meta( $user->ID, '_wrs_origin_landing_id', true );

    $iniciativa_origen_display = __('N/A', 'woodmart-referral-system');
    if ( !empty($origin_landing_id) && absint($origin_landing_id) > 0 ) {
        $initiative_post = get_post( absint($origin_landing_id) );
        if ( $initiative_post && $initiative_post->post_type === 'wrs_landing_page' ) { // 'wrs_landing_page' es el nombre programático
            $iniciativa_origen_display = esc_html( get_the_title( $initiative_post ) );
        } else {
            $iniciativa_origen_display = esc_html__('ID de Iniciativa inválido o no encontrado', 'woodmart-referral-system') . ' (' . absint($origin_landing_id) . ')';
        }
    }

    // Lista de todos los campos WRS para mostrar (ajusta según necesites)
    $wrs_profile_fields_to_display = array(
        'wrs_nombres'           => __('Nombres (WRS)', 'woodmart-referral-system'),
        'wrs_apellidos'         => __('Apellidos (WRS)', 'woodmart-referral-system'),
        'wrs_tipo_doc'          => __('Tipo Documento', 'woodmart-referral-system'),
        'wrs_numero_doc'        => __('Número Documento', 'woodmart-referral-system'),
        'wrs_telefono'          => __('Teléfono', 'woodmart-referral-system'),
        'wrs_genero'            => __('Género', 'woodmart-referral-system'),
        'wrs_fecha_nacimiento'  => __('Fecha Nacimiento', 'woodmart-referral-system'),
        'wrs_departamento_viv'  => __('Departamento Vivienda (Clave)', 'woodmart-referral-system'),
        'wrs_municipio_viv'     => __('Municipio Vivienda (Clave)', 'woodmart-referral-system'),
        'wrs_localidad'         => __('Localidad Bogotá (Clave)', 'woodmart-referral-system'),
        'wrs_barrio'            => __('Barrio', 'woodmart-referral-system'),
        'wrs_vereda'            => __('Vereda', 'woodmart-referral-system'),
        'wrs_direccion'         => __('Dirección', 'woodmart-referral-system'),
        'wrs_estado_civil'      => __('Estado Civil', 'woodmart-referral-system'),
        'wrs_nivel_educativo'   => __('Nivel Educativo', 'woodmart-referral-system'),
        'wrs_profesion'         => __('Profesión', 'woodmart-referral-system'),
        'wrs_ocupacion'         => __('Ocupación', 'woodmart-referral-system'),
        'wrs_es_cuidador'       => __('¿Es Cuidador?', 'woodmart-referral-system'),
        'wrs_num_hijos'         => __('Número de Hijos', 'woodmart-referral-system'),
        'wrs_temas_interes'     => __('Temas de Interés', 'woodmart-referral-system'),
        'wrs_observaciones'     => __('Observaciones', 'woodmart-referral-system'),
    );

    // Funciones helper para obtener etiquetas (asumimos que están en wrs-core-functions.php)
    $all_geo_data = function_exists('wrs_get_colombia_geo_data') ? wrs_get_colombia_geo_data() : [];
    $localidades_bogota = function_exists('wrs_get_bogota_localidades') ? wrs_get_bogota_localidades() : [];

    ?>
    <h2><?php esc_html_e( 'Información Adicional WRS y Referidos', 'woodmart-referral-system' ); ?></h2>
    <table class="form-table" id="wrs-user-profile-info">
        <tbody>
            <tr>
                <th><label><?php esc_html_e( 'Referido Por', 'woodmart-referral-system' ); ?></label></th>
                <td>
                    <?php
                    if(!empty($referrer_id) && absint($referrer_id) > 0){
                        $ref_data = get_userdata(absint($referrer_id));
                        if($ref_data){
                            $ref_name = trim($ref_data->first_name.' '.$ref_data->last_name);
                            $disp_name = !empty($ref_name) ? $ref_name : $ref_data->display_name;
                            echo '<a href="'.esc_url(get_edit_user_link(absint($referrer_id))).'">'.esc_html($disp_name).'</a> (ID: '.esc_html(absint($referrer_id)).')';
                        } else {
                            echo esc_html__('ID de referente inválido','woodmart-referral-system').' ('.esc_html($referrer_id).')';
                        }
                    } else {
                        esc_html_e('N/A','woodmart-referral-system');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="wrs_can_refer_admin"><?php esc_html_e( 'Permitir Referir', 'woodmart-referral-system' ); ?></label></th>
                <td>
                    <input type="checkbox" name="wrs_can_refer" id="wrs_can_refer_admin" value="yes" <?php checked( $is_checked ); ?> />
                    <span class="description"><?php esc_html_e('Marcar si este usuario puede referir a otros.','woodmart-referral-system'); ?></span>
                    <?php wp_nonce_field('wrs_save_can_refer_nonce_'.$user->ID,'wrs_can_refer_nonce'); // Nonce para el guardado ?>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Iniciativa de Origen', 'woodmart-referral-system' ); ?></label></th>
                <td><?php echo esc_html($iniciativa_origen_display); ?></td>
            </tr>
            <tr><td colspan="2"><hr><h3><?php esc_html_e( 'Datos Personales Adicionales (WRS)', 'woodmart-referral-system' ); ?></h3></td></tr>
            <?php
            foreach ($wrs_profile_fields_to_display as $meta_key => $label) {
                $value = get_user_meta($user->ID, $meta_key, true);
                $display_value = __('N/A', 'woodmart-referral-system');

                if ($value !== '') {
                    if (is_array($value)) {
                        // Para campos como 'wrs_temas_interes'
                        $temp_labels = [];
                        $options_list = function_exists('wrs_get_options') ? wrs_get_options(str_replace('wrs_', '', $meta_key)) : [];
                        foreach ($value as $single_val_key) {
                            $temp_labels[] = isset($options_list[$single_val_key]) ? html_entity_decode($options_list[$single_val_key]) : $single_val_key;
                        }
                        $display_value = !empty($temp_labels) ? implode(', ', $temp_labels) : __('N/A', 'woodmart-referral-system');
                    } elseif (in_array($meta_key, ['wrs_tipo_doc', 'wrs_genero', 'wrs_estado_civil', 'wrs_nivel_educativo', 'wrs_ocupacion', 'wrs_es_cuidador'])) {
                        $options_list = function_exists('wrs_get_options') ? wrs_get_options(str_replace('wrs_', '', $meta_key)) : [];
                        $display_value = isset($options_list[$value]) ? html_entity_decode($options_list[$value]) : $value;
                    } elseif ($meta_key === 'wrs_departamento_viv') {
                        $display_value = isset($all_geo_data[$value]['name']) ? html_entity_decode($all_geo_data[$value]['name']) : $value;
                    } elseif ($meta_key === 'wrs_municipio_viv') {
                        $depto_key_for_mcipio = get_user_meta($user->ID, 'wrs_departamento_viv', true);
                        $display_value = isset($all_geo_data[$depto_key_for_mcipio]['cities'][$value]['name']) ? html_entity_decode($all_geo_data[$depto_key_for_mcipio]['cities'][$value]['name']) : $value;
                    } elseif ($meta_key === 'wrs_localidad') {
                         $depto_key_for_localidad = get_user_meta($user->ID, 'wrs_departamento_viv', true);
                         if ($depto_key_for_localidad === 'BOGOTA_DC') {
                            $display_value = isset($localidades_bogota[$value]) ? html_entity_decode($localidades_bogota[$value]) : $value;
                         } else {
                             $display_value = __('N/A (No es Bogotá D.C.)', 'woodmart-referral-system');
                         }
                    } elseif ($meta_key === 'wrs_observaciones') {
                        $display_value = nl2br(esc_html($value));
                    } else {
                        $display_value = esc_html($value);
                    }
                }
                ?>
                <tr>
                    <th><label><?php echo esc_html($label); ?></label></th>
                    <td><?php echo ($value !== '') ? $display_value : __('N/A', 'woodmart-referral-system'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
// El add_action se moverá al archivo principal o a un cargador de hooks.

/**
 * Guarda el valor del campo 'Permitir Referir' y otros campos WRS
 * al actualizar el perfil de usuario desde el panel de administración.
 *
 * @param int $user_id ID del usuario que se está actualizando.
 */
function wrs_save_custom_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    // Nonce para 'Permitir Referir'
    if ( isset( $_POST['wrs_can_refer_nonce'] ) && wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['wrs_can_refer_nonce'])), 'wrs_save_can_refer_nonce_' . $user_id ) ) {
        if ( isset( $_POST['wrs_can_refer'] ) && $_POST['wrs_can_refer'] === 'yes' ) {
            update_user_meta( $user_id, '_wrs_can_refer', 'yes' );
        } else {
            update_user_meta( $user_id, '_wrs_can_refer', 'no' );
        }
    }
    // Aquí podrías añadir lógica para editar otros campos WRS desde el admin si fuera necesario,
    // pero el requerimiento actual solo especifica el checkbox '_wrs_can_refer'.
    // El resto de los campos se muestran como solo lectura.
}
// Los add_action se moverán al archivo principal o a un cargador de hooks.