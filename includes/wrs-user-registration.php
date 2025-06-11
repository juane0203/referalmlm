<?php
/**
 * Funcionalidad relacionada con el Registro de Usuarios de WooCommerce
 * - Añadir campos personalizados al formulario.
 * - Validar campos personalizados.
 * - Guardar campos personalizados.
 * - Lógica de captura de referente e iniciativa durante el registro.
 * - Comportamiento post-registro (página de agradecimiento personalizada).
 * - Email personalizado con link de referencia y establecimiento de contraseña.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * --------------------------------------------------------------------------
 * NOTA SOBRE EL ALMACENAMIENTO DE DATOS DE USUARIO PERSONALIZADOS EN WORDPRESS
 * --------------------------------------------------------------------------
 * WordPress ofrece una forma estándar y flexible de almacenar datos adicionales
 * para los usuarios a través de la tabla `wp_usermeta`. Esta tabla está diseñada
 * como un sistema de pares clave-valor, permitiendo añadir cualquier cantidad
 * de información personalizada sin necesidad de alterar la estructura de la
 * tabla principal `wp_users`.
 *
 * En este plugin, todos los campos personalizados del formulario de registro
 * (con prefijo `wrs_`) se almacenan en `wp_usermeta`. Campos como
 * nombres y apellidos también se mapean a `first_name` y `last_name` en `wp_users`.
 */

/**
 * Añade campos personalizados al formulario de registro de WooCommerce.
 * VERSIÓN COMPLETA con detección múltiple de iniciativa de origen y detección forzada.
 */
function wrs_add_custom_registration_fields() {
    
    // === FORZAR DETECCIÓN DE INICIATIVA SI NO HAY ===
    $force_detection_done = false;

    // Verificar si tenemos iniciativa actual
    $current_initiative_check = wrs_get_initiative_from_multiple_sources();
    if ( $current_initiative_check['id'] <= 0 ) {
        
        // Método 1: Desde referrer (si viene de una landing page)
        $referrer = wp_get_referer();
        if ( $referrer && !$force_detection_done ) {
            if ( preg_match('/\/iniciativa\/([^\/\?]+)/', $referrer, $matches) ) {
                $landing_slug = sanitize_title($matches[1]);
                $landing_post = get_page_by_path($landing_slug, OBJECT, 'wrs_landing_page');
                
                if ( $landing_post && $landing_post->post_status === 'publish' ) {
                    wrs_set_initiative_with_backup( $landing_post->ID, 'form_referrer_detection' );
                    error_log('[WRS Form Force Detection] Iniciativa capturada desde referrer en formulario: ' . $landing_slug . ' (ID: ' . $landing_post->ID . ')');
                    $force_detection_done = true;
                }
            }
        }
        
        // Método 2: Desde la URL actual si estamos en una landing page
        if ( !$force_detection_done && is_singular('wrs_landing_page') ) {
            global $post;
            if ( $post && $post->post_type === 'wrs_landing_page' ) {
                wrs_set_initiative_with_backup( $post->ID, 'form_current_page_detection' );
                error_log('[WRS Form Force Detection] Iniciativa capturada desde página actual: ID ' . $post->ID);
                $force_detection_done = true;
            }
        }
        
        // Método 3: Buscar en la sesión PHP (último recurso)
        if ( !$force_detection_done && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['wrs_last_landing_id']) ) {
            $session_landing = absint($_SESSION['wrs_last_landing_id']);
            if ( $session_landing > 0 ) {
                $landing_post = get_post($session_landing);
                if ( $landing_post && $landing_post->post_type === 'wrs_landing_page' && $landing_post->post_status === 'publish' ) {
                    wrs_set_initiative_with_backup( $session_landing, 'form_php_session_detection' );
                    error_log('[WRS Form Force Detection] Iniciativa capturada desde sesión PHP: ID ' . $session_landing);
                    $force_detection_done = true;
                }
            }
        }
    }

    if ( $force_detection_done ) {
        error_log('[WRS Form Force Detection] Detección forzada completada exitosamente');
    } else {
        error_log('[WRS Form Force Detection] No se pudo detectar iniciativa por ningún método');
    }
    
    $posted_data = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce es verificado por WooCommerce al procesar el formulario.
    
    // === DETECCIÓN MÚLTIPLE DE INICIATIVA DE ORIGEN ===
    $current_landing_id = 0;
    $landing_source = 'none';
    
    // Método 1: Desde sesión WC (prioritario)
    if ( function_exists('WC') && WC()->session ) {
        $session_landing = WC()->session->get('wrs_origin_landing_id_wc');
        if ( !empty($session_landing) && is_numeric($session_landing) ) {
            $current_landing_id = absint($session_landing);
            $landing_source = 'wc_session';
        }
    }
    
    // Método 2: Desde cookie de respaldo
    if ( !$current_landing_id && isset($_COOKIE['wrs_origin_landing_backup']) ) {
        $cookie_landing = $_COOKIE['wrs_origin_landing_backup'];
        if ( !empty($cookie_landing) && is_numeric($cookie_landing) ) {
            $current_landing_id = absint($cookie_landing);
            $landing_source = 'cookie_backup';
        }
    }
    
    // Método 3: Desde POST (si el formulario fue enviado con error y se recarga)
    if ( !$current_landing_id && isset($_POST['wrs_hidden_landing_id']) ) {
        $post_landing = $_POST['wrs_hidden_landing_id'];
        if ( !empty($post_landing) && is_numeric($post_landing) ) {
            $current_landing_id = absint($post_landing);
            $landing_source = 'post_hidden';
        }
    }
    
    // Método 4: Desde GET (URL directa con parámetro)
    if ( !$current_landing_id && isset($_GET['landing_id']) ) {
        $get_landing = $_GET['landing_id'];
        if ( !empty($get_landing) && is_numeric($get_landing) ) {
            $current_landing_id = absint($get_landing);
            $landing_source = 'get_param';
        }
    }
    
    // Log de debug mejorado
    error_log('[WRS Form Fields] Landing ID detectado: ' . $current_landing_id . ' (fuente: ' . $landing_source . ')');
    
    ?>
    
    <!-- CAMPO OCULTO PARA PRESERVAR LA INICIATIVA (CRÍTICO) -->
    <?php if ($current_landing_id > 0): ?>
        <input type="hidden" name="wrs_hidden_landing_id" value="<?php echo absint($current_landing_id); ?>" />
        <!-- Debug info (solo visible en el código fuente) -->
        <!-- WRS Debug: Landing ID <?php echo absint($current_landing_id); ?> from <?php echo esc_attr($landing_source); ?> -->
    <?php endif; ?>
    
    <!-- CAMPO OCULTO PARA TRACKING DE FUENTE -->
    <input type="hidden" name="wrs_landing_source" value="<?php echo esc_attr($landing_source); ?>" />
    
    <!-- MOSTRAR ADVERTENCIA SI NO HAY INICIATIVA DETECTADA -->
    <?php if ($current_landing_id <= 0): ?>
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
            <h4 style="color: #856404; margin: 0 0 10px 0;">
                ℹ️ Registro Directo
            </h4>
            <p style="color: #856404; margin: 0; font-size: 14px;">
                Te estás registrando directamente. Si tienes un código de invitación o vienes de una iniciativa específica, 
                <a href="<?php echo home_url(); ?>" style="color: #856404; text-decoration: underline;">regresa al inicio</a> 
                y accede desde el enlace correspondiente.
            </p>
        </div>
    <?php else: ?>
        <?php
        // Mostrar información de la iniciativa detectada
        $landing_post = get_post($current_landing_id);
        if ($landing_post && $landing_post->post_type === 'wrs_landing_page'):
        ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
            <h4 style="color: #155724; margin: 0 0 10px 0;">
                ✅ Registro desde: <?php echo esc_html(get_the_title($landing_post)); ?>
            </h4>
            <p style="color: #155724; margin: 0; font-size: 14px;">
                Te estás registrando a través de esta iniciativa. ¡Excelente!
            </p>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="clear:both; margin-bottom: 20px; border-top: 1px solid #eee; padding-top: 20px;">
        <h4><?php esc_html_e( 'Información Personal Adicional', 'woodmart-referral-system' ); ?></h4>
    </div>

    <p class="form-row form-row-first">
        <label for="reg_wrs_nombres"><?php esc_html_e( 'Nombres', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_nombres" id="reg_wrs_nombres" value="<?php echo isset($posted_data['wrs_nombres']) ? esc_attr( wp_unslash( $posted_data['wrs_nombres'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_apellidos"><?php esc_html_e( 'Apellidos', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_apellidos" id="reg_wrs_apellidos" value="<?php echo isset($posted_data['wrs_apellidos']) ? esc_attr( wp_unslash( $posted_data['wrs_apellidos'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_tipo_doc"><?php esc_html_e( 'Tipo Documento', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_tipo_doc" id="reg_wrs_tipo_doc" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona tipo de documento...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('tipo_doc') as $value => $label ) {
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_tipo_doc'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_numero_doc"><?php esc_html_e( 'Número Documento', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_numero_doc" id="reg_wrs_numero_doc" value="<?php echo isset($posted_data['wrs_numero_doc']) ? esc_attr( wp_unslash( $posted_data['wrs_numero_doc'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_wrs_telefono"><?php esc_html_e( 'Número de Teléfono', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="tel" class="input-text" name="wrs_telefono" id="reg_wrs_telefono" value="<?php echo isset($posted_data['wrs_telefono']) ? esc_attr( wp_unslash( $posted_data['wrs_telefono'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_genero"><?php esc_html_e( 'Género', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_genero" id="reg_wrs_genero" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona género...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('genero') as $value => $label ) {
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_genero'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_fecha_nacimiento"><?php esc_html_e( 'Fecha de Nacimiento', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="date" class="input-text" name="wrs_fecha_nacimiento" id="reg_wrs_fecha_nacimiento" value="<?php echo isset($posted_data['wrs_fecha_nacimiento']) ? esc_attr( $posted_data['wrs_fecha_nacimiento'] ) : ''; ?>" placeholder="YYYY-MM-DD" max="<?php echo date('Y-m-d'); ?>" required />
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_departamento_viv"><?php esc_html_e( 'Departamento Vivienda', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_departamento_viv" id="reg_wrs_departamento_viv" class="input-select wrs-departamento-select" required>
            <option value=""><?php esc_html_e('Selecciona un Departamento...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_colombia_geo_data') ) {
                $all_geo_data = wrs_get_colombia_geo_data();
                $departamentos_keys = array_keys($all_geo_data);
                sort($departamentos_keys);
                $current_depto = $posted_data['wrs_departamento_viv'] ?? '';
                foreach ( $departamentos_keys as $dept_key ) {
                    $dept_display_name = isset($all_geo_data[$dept_key]['name']) ? html_entity_decode($all_geo_data[$dept_key]['name']) : $dept_key;
                    echo '<option value="' . esc_attr( $dept_key ) . '" ' . selected( $current_depto, $dept_key, false ) . '>' . esc_html( $dept_display_name ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_municipio_viv"><?php esc_html_e( 'Municipio Vivienda', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_municipio_viv" id="reg_wrs_municipio_viv" class="input-select" disabled required>
            <option value=""><?php esc_html_e('Selecciona departamento primero...','woodmart-referral-system');?></option>
        </select>
    </p>
    <p class="form-row form-row-first wrs-localidad-field" style="display: none;">
        <label for="reg_wrs_localidad"><?php esc_html_e( 'Localidad (Bogotá)', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_localidad" id="reg_wrs_localidad" class="input-select">
            <option value=""><?php esc_html_e('Selecciona localidad...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_bogota_localidades') ) {
                foreach ( wrs_get_bogota_localidades() as $value => $label ) { 
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_localidad'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row <?php echo (isset($posted_data['wrs_departamento_viv']) && $posted_data['wrs_departamento_viv'] === 'BOGOTA_DC') ? 'form-row-last' : 'form-row-wide'; ?>">
        <label for="reg_wrs_barrio"><?php esc_html_e( 'Barrio', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_barrio" id="reg_wrs_barrio" value="<?php echo isset($posted_data['wrs_barrio']) ? esc_attr( wp_unslash( $posted_data['wrs_barrio'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_vereda"><?php esc_html_e( 'Vereda', 'woodmart-referral-system' ); ?></label>
        <input type="text" class="input-text" name="wrs_vereda" id="reg_wrs_vereda" value="<?php echo isset($posted_data['wrs_vereda']) ? esc_attr( wp_unslash( $posted_data['wrs_vereda'] ) ) : ''; ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_direccion"><?php esc_html_e( 'Dirección', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_direccion" id="reg_wrs_direccion" value="<?php echo isset($posted_data['wrs_direccion']) ? esc_attr( wp_unslash( $posted_data['wrs_direccion'] ) ) : ''; ?>" required />
    </p>
    <div style="clear:both; margin-bottom: 20px; border-top: 1px solid #eee; padding-top: 20px;"></div>
    <p class="form-row form-row-first">
        <label for="reg_wrs_estado_civil"><?php esc_html_e( 'Estado Civil', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_estado_civil" id="reg_wrs_estado_civil" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona estado civil...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('estado_civil') as $value => $label ) {
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_estado_civil'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_nivel_educativo"><?php esc_html_e( 'Nivel educativo', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_nivel_educativo" id="reg_wrs_nivel_educativo" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona nivel educativo...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('nivel_educativo') as $value => $label ) {
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_nivel_educativo'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_profesion"><?php esc_html_e( 'Profesión', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="wrs_profesion" id="reg_wrs_profesion" value="<?php echo isset($posted_data['wrs_profesion']) ? esc_attr( wp_unslash( $posted_data['wrs_profesion'] ) ) : ''; ?>" required />
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_ocupacion"><?php esc_html_e( 'Ocupación', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_ocupacion" id="reg_wrs_ocupacion" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona ocupación...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('ocupacion') as $value => $label ) {
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_ocupacion'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-first">
        <label for="reg_wrs_es_cuidador"><?php esc_html_e( '¿Es Cuidador?', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <select name="wrs_es_cuidador" id="reg_wrs_es_cuidador" class="input-select" required>
            <option value=""><?php esc_html_e('Selecciona opción...','woodmart-referral-system');?></option>
            <?php
            if ( function_exists('wrs_get_options') ) {
                foreach ( wrs_get_options('es_cuidador') as $value => $label ) { 
                    echo '<option value="' . esc_attr( $value ) . '" ' . selected( ($posted_data['wrs_es_cuidador'] ?? ''), $value, false ) . '>' . esc_html( $label ) . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p class="form-row form-row-last">
        <label for="reg_wrs_num_hijos"><?php esc_html_e( 'Número de Hijos', 'woodmart-referral-system' ); ?> <span class="required">*</span></label>
        <input type="number" class="input-text" name="wrs_num_hijos" id="reg_wrs_num_hijos" value="<?php echo isset($posted_data['wrs_num_hijos']) ? esc_attr( $posted_data['wrs_num_hijos'] ) : ''; ?>" min="0" style="width: 100px;" required />
    </p>
    <div style="clear:both;"></div>
    <fieldset class="form-row form-row-wide wrs-checkbox-group">
        <legend><?php esc_html_e( 'Temas de Interés', 'woodmart-referral-system' ); ?> <span class="required">*</span></legend>
        <?php
        if ( function_exists('wrs_get_options') ) {
            $temas_interes_options = wrs_get_options('temas_interes');
            $selected_temas = $posted_data['wrs_temas_interes'] ?? array();
            if (!is_array($selected_temas)) { $selected_temas = array(); }
            ?>
            <div style="max-height: 150px; overflow-y: auto; border: 1px solid #eee; padding: 10px; margin-top: 5px;">
                <?php foreach ($temas_interes_options as $value => $label) { ?>
                    <p style="margin-bottom: 5px;">
                        <label>
                            <input type="checkbox" name="wrs_temas_interes[]" value="<?php echo esc_attr($value); ?>" <?php checked( in_array($value, $selected_temas, true) ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </label>
                    </p>
                <?php } ?>
            </div>
            <small style="color: #666; font-style: italic;">Selecciona al menos un tema de interés</small>
            <?php
        }
        ?>
    </fieldset>
    <p class="form-row form-row-wide">
        <label for="reg_wrs_observaciones"><?php esc_html_e( 'Observaciones', 'woodmart-referral-system' ); ?></label>
        <textarea class="input-text" name="wrs_observaciones" id="reg_wrs_observaciones" rows="4"><?php echo isset($posted_data['wrs_observaciones']) ? esc_textarea( wp_unslash( $posted_data['wrs_observaciones'] ) ) : ''; ?></textarea>
        <small style="color: #666; font-style: italic;">Comparte algo sobre ti o tus expectativas (opcional)</small>
    </p>
    <div class="clear"></div>
    <?php
}
/**
 * Valida los campos personalizados obligatorios en el registro.
 * VERSIÓN COMPLETA: Todos los campos obligatorios excepto vereda y observaciones.
 * Añade validación para email existente, mostrando la iniciativa de origen si existe.
 */
function wrs_validate_custom_registration_fields( $errors, $username, $email ) {
    
    // === CAMPOS OBLIGATORIOS BÁSICOS ===
    $required_fields = array(
        'wrs_nombres'          => __( '<strong>Nombres</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_apellidos'        => __( '<strong>Apellidos</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_tipo_doc'         => __( '<strong>Tipo de Documento</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_numero_doc'       => __( '<strong>Número de Documento</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_telefono'         => __( '<strong>Número de Teléfono</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_genero'           => __( '<strong>Género</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_fecha_nacimiento' => __( '<strong>Fecha de Nacimiento</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_departamento_viv' => __( '<strong>Departamento de Vivienda</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_municipio_viv'    => __( '<strong>Municipio de Vivienda</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_barrio'           => __( '<strong>Barrio</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_direccion'        => __( '<strong>Dirección</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_estado_civil'     => __( '<strong>Estado Civil</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_nivel_educativo'  => __( '<strong>Nivel Educativo</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_profesion'        => __( '<strong>Profesión</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_ocupacion'        => __( '<strong>Ocupación</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_es_cuidador'      => __( '<strong>¿Es Cuidador?</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
        'wrs_num_hijos'        => __( '<strong>Número de Hijos</strong> es un campo obligatorio.', 'woodmart-referral-system' ),
    );

    // Validar campos básicos obligatorios
    foreach ( $required_fields as $field_name => $message ) {
        $field_value = isset($_POST[$field_name]) ? trim(wp_unslash($_POST[$field_name])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        
        if ( empty( $field_value ) || $field_value === '' || $field_value === '0' ) {
            $errors->add( $field_name . '_error', $message );
        }
    }

    // === VALIDACIÓN ESPECIAL PARA LOCALIDAD (solo si es Bogotá) ===
    $departamento = isset($_POST['wrs_departamento_viv']) ? sanitize_text_field(wp_unslash($_POST['wrs_departamento_viv'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( $departamento === 'BOGOTA_DC' ) {
        $localidad = isset($_POST['wrs_localidad']) ? trim(wp_unslash($_POST['wrs_localidad'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if ( empty( $localidad ) ) {
            $errors->add( 'wrs_localidad_error', 
                __( '<strong>Localidad</strong> es obligatoria cuando el departamento es Bogotá D.C.', 'woodmart-referral-system' ) 
            );
        }
    }

    // === VALIDACIÓN ESPECIAL PARA TEMAS DE INTERÉS ===
    $temas_interes = isset($_POST['wrs_temas_interes']) ? $_POST['wrs_temas_interes'] : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( !is_array($temas_interes) || empty($temas_interes) ) {
        $errors->add( 'wrs_temas_interes_error', 
            __( '<strong>Temas de Interés</strong>: Debes seleccionar al menos un tema de interés.', 'woodmart-referral-system' ) 
        );
    }

    // === VALIDACIONES ADICIONALES ESPECÍFICAS ===
    
    // Validar formato de fecha de nacimiento
    $fecha_nacimiento = isset($_POST['wrs_fecha_nacimiento']) ? sanitize_text_field($_POST['wrs_fecha_nacimiento']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( !empty($fecha_nacimiento) ) {
        $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
        if ( !$fecha_valida || $fecha_valida->format('Y-m-d') !== $fecha_nacimiento ) {
            $errors->add( 'wrs_fecha_nacimiento_format_error', 
                __( '<strong>Fecha de Nacimiento</strong> debe tener el formato YYYY-MM-DD (ejemplo: 1990-12-25).', 'woodmart-referral-system' ) 
            );
        } else {
            // Validar que no sea una fecha futura
            $hoy = new DateTime();
            if ( $fecha_valida > $hoy ) {
                $errors->add( 'wrs_fecha_nacimiento_future_error', 
                    __( '<strong>Fecha de Nacimiento</strong> no puede ser una fecha futura.', 'woodmart-referral-system' ) 
                );
            }
            
            // Validar edad mínima (ejemplo: 18 años)
            $edad_minima = clone $hoy;
            $edad_minima->sub(new DateInterval('P18Y'));
            if ( $fecha_valida > $edad_minima ) {
                $errors->add( 'wrs_fecha_nacimiento_age_error', 
                    __( '<strong>Fecha de Nacimiento</strong>: Debes ser mayor de 18 años para registrarte.', 'woodmart-referral-system' ) 
                );
            }
        }
    }

    // Validar número de hijos (debe ser número entero no negativo)
    $num_hijos = isset($_POST['wrs_num_hijos']) ? sanitize_text_field($_POST['wrs_num_hijos']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( !empty($num_hijos) ) {
        if ( !is_numeric($num_hijos) || intval($num_hijos) < 0 || intval($num_hijos) != $num_hijos ) {
            $errors->add( 'wrs_num_hijos_format_error', 
                __( '<strong>Número de Hijos</strong> debe ser un número entero igual o mayor a 0.', 'woodmart-referral-system' ) 
            );
        }
    }

    // Validar formato de teléfono (solo números, espacios, guiones y paréntesis)
    $telefono = isset($_POST['wrs_telefono']) ? sanitize_text_field($_POST['wrs_telefono']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( !empty($telefono) ) {
        // Permitir números, espacios, guiones, paréntesis y signo +
        if ( !preg_match('/^[\d\s\-\(\)\+]+$/', $telefono) ) {
            $errors->add( 'wrs_telefono_format_error', 
                __( '<strong>Número de Teléfono</strong> solo puede contener números, espacios, guiones, paréntesis y el signo +.', 'woodmart-referral-system' ) 
            );
        }
        
        // Validar longitud mínima (mínimo 7 dígitos)
        $solo_numeros = preg_replace('/[^\d]/', '', $telefono);
        if ( strlen($solo_numeros) < 7 ) {
            $errors->add( 'wrs_telefono_length_error', 
                __( '<strong>Número de Teléfono</strong> debe tener al menos 7 dígitos.', 'woodmart-referral-system' ) 
            );
        }
    }

    // Validar que el número de documento no tenga caracteres especiales
    $numero_doc = isset($_POST['wrs_numero_doc']) ? sanitize_text_field($_POST['wrs_numero_doc']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
    if ( !empty($numero_doc) ) {
        // Solo permitir números, letras y guiones
        if ( !preg_match('/^[a-zA-Z0-9\-]+$/', $numero_doc) ) {
            $errors->add( 'wrs_numero_doc_format_error', 
                __( '<strong>Número de Documento</strong> solo puede contener números, letras y guiones.', 'woodmart-referral-system' ) 
            );
        }
    }

    // === VALIDACIÓN DE EMAIL EXISTENTE (MEJORADA) ===
    if ( !empty( $email ) && email_exists( $email ) ) {
        // Remover error estándar de WooCommerce si existe
        if ( $errors->get_error_data('registration-error-email-exists') ) {
            $errors->remove('registration-error-email-exists');
        }
        
        $existing_user = get_user_by( 'email', $email );
        if ( $existing_user ) {
            $origin_initiative_id = get_user_meta( $existing_user->ID, '_wrs_origin_landing_id', true );
            $initiative_title = __('una iniciativa desconocida', 'woodmart-referral-system'); 
            
            if ( $origin_initiative_id && is_numeric($origin_initiative_id) ) {
                $initiative_post = get_post( absint($origin_initiative_id) );
                if ( $initiative_post && $initiative_post->post_type === 'wrs_landing_page' ) { 
                    $initiative_title_raw = get_the_title($initiative_post);
                    if (!empty($initiative_title_raw)) {
                        $initiative_title = '"' . esc_html($initiative_title_raw) . '"';
                    }
                } else {
                    $initiative_title = __('una iniciativa que ya no está disponible', 'woodmart-referral-system');
                }
            } elseif (!empty($origin_initiative_id)) { 
                $initiative_title = esc_html($origin_initiative_id);
            }
            
            $custom_message = sprintf(
                __('<strong>Error:</strong> Esta dirección de correo electrónico (%1$s) ya está registrada en nuestra red. Parece que te inscribiste originalmente a través de %2$s. <a href="%3$s">¿Olvidaste tu contraseña?</a>', 'woodmart-referral-system'),
                esc_html($email),
                $initiative_title,
                wp_lostpassword_url()
            );
            $errors->add( 'wrs_email_already_registered_detailed_error', $custom_message );
        } else {
            $errors->add( 'wrs_email_already_registered_error', 
                __('<strong>Error:</strong> Esta dirección de correo electrónico ya está en uso.', 'woodmart-referral-system') 
            );
        }
    }

    // === LOG DE ERRORES PARA DEBUGGING ===
    if ( $errors->get_error_codes() ) {
        $error_codes = $errors->get_error_codes();
        error_log('[WRS Validation] Errores de validación encontrados: ' . implode(', ', $error_codes));
    }

    return $errors;
}
/**
 * Guarda los datos de los campos personalizados (con keys WRS) cuando se crea un cliente.
 * VERSIÓN MEJORADA con detección múltiple robusta de iniciativa origen.
 */
function wrs_save_custom_registration_fields( $customer_id ) {
    error_log('[WRS Save Fields - INICIO MEJORADO] Procesando para customer ID: ' . $customer_id);

    // === GUARDADO DE CAMPOS ESTÁNDAR DEL FORMULARIO ===
    $fields_to_save = array(
        'wrs_nombres'          => 'sanitize_text_field', 'wrs_apellidos'        => 'sanitize_text_field',
        'wrs_tipo_doc'         => 'sanitize_text_field', 'wrs_numero_doc'       => 'sanitize_text_field',
        'wrs_telefono'         => 'sanitize_text_field', 'wrs_genero'           => 'sanitize_text_field',
        'wrs_fecha_nacimiento' => 'sanitize_text_field', 'wrs_departamento_viv' => 'sanitize_text_field',
        'wrs_municipio_viv'    => 'sanitize_text_field', 'wrs_localidad'        => 'sanitize_text_field',
        'wrs_barrio'           => 'sanitize_text_field', 'wrs_vereda'           => 'sanitize_text_field',
        'wrs_direccion'        => 'sanitize_text_field', 'wrs_estado_civil'     => 'sanitize_text_field',
        'wrs_nivel_educativo'  => 'sanitize_text_field', 'wrs_profesion'        => 'sanitize_text_field',
        'wrs_ocupacion'        => 'sanitize_text_field', 'wrs_es_cuidador'      => 'sanitize_text_field',
        'wrs_num_hijos'        => 'absint',              'wrs_observaciones'    => 'sanitize_textarea_field',
    );
    
    foreach ( $fields_to_save as $key => $sanitize_callback ) {
        if ( isset( $_POST[$key] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $value = ($sanitize_callback === 'absint') ? absint( $_POST[$key] ) : call_user_func( $sanitize_callback, wp_unslash( $_POST[$key] ) );
            update_user_meta( $customer_id, $key, $value );
        }
    }
    
    // Temas de interés (array)
    if ( isset( $_POST['wrs_temas_interes'] ) && is_array( $_POST['wrs_temas_interes'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $sanitized_temas = array_map( 'sanitize_text_field', wp_unslash( $_POST['wrs_temas_interes'] ) );
        update_user_meta( $customer_id, 'wrs_temas_interes', $sanitized_temas );
    } else {
        update_user_meta( $customer_id, 'wrs_temas_interes', array() );
    }

    // Actualizar datos principales de WordPress
    $user_data_for_wp_users = array('ID' => $customer_id); 
    $update_core_user_data = false;
    if ( isset( $_POST['wrs_nombres'] ) ) { 
        $user_data_for_wp_users['first_name'] = sanitize_text_field( wp_unslash( $_POST['wrs_nombres'] ) ); 
        $update_core_user_data = true; 
    }
    if ( isset( $_POST['wrs_apellidos'] ) ) { 
        $user_data_for_wp_users['last_name'] = sanitize_text_field( wp_unslash( $_POST['wrs_apellidos'] ) ); 
        $update_core_user_data = true; 
    }
    if ( $update_core_user_data ) { 
        wp_update_user( $user_data_for_wp_users ); 
    }

    // === DETECCIÓN MÚLTIPLE Y ROBUSTA DE INICIATIVA DE ORIGEN ===
    $origin_landing_id = 0;
    $landing_source = 'none';
    $landing_debug = array();
    
    // Método 1: Campo oculto del formulario (MÁS CONFIABLE)
    if ( isset( $_POST['wrs_hidden_landing_id'] ) ) {
        $post_landing = $_POST['wrs_hidden_landing_id'];
        $landing_debug['post_hidden'] = $post_landing;
        if ( !empty($post_landing) && is_numeric($post_landing) ) {
            $origin_landing_id = absint($post_landing);
            $landing_source = 'post_hidden_field';
        }
    }
    
    // Método 2: Sesión WC (respaldo primario)
    if ( !$origin_landing_id && function_exists('WC') && WC()->session ) {
        $session_landing = WC()->session->get('wrs_origin_landing_id_wc');
        $landing_debug['wc_session'] = $session_landing;
        if ( !empty($session_landing) && is_numeric($session_landing) ) {
            $origin_landing_id = absint($session_landing);
            $landing_source = 'wc_session';
        }
    }
    
    // Método 3: Cookie de respaldo
    if ( !$origin_landing_id && isset($_COOKIE['wrs_origin_landing_backup']) ) {
        $cookie_landing = $_COOKIE['wrs_origin_landing_backup'];
        $landing_debug['cookie_backup'] = $cookie_landing;
        if ( !empty($cookie_landing) && is_numeric($cookie_landing) ) {
            $origin_landing_id = absint($cookie_landing);
            $landing_source = 'cookie_backup';
        }
    }
    
    // Método 4: GET parameter (último respaldo)
    if ( !$origin_landing_id && isset($_GET['landing_id']) ) {
        $get_landing = $_GET['landing_id'];
        $landing_debug['get_param'] = $get_landing;
        if ( !empty($get_landing) && is_numeric($get_landing) ) {
            $origin_landing_id = absint($get_landing);
            $landing_source = 'get_parameter';
        }
    }
    
    // Tracking de fuente detectada (para estadísticas)
    $detected_source = isset($_POST['wrs_landing_source']) ? sanitize_text_field($_POST['wrs_landing_source']) : 'unknown';
    update_user_meta( $customer_id, '_wrs_landing_detection_source', $detected_source );
    
    // Log completo de debug
    error_log('[WRS Save Fields - Landing Debug] Customer ' . $customer_id . ': ' . print_r($landing_debug, true));
    error_log('[WRS Save Fields - Landing Final] ID: ' . $origin_landing_id . ', Fuente: ' . $landing_source);
    
    // === VALIDACIÓN Y GUARDADO DE LA INICIATIVA ===
    if ($origin_landing_id > 0) {
        // Verificar que la iniciativa existe realmente
        $landing_post = get_post($origin_landing_id);
        if ($landing_post && $landing_post->post_type === 'wrs_landing_page' && $landing_post->post_status === 'publish') {
            update_user_meta( $customer_id, '_wrs_origin_landing_id', $origin_landing_id );
            update_user_meta( $customer_id, '_wrs_landing_source_method', $landing_source );
            error_log('[WRS Save Fields - Landing SUCCESS] Iniciativa ' . $origin_landing_id . ' guardada para customer ' . $customer_id . ' (fuente: ' . $landing_source . ')');
            
            // Limpiar datos temporales SOLO después de guardar exitosamente
            if ( function_exists('WC') && WC()->session ) {
                WC()->session->set('wrs_origin_landing_id_wc', null);
            }
            if ( isset($_COOKIE['wrs_origin_landing_backup']) ) {
                setcookie('wrs_origin_landing_backup', '', time() - 3600, '/', '', is_ssl(), true);
            }
            
        } else {
            error_log('[WRS Save Fields - Landing ERROR] Iniciativa ' . $origin_landing_id . ' NO VÁLIDA (no existe o no está publicada)');
            update_user_meta( $customer_id, '_wrs_origin_landing_id', 0 ); // Guardar 0 para indicar que no hay iniciativa válida
            update_user_meta( $customer_id, '_wrs_landing_error', 'invalid_landing_post' );
        }
    } else {
        error_log('[WRS Save Fields - Landing WARNING] NO se detectó landing_id válido de ninguna fuente');
        update_user_meta( $customer_id, '_wrs_origin_landing_id', 0 );
        update_user_meta( $customer_id, '_wrs_landing_error', 'no_landing_detected' );
    }

    // === GUARDAR ID DEL REFERENTE DESDE COOKIE ===
    $cookie_name_ref = 'wrs_referrer_id';
    error_log('[WRS Save Fields - Referente] Intentando leer cookie: \'' . $cookie_name_ref . '\' para customer ID: ' . $customer_id);
    
    if ( isset( $_COOKIE[ $cookie_name_ref ] ) ) {
        $referrer_id_from_cookie_raw = $_COOKIE[ $cookie_name_ref ];
        error_log('[WRS Save Fields - Referente] Cookie \'' . $cookie_name_ref . '\' SÍ EXISTE. Valor RAW: "' . $referrer_id_from_cookie_raw . '"');
        
        $referrer_id_from_cookie_sanitized = sanitize_text_field(wp_unslash($referrer_id_from_cookie_raw));
        $referrer_id = absint($referrer_id_from_cookie_sanitized);
        error_log('[WRS Save Fields - Referente] Valor de referente sanitizado y convertido a entero: ' . $referrer_id);

        $referrer_user_data = get_userdata( $referrer_id ); 

        if ( $referrer_id > 0 && $referrer_user_data && $referrer_id != $customer_id ) {
            update_user_meta( $customer_id, '_wrs_referrer_id', $referrer_id );
            error_log('[WRS Save Fields - Referente] User meta _wrs_referrer_id ACTUALIZADO para customer ' . $customer_id . ' con ID de referente: ' . $referrer_id);
            
            // Borrar la cookie después de usarla exitosamente
            $cookie_path = COOKIEPATH ? COOKIEPATH : '/'; 
            $cookie_domain = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
            setcookie( $cookie_name_ref, '', time() - 3600, $cookie_path, $cookie_domain, is_ssl(), true );
            error_log('[WRS Save Fields - Referente] Cookie ' . $cookie_name_ref . ' borrada después de uso exitoso.');
            
        } else {
            error_log('[WRS Save Fields - Referente] Condiciones NO CUMPLIDAS para guardar _wrs_referrer_id.');
            if (!($referrer_id > 0)) error_log('    - Razón: $referrer_id no es > 0 (valor: '.$referrer_id.')');
            if (!$referrer_user_data) error_log('    - Razón: get_userdata('.$referrer_id.') devolvió false (usuario referente no existe).');
            if ($referrer_id == $customer_id) error_log('    - Razón: Intento de auto-referencia (referrer_id == customer_id).');
        }
    } else {
        error_log('[WRS Save Fields - Referente] Cookie \'' . $cookie_name_ref . '\' NO ESTÁ SETEADA en el momento de guardar campos.');
    }

    // === ESTABLECER PERMISOS DE REFERENCIA ===
    update_user_meta( $customer_id, '_wrs_can_refer', 'yes' );
    
    // Timestamp de registro para estadísticas
    update_user_meta( $customer_id, '_wrs_registration_timestamp', time() );
    
    error_log('[WRS Save Fields - FIN MEJORADO] Procesamiento completado para customer ID: ' . $customer_id);
}
/**
 * Previene el auto-login después del registro de WooCommerce.
 */
function wrs_prevent_autologin_after_registration() {
    if ( isset( $_POST['register'] ) && 
         isset( $_POST['woocommerce-register-nonce'] ) && 
         wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['woocommerce-register-nonce'])), 'woocommerce-register' ) 
    ) {
        add_filter('woocommerce_registration_auth_new_customer', '__return_false', 99);
    }
}

/**
 * Acciones finales después de que un nuevo cliente de WooCommerce es creado:
 * 1. Establece una bandera en la sesión para la redirección a página de agradecimiento.
 * VERSIÓN MEJORADA con mejor manejo de datos de sesión.
 */
function wrs_registration_completed_actions($customer_id, $new_customer_data = array(), $password_generated = '') {
    // Verificación del nonce
    if ( !isset( $_POST['register'] ) || 
         !isset( $_POST['woocommerce-register-nonce'] ) ||
         !wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['woocommerce-register-nonce'])), 'woocommerce-register' ) 
    ) {
        return;
    }

    error_log('[WRS Register Completed Actions - MEJORADO] Función ejecutada para customer ID: ' . $customer_id);

    // Obtener datos del usuario
    $user_data = get_userdata($customer_id);
    if (!$user_data) {
        error_log('[WRS Register Completed Actions] ERROR: No se pudo obtener datos del usuario ' . $customer_id);
        return;
    }

    // Obtener nombres personalizados
    $first_name = get_user_meta($customer_id, 'wrs_nombres', true);
    $last_name = get_user_meta($customer_id, 'wrs_apellidos', true);
    $full_name = trim($first_name . ' ' . $last_name);
    
    // Obtener iniciativa usando el sistema robusto
    $origin_landing_id = get_user_meta($customer_id, '_wrs_origin_landing_id', true);
    
    // Establecer la bandera en la sesión de WooCommerce para redirección
    if ( function_exists('WC') && WC()->session ) {
        if ( !WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie(true);
        }
        
        // Datos principales para la página de agradecimiento
        WC()->session->set('wrs_just_registered', $customer_id); 
        WC()->session->set('wrs_redirect_time', time());
        
        // Datos del usuario
        WC()->session->set('wrs_user_name', !empty($full_name) ? $full_name : $user_data->display_name);
        WC()->session->set('wrs_user_login', $user_data->user_login);
        WC()->session->set('wrs_user_email', $user_data->user_email);
        
        // Datos de la iniciativa
        WC()->session->set('wrs_origin_landing_id_thanks', $origin_landing_id);
        
        // Información adicional para debugging
        $landing_source = get_user_meta($customer_id, '_wrs_landing_source_method', true);
        WC()->session->set('wrs_landing_source_debug', $landing_source);
        
        error_log('[WRS Register Completed Actions] Datos de sesión establecidos correctamente para página de agradecimiento');
        error_log('[WRS Register Completed Actions] Usuario: ' . $user_data->user_login . ', Iniciativa: ' . $origin_landing_id . ', Fuente: ' . $landing_source);
    } else {
        error_log('[WRS Register Completed Actions] ERROR: No se pudo acceder a WC()->session');
    }
}
/**
 * Función para mostrar la página de agradecimiento personalizada
 */
function wrs_show_thank_you_page() {
    if ( !function_exists('WC') || !WC()->session || !WC()->session->has_session() ) {
        wp_redirect(home_url());
        exit;
    }
    
    $just_registered = WC()->session->get('wrs_just_registered');
    $redirect_time = WC()->session->get('wrs_redirect_time');
    
    // Verificar que la sesión sea válida y reciente (máximo 5 minutos)
    if ( !$just_registered || !$redirect_time || (time() - $redirect_time > 300) ) {
        wp_redirect(home_url());
        exit;
    }
    
    // Obtener datos del usuario de la sesión
    $user_name = WC()->session->get('wrs_user_name');
    $user_login = WC()->session->get('wrs_user_login');
    $user_email = WC()->session->get('wrs_user_email');
    $origin_landing_id = WC()->session->get('wrs_origin_landing_id_thanks');
    
    // Obtener información de la iniciativa
    $initiative_title = __('nuestra red', 'woodmart-referral-system');
    $initiative_url = home_url();
    
    if ($origin_landing_id && is_numeric($origin_landing_id)) {
        $initiative_post = get_post(absint($origin_landing_id));
        if ($initiative_post && $initiative_post->post_type === 'wrs_landing_page') {
            $initiative_title = get_the_title($initiative_post);
            $initiative_url = get_permalink($initiative_post);
        }
    }
    
    // Generar link de referencia
    $referral_link = add_query_arg('ref', $user_login, $initiative_url);
    
    // Generar link de WhatsApp para compartir
    $whatsapp_message = sprintf(
        'Únete a la red de %s! 🚀 Te invito a formar parte de esta increíble comunidad. Regístrate aquí: %s',
        $initiative_title,
        $referral_link
    );
    $whatsapp_url = 'https://api.whatsapp.com/send?text=' . urlencode($whatsapp_message);
    
    // Limpiar datos de sesión después de obtenerlos
    WC()->session->set('wrs_just_registered', null);
    WC()->session->set('wrs_redirect_time', null);
    WC()->session->set('wrs_user_name', null);
    WC()->session->set('wrs_user_login', null);
    WC()->session->set('wrs_user_email', null);
    WC()->session->set('wrs_origin_landing_id_thanks', null);
    
    // Mostrar página de agradecimiento
    get_header();
    ?>
    <div class="wrs-thank-you-page" style="max-width: 800px; margin: 40px auto; padding: 20px; text-align: center; background: #f9f9f9; border-radius: 10px;">
        <h1 style="color: #2ECC71; font-size: 2.5em; margin-bottom: 20px;">
            ¡Hola <?php echo esc_html($user_name); ?>!
        </h1>
        
        <div style="font-size: 1.2em; line-height: 1.6; margin-bottom: 30px;">
            <p><strong>Gracias por registrarte en <?php echo esc_html($initiative_title); ?></strong></p>
            <p>Te has unido exitosamente a nuestra red y ahora puedes comenzar a referir a más personas.</p>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 15px;">Este es tu link de referencia personal:</h3>
            <div style="background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; font-family: monospace; word-break: break-all;">
                <input type="text" value="<?php echo esc_url($referral_link); ?>" 
                       id="wrs-referral-link" readonly 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 3px; background: white;">
            </div>
            
            <div style="margin: 20px 0;">
                <button onclick="copyReferralLink()" 
                        style="background: #2ECC71; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-size: 1em; margin-right: 10px;">
                    📋 Copiar Link
                </button>
                
                <?php 
                $whatsapp_message = sprintf(
                    __('¡Únete a la red de %s! %s Te invito a formar parte de esta increíble comunidad. Regístrate aquí: %s', 'woodmart-referral-system'),
                    $initiative_title,
                    '🚀',
                    $referral_link
                );
                $whatsapp_url = 'https://api.whatsapp.com/send?text=' . urlencode($whatsapp_message);
                ?>
                
                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank"
                   style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); color: white; text-decoration: none; padding: 12px 25px; border-radius: 5px; display: inline-block; font-size: 1em; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); transition: all 0.3s ease;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(37, 211, 102, 0.4)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(37, 211, 102, 0.3)';">
                    📱 Compartir en WhatsApp
                </a>
            </div>
            
            <p style="margin-top: 15px; color: #666; font-size: 0.9em;">
                Comparte este link con tus contactos para que se unan a través de tu referencia
            </p>
        </div>
        
        <div style="margin: 30px 0;">
            <h4>🚀 ¡Comienza a hacer crecer tu red ahora!</h4>
            <div style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); padding: 20px; border-radius: 10px; margin: 15px 0; color: white;">
                <h5 style="margin: 0 0 10px 0; color: white;">📱 Comparte fácilmente en WhatsApp</h5>
                <p style="margin: 5px 0; font-size: 0.9em;">Una forma rápida de invitar a tus contactos</p>
                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank"
                   style="background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px; border: 2px solid rgba(255,255,255,0.3); font-weight: bold;"
                   onmouseover="this.style.background='rgba(255,255,255,0.3)';"
                   onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                    🚀 Enviar Invitación por WhatsApp
                </a>
            </div>
            
            <h4>¿Qué sigue ahora?</h4>
            <ul style="text-align: left; display: inline-block; margin: 15px 0;">
                <li>✅ Recibirás un correo electrónico con toda esta información</li>
                <li>📧 Revisa tu email para establecer tu contraseña de acceso</li>
                <li>🤝 Comparte tu link de referencia con familiares y amigos</li>
                <li>📊 Accede a tu cuenta para ver tu red de referidos</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" 
               style="background: #3498DB; color: white; text-decoration: none; padding: 15px 30px; border-radius: 5px; display: inline-block; margin: 10px;">
                🏠 Ir a Mi Cuenta
            </a>
            <a href="<?php echo esc_url(home_url()); ?>" 
               style="background: #95A5A6; color: white; text-decoration: none; padding: 15px 30px; border-radius: 5px; display: inline-block; margin: 10px;">
                🏡 Volver al Inicio
            </a>
        </div>
    </div>
    
    <script>
    function copyReferralLink() {
        var linkField = document.getElementById('wrs-referral-link');
        linkField.select();
        linkField.setSelectionRange(0, 99999); // Para móviles
        
        try {
            document.execCommand('copy');
            var button = event.target;
            var originalText = button.innerHTML;
            button.innerHTML = '✅ ¡Copiado!';
            button.style.background = '#27AE60';
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.style.background = '#2ECC71';
            }, 2000);
        } catch (err) {
            alert('Por favor copia el link manualmente');
        }
    }
    </script>
    
    <?php
    get_footer();
    exit;
}

/**
 * ============================================================================
 * FUNCIONES AUXILIARES PARA EL SISTEMA DE RESPALDO DE INICIATIVAS
 * ============================================================================
 * Estas funciones deben añadirse AL INICIO del archivo, antes de las funciones existentes.
 */

/**
 * Establece la iniciativa de origen con múltiples respaldos para garantizar persistencia.
 * Utilizar esta función desde landing pages o cuando se detecte una iniciativa.
 * 
 * @param int $landing_id ID de la landing page/iniciativa
 * @param string $context Contexto desde donde se llama (para debugging)
 * @return bool True si se estableció correctamente
 */
function wrs_set_initiative_with_backup($landing_id, $context = 'unknown') {
    if (!$landing_id || !is_numeric($landing_id) || $landing_id <= 0) {
        error_log('[WRS Set Initiative] ERROR: landing_id inválido: ' . $landing_id . ' (contexto: ' . $context . ')');
        return false;
    }
    
    $landing_id = absint($landing_id);
    
    // Verificar que la iniciativa existe
    $landing_post = get_post($landing_id);
    if (!$landing_post || $landing_post->post_type !== 'wrs_landing_page' || $landing_post->post_status !== 'publish') {
        error_log('[WRS Set Initiative] ERROR: Landing page ' . $landing_id . ' no existe o no está publicada (contexto: ' . $context . ')');
        return false;
    }
    
    $success = false;
    
    // Método 1: Guardar en sesión WC (prioritario)
    if ( function_exists('WC') && WC()->session ) {
        if ( !WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie(true);
        }
        WC()->session->set('wrs_origin_landing_id_wc', $landing_id);
        error_log('[WRS Set Initiative] WC Session establecida: ' . $landing_id . ' (contexto: ' . $context . ')');
        $success = true;
    }
    
    // Método 2: Cookie de respaldo (24 horas de duración)
    $cookie_set = setcookie(
        'wrs_origin_landing_backup', 
        $landing_id, 
        time() + 86400, // 24 horas
        '/', 
        '', 
        is_ssl(), 
        true // HttpOnly
    );
    
    if ($cookie_set) {
        error_log('[WRS Set Initiative] Cookie respaldo establecida: ' . $landing_id . ' (contexto: ' . $context . ')');
        $success = true;
    } else {
        error_log('[WRS Set Initiative] WARNING: No se pudo establecer cookie respaldo (contexto: ' . $context . ')');
    }
    
    // Método 3: Transient como respaldo adicional (para casos extremos)
    $transient_key = 'wrs_landing_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    set_transient($transient_key, $landing_id, 3600); // 1 hora
    error_log('[WRS Set Initiative] Transient respaldo establecido: ' . $transient_key . ' = ' . $landing_id);
    
    return $success;
}

/**
 * Obtiene la iniciativa de origen desde múltiples fuentes de respaldo.
 * Orden de prioridad: POST hidden -> WC Session -> Cookie -> Transient -> GET
 * 
 * @return array ['id' => int, 'source' => string] Información de la iniciativa detectada
 */
function wrs_get_initiative_from_multiple_sources() {
    $result = array('id' => 0, 'source' => 'none');
    
    // Método 1: Campo oculto del POST (más confiable durante el registro)
    if ( isset( $_POST['wrs_hidden_landing_id'] ) ) {
        $post_landing = $_POST['wrs_hidden_landing_id'];
        if ( !empty($post_landing) && is_numeric($post_landing) ) {
            $result['id'] = absint($post_landing);
            $result['source'] = 'post_hidden_field';
            return $result;
        }
    }
    
    // Método 2: Sesión WC
    if ( function_exists('WC') && WC()->session ) {
        $session_landing = WC()->session->get('wrs_origin_landing_id_wc');
        if ( !empty($session_landing) && is_numeric($session_landing) ) {
            $result['id'] = absint($session_landing);
            $result['source'] = 'wc_session';
            return $result;
        }
    }
    
    // Método 3: Cookie de respaldo
    if ( isset($_COOKIE['wrs_origin_landing_backup']) ) {
        $cookie_landing = $_COOKIE['wrs_origin_landing_backup'];
        if ( !empty($cookie_landing) && is_numeric($cookie_landing) ) {
            $result['id'] = absint($cookie_landing);
            $result['source'] = 'cookie_backup';
            return $result;
        }
    }
    
    // Método 4: Transient respaldo
    $transient_key = 'wrs_landing_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    $transient_landing = get_transient($transient_key);
    if ( !empty($transient_landing) && is_numeric($transient_landing) ) {
        $result['id'] = absint($transient_landing);
        $result['source'] = 'transient_backup';
        return $result;
    }
    
    // Método 5: GET parameter (último respaldo)
    if ( isset($_GET['landing_id']) ) {
        $get_landing = $_GET['landing_id'];
        if ( !empty($get_landing) && is_numeric($get_landing) ) {
            $result['id'] = absint($get_landing);
            $result['source'] = 'get_parameter';
            return $result;
        }
    }
    
    return $result;
}


/**
 * ============================================================================
 * CAPTURA AUTOMÁTICA DE INICIATIVA CUANDO SE VISITA UNA LANDING PAGE
 * ============================================================================
 * Agregar estas funciones AL INICIO del archivo, después de las funciones auxiliares
 */

/**
 * Captura automáticamente la iniciativa cuando se visita una landing page.
 * Se ejecuta al cargar cualquier página del tipo 'wrs_landing_page'.
 */
function wrs_auto_capture_landing_page_visit() {
    // Solo ejecutar en frontend y si no es admin
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Verificar si estamos en una landing page
    if ( is_singular('wrs_landing_page') ) {
        global $post;
        
        if ( $post && $post->post_type === 'wrs_landing_page' && $post->post_status === 'publish' ) {
            $landing_id = $post->ID;
            
            // Establecer la iniciativa con el sistema de respaldo
            $success = wrs_set_initiative_with_backup( $landing_id, 'landing_page_visit' );
            
            if ( $success ) {
                error_log('[WRS Auto Capture] Iniciativa capturada automáticamente: ID ' . $landing_id . ' (' . get_the_title($landing_id) . ')');
            } else {
                error_log('[WRS Auto Capture] ERROR: No se pudo capturar iniciativa ID ' . $landing_id);
            }
        }
    }
    
    // También capturar si viene por URL con parámetro landing_id
    if ( isset($_GET['landing_id']) && is_numeric($_GET['landing_id']) ) {
        $landing_id = absint($_GET['landing_id']);
        
        if ( $landing_id > 0 ) {
            $landing_post = get_post($landing_id);
            if ( $landing_post && $landing_post->post_type === 'wrs_landing_page' && $landing_post->post_status === 'publish' ) {
                wrs_set_initiative_with_backup( $landing_id, 'url_parameter' );
                error_log('[WRS Auto Capture] Iniciativa capturada por parámetro URL: ID ' . $landing_id);
            }
        }
    }
}

/**
 * Captura la iniciativa cuando se visita cualquier página con datos en la URL.
 * Útil para casos donde el tracking viene por enlaces externos.
 */
function wrs_capture_initiative_from_url() {
    // Solo ejecutar en frontend
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }
    
    // Si ya tenemos una iniciativa capturada, no sobrescribir
    $current_initiative = wrs_get_initiative_from_multiple_sources();
    if ( $current_initiative['id'] > 0 ) {
        return; // Ya hay iniciativa, no sobrescribir
    }
    
    // Buscar en la URL actual si hay referencia a una landing page
    $current_url = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) );
    
    // Detectar si estamos en una URL de iniciativa (/iniciativa/nombre-iniciativa/)
    if ( preg_match('/\/iniciativa\/([^\/\?]+)/', $_SERVER['REQUEST_URI'], $matches) ) {
        $landing_slug = sanitize_title($matches[1]);
        
        // Buscar la landing page por slug
        $landing_post = get_page_by_path($landing_slug, OBJECT, 'wrs_landing_page');
        
        if ( $landing_post && $landing_post->post_status === 'publish' ) {
            wrs_set_initiative_with_backup( $landing_post->ID, 'url_slug_detection' );
            error_log('[WRS URL Capture] Iniciativa detectada por slug: ' . $landing_slug . ' (ID: ' . $landing_post->ID . ')');
        } else {
            error_log('[WRS URL Capture] WARNING: Slug de iniciativa no encontrado: ' . $landing_slug);
        }
    }
}

/**
 * Hook especial para when el usuario hace clic en "Únete" desde una landing page.
 * Captura la iniciativa antes de redirigir al registro.
 */
function wrs_capture_before_registration_redirect() {
    // Solo si estamos yendo hacia el registro
    if ( !isset($_GET['action']) || $_GET['action'] !== 'register' ) {
        return;
    }
    
    // Verificar si tenemos referrer de una landing page
    $referrer = wp_get_referer();
    if ( $referrer ) {
        // Extraer el slug de la landing page del referrer
        if ( preg_match('/\/iniciativa\/([^\/\?]+)/', $referrer, $matches) ) {
            $landing_slug = sanitize_title($matches[1]);
            
            $landing_post = get_page_by_path($landing_slug, OBJECT, 'wrs_landing_page');
            
            if ( $landing_post && $landing_post->post_status === 'publish' ) {
                wrs_set_initiative_with_backup( $landing_post->ID, 'registration_redirect' );
                error_log('[WRS Registration Redirect] Iniciativa capturada desde referrer: ' . $landing_slug . ' (ID: ' . $landing_post->ID . ')');
            }
        }
    }
}

/**
 * Función de debug para mostrar el estado actual de captura.
 * Útil para troubleshooting.
 */
function wrs_debug_current_capture_state() {
    if ( !current_user_can('manage_options') ) {
        return;
    }
    
    $current_initiative = wrs_get_initiative_from_multiple_sources();
    $current_url = $_SERVER['REQUEST_URI'];
    $referrer = wp_get_referer();
    
    $debug_info = array(
        'current_url' => $current_url,
        'referrer' => $referrer,
        'initiative_detected' => $current_initiative,
        'is_landing_page' => is_singular('wrs_landing_page'),
        'is_registration' => (isset($_GET['action']) && $_GET['action'] === 'register'),
        'post_id' => get_the_ID(),
        'post_type' => get_post_type(),
    );
    
    error_log('[WRS Debug Capture State] ' . print_r($debug_info, true));
}

// Hooks para activar la captura automática
add_action( 'wp', 'wrs_auto_capture_landing_page_visit', 5 );
add_action( 'wp', 'wrs_capture_initiative_from_url', 6 );
add_action( 'template_redirect', 'wrs_capture_before_registration_redirect', 5 );

// Hook de debug (solo para admins)
add_action( 'wp_footer', 'wrs_debug_current_capture_state', 999 );
/**
 * Limpia todos los datos temporales de iniciativa después de uso exitoso.
 * 
 * @param string $context Contexto desde donde se llama
 */
function wrs_cleanup_initiative_temp_data($context = 'unknown') {
    // Limpiar sesión WC
    if ( function_exists('WC') && WC()->session ) {
        WC()->session->set('wrs_origin_landing_id_wc', null);
    }
    
    // Limpiar cookie
    if ( isset($_COOKIE['wrs_origin_landing_backup']) ) {
        setcookie('wrs_origin_landing_backup', '', time() - 3600, '/', '', is_ssl(), true);
    }
    
    // Limpiar transient
    $transient_key = 'wrs_landing_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    delete_transient($transient_key);
    
    error_log('[WRS Cleanup] Datos temporales limpiados (contexto: ' . $context . ')');
}

/**
 * Redirección forzosa a página de agradecimiento
 */
function wrs_force_redirect_to_thank_you_page() {
    // No hacer nada en admin
    if ( is_admin() || ! function_exists('WC') || ! WC()->session || ! WC()->session->has_session() ) {
        return; 
    }

    $just_registered_customer_id = WC()->session->get('wrs_just_registered');
    $redirect_time = WC()->session->get('wrs_redirect_time');

    // Solo actuar si la bandera está presente y no ha pasado mucho tiempo
    if ( $just_registered_customer_id && $redirect_time && (time() - $redirect_time < 30) ) {
        error_log('[WRS Force Redirect] Mostrando página de agradecimiento para customer ID: ' . $just_registered_customer_id);
        wrs_show_thank_you_page();
    }
}

/**
 * Envía un correo de bienvenida personalizado con link de referencia y establecimiento de contraseña.
 */
function wrs_send_custom_welcome_email( $customer_id, $new_customer_data ) {
    $user = get_userdata( $customer_id );
    if ( ! $user ) {
        return;
    }

    $user_email = $user->user_email;
    $user_login = $user->user_login;
    
    // Obtener nombres
    $first_name_wrs = get_user_meta( $customer_id, 'wrs_nombres', true );
    $last_name_wrs = get_user_meta( $customer_id, 'wrs_apellidos', true );
    $user_display_name = trim($first_name_wrs . ' ' . $last_name_wrs);
    
    if (empty($user_display_name)) {
        $user_display_name = $user->display_name;
    }

    // Obtener iniciativa
    $origin_landing_id = get_user_meta( $customer_id, '_wrs_origin_landing_id', true );
    $initiative_title = get_bloginfo( 'name' );
    $initiative_url = home_url();
    
    if ($origin_landing_id && is_numeric($origin_landing_id)) {
        $initiative_post = get_post(absint($origin_landing_id));
        if ($initiative_post && $initiative_post->post_type === 'wrs_landing_page') {
            $initiative_title = get_the_title($initiative_post);
            $initiative_url = get_permalink($initiative_post);
        }
    }
    
    // Link de referencia
    $referral_link = add_query_arg('ref', $user_login, $initiative_url);
    
    // Link de WhatsApp
    $whatsapp_message = '¡Únete a la red de ' . $initiative_title . '! 🚀 Te invito a formar parte de esta increíble comunidad. Regístrate aquí: ' . $referral_link;
    $whatsapp_url = 'https://api.whatsapp.com/send?text=' . urlencode($whatsapp_message);
    
    // Contraseña
    $key = get_password_reset_key( $user );
    if ( ! is_wp_error( $key ) ) {
        $reset_password_url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
    } else {
        $reset_password_url = wp_login_url();
    }
    
    $site_name = get_bloginfo( 'name' );
    $account_link = wc_get_page_permalink( 'myaccount' );

    $subject = '¡Bienvenido/a a ' . $initiative_title . ', ' . $user_display_name . '! 🎉';

    // MENSAJE HTML CON ESTILOS
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido a ' . esc_html($initiative_title) . '</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f7fa;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f7fa;">
            <tr>
                <td align="center" style="padding: 20px 0;">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                        
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%); padding: 40px 30px; text-align: center;">
                                <h1 style="color: white; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                                    ¡Hola ' . esc_html($user_display_name) . '! 🌟
                                </h1>
                                <p style="color: white; margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">
                                    Bienvenido/a a ' . esc_html($initiative_title) . '
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Welcome Message -->
                        <tr>
                            <td style="padding: 30px; text-align: center;">
                                <p style="font-size: 18px; color: #2c3e50; margin: 0 0 20px 0; line-height: 1.6;">
                                    ¡Gracias por registrarte! Tu cuenta ha sido creada exitosamente y ahora eres parte de nuestra red.
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Referral Link Section -->
                        <tr>
                            <td style="padding: 0 30px 20px 30px;">
                                <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; border: 2px solid #e8f5e8;">
                                    <h3 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 20px; text-align: center;">
                                        🎯 Tu Link de Referencia Personal
                                    </h3>
                                    <div style="background: white; padding: 15px; border-radius: 8px; border: 2px dashed #27AE60; margin: 15px 0;">
                                        <p style="margin: 0; font-family: monospace; font-size: 14px; color: #2c3e50; word-break: break-all; text-align: center;">
                                            ' . esc_html($referral_link) . '
                                        </p>
                                    </div>
                                    <p style="margin: 10px 0 0 0; color: #7f8c8d; font-size: 14px; text-align: center;">
                                        Comparte este link para que otros se unan a través de tu referencia
                                    </p>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- WhatsApp Section -->
                        <tr>
                            <td style="padding: 0 30px 30px 30px;">
                                <div style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-radius: 12px; padding: 25px; text-align: center; box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);">
                                    <h3 style="color: white; margin: 0 0 10px 0; font-size: 22px;">
                                        📱 Comparte Fácilmente en WhatsApp
                                    </h3>
                                    <p style="color: white; margin: 0 0 20px 0; font-size: 16px; opacity: 0.9;">
                                        Haz clic en el botón para compartir tu invitación automáticamente
                                    </p>
                                    <a href="' . esc_url($whatsapp_url) . '" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 16px; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s ease;">
                                        🚀 Enviar por WhatsApp
                                    </a>
                                    <p style="color: white; margin: 15px 0 0 0; font-size: 14px; opacity: 0.8;">
                                        El mensaje se generará automáticamente con tu link incluido
                                    </p>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Password Section -->
                        <tr>
                            <td style="padding: 0 30px 20px 30px;">
                                <div style="background: #fff3cd; border-radius: 12px; padding: 25px; border-left: 5px solid #ffc107;">
                                    <h3 style="color: #856404; margin: 0 0 15px 0; font-size: 18px;">
                                        🔐 Establece tu Contraseña
                                    </h3>
                                    <p style="color: #856404; margin: 0 0 15px 0; font-size: 14px;">
                                        Para acceder a tu cuenta, primero debes crear tu contraseña:
                                    </p>
                                    <a href="' . esc_url($reset_password_url) . '" style="display: inline-block; background: #ffc107; color: #856404; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; margin: 5px 0;">
                                        Crear Mi Contraseña
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Access Data -->
                        <tr>
                            <td style="padding: 0 30px 20px 30px;">
                                <div style="background: #e7f3ff; border-radius: 12px; padding: 20px; border-left: 5px solid #007bff;">
                                    <h4 style="color: #004085; margin: 0 0 15px 0; font-size: 16px;">📊 Tus Datos de Acceso</h4>
                                    <table style="width: 100%; font-size: 14px; color: #004085;">
                                        <tr>
                                            <td style="padding: 5px 0;"><strong>Usuario:</strong></td>
                                            <td style="padding: 5px 0;">' . esc_html($user_login) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;"><strong>Email:</strong></td>
                                            <td style="padding: 5px 0;">' . esc_html($user_email) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;"><strong>Contraseña:</strong></td>
                                            <td style="padding: 5px 0;">Créala con el botón de arriba</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Next Steps -->
                        <tr>
                            <td style="padding: 0 30px 30px 30px;">
                                <div style="background: #f8f9fa; border-radius: 12px; padding: 25px;">
                                    <h4 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; text-align: center;">
                                        🚀 Próximos Pasos
                                    </h4>
                                    <table style="width: 100%; font-size: 14px; color: #2c3e50;">
                                        <tr>
                                            <td style="padding: 8px; width: 30px;">✅</td>
                                            <td style="padding: 8px;">Crea tu contraseña usando el botón amarillo</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px;">📱</td>
                                            <td style="padding: 8px;">Usa el botón verde de WhatsApp para invitar contactos</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px;">👥</td>
                                            <td style="padding: 8px;">Ve tu red crecer en tiempo real</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px;">📈</td>
                                            <td style="padding: 8px;">Accede a estadísticas en tu cuenta</td>
                                        </tr>
                                    </table>
                                    
                                    <div style="text-align: center; margin-top: 20px;">
                                        <a href="' . esc_url($account_link) . '" style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold;">
                                            Ir a Mi Cuenta
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style="background: #2c3e50; padding: 20px 30px; text-align: center;">
                                <p style="color: white; margin: 0; font-size: 16px;">
                                    ¡Esperamos ver crecer tu red! 💪
                                </p>
                                <p style="color: #bdc3c7; margin: 10px 0 0 0; font-size: 14px;">
                                    Saludos cordiales, <strong>El equipo de ' . esc_html($site_name) . '</strong>
                                </p>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>'
    );

    if ( wp_mail( $user_email, $subject, $message, $headers ) ) {
        error_log("[WRS Registration Email] Correo HTML enviado exitosamente a {$user_email} (Usuario ID: {$customer_id}).");
    } else {
        error_log("[WRS Registration Email] ERROR al enviar correo HTML a {$user_email} (Usuario ID: {$customer_id}).");
    }
}
?>