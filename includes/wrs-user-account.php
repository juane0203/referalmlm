<?php
/**
 * Funcionalidad para la Sección "Mi Cuenta" de WooCommerce del Plugin WRS.
 * - Nuevos ítems de menú.
 * - Nuevos endpoints.
 * - Contenido de los endpoints: Links de Referencia, Mi Red, Mi Perfil.
 * - Manejo de la actualización del perfil desde el frontend usando hooks de WooCommerce.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Añade nuevos items al menú de Mi Cuenta de WooCommerce.
 */
function wrs_add_account_menu_items( $items ) {
    $new_items_after = 'orders'; // Intentar poner después de 'Pedidos'
    if (!isset($items[$new_items_after])) {
        $new_items_after = 'dashboard'; // Fallback después de 'Escritorio'
    }
    if (!isset($items[$new_items_after])) { // Si sigue sin encontrar, poner antes de logout
        $new_items_after = 'customer-logout'; 
    }
    
    $offset = 0;
    if (is_array($items) && !empty($items)) {
        $keys = array_keys($items);
        $search = array_search($new_items_after, $keys);
        if ($search !== false) {
            $offset = $search + 1; // Para poner después del elemento encontrado
        } else { 
            $offset = count($items) > 0 ? count($items) -1 : 0; // Poner antes del último
        }
    } else {
        $items = array(); 
    }

    $new_items = array(
        'my-profile'     => __( 'Mi Perfil WRS', 'woodmart-referral-system' ),
        'referral-links' => __( 'Links de Referencia', 'woodmart-referral-system' ),
        'genealogy-tree' => __( 'Mi Red', 'woodmart-referral-system' )
    );

    $start = array_slice( $items, 0, $offset, true );
    $end = array_slice( $items, $offset, null, true );
    $items = $start + $new_items + $end;
    
    return $items;
}

/**
 * Registra los nuevos endpoints para Mi Cuenta de WooCommerce.
 */
function wrs_add_account_endpoints() {
    add_rewrite_endpoint( 'referral-links', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'genealogy-tree', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'my-profile', EP_ROOT | EP_PAGES );
}

/**
 * Muestra el contenido para el endpoint 'referral-links'.
 */
function wrs_referral_links_endpoint_content() {
    // (Asegúrate de que este código sea el que te funcionaba para los links de referido)
    echo '<h2>' . esc_html__( 'Mis Links de Referencia', 'woodmart-referral-system' ) . '</h2>';
    if ( ! is_user_logged_in() ) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Debes iniciar sesión para ver tus links de referencia.', 'woodmart-referral-system' ), 'error' ); } return; }
    $user_id = get_current_user_id(); $current_user_info = get_userdata($user_id);
    if (!$current_user_info) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Error al obtener la información del usuario.', 'woodmart-referral-system' ), 'error' ); } return; }
    $user_login = $current_user_info->user_login; $can_refer = get_user_meta( $user_id, '_wrs_can_refer', true ); $is_admin = current_user_can( 'manage_options' );
    if ( ! $is_admin && $can_refer !== 'yes' ) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Actualmente no tienes permiso para generar links de referencia.', 'woodmart-referral-system' ), 'notice' ); } return; }
    $plugin_version = defined('WRS_PLUGIN_VERSION') ? WRS_PLUGIN_VERSION : 'FS.1.0'; 
    if ( $is_admin ) {
        echo '<p>' . esc_html__( 'Como administrador, puedes generar links para todas las iniciativas activas:', 'woodmart-referral-system' ) . '</p>';
        $args = array('post_type'=> 'wrs_landing_page','post_status'=> 'publish','posts_per_page' => -1,'orderby'=> 'title','order'=> 'ASC');
        $all_initiatives_query = new WP_Query( $args );
        if ( $all_initiatives_query->have_posts() ) { 
            echo '<ul class="wrs-referral-links-list">';
            while ( $all_initiatives_query->have_posts() ) : $all_initiatives_query->the_post();
                $initiative_title = get_the_title(); $initiative_permalink = get_permalink();
                $referral_link = add_query_arg( 'ref', $user_login, $initiative_permalink ); $field_id = 'wrs_ref_link_' . get_the_ID();
                echo '<li class="wrs-referral-link-item"><strong>' . esc_html( $initiative_title ) . '</strong><div class="wrs-copy-wrapper"><input type="text" id="' . esc_attr($field_id) . '" readonly value="' . esc_url( $referral_link ) . '" class="wrs-referral-input" onclick="this.select();" /><button type="button" class="button wrs-copy-button" data-clipboard-target="#' . esc_attr($field_id) . '">' . esc_html__('Copiar','woodmart-referral-system') . '</button></div></li>';
            endwhile;
            echo '</ul>'; wp_reset_postdata();
            if (!wp_script_is('clipboard')) { wp_enqueue_script('clipboard', 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js', array('jquery'), $plugin_version, true); wp_add_inline_script('clipboard', 'jQuery(document).ready(function($){ if(typeof ClipboardJS !== "undefined"){ var clipboard = new ClipboardJS(".wrs-copy-button"); clipboard.on("success", function(e) { var originalText = $(e.trigger).text(); $(e.trigger).text("'.esc_js(__('Copiado!','woodmart-referral-system')).'"); setTimeout(function(){ $(e.trigger).text(originalText); }, 1500); e.clearSelection(); }); } });');}
        } else { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'No hay iniciativas publicadas actualmente.', 'woodmart-referral-system' ), 'info' ); }}
    } else {
        $origin_initiative_id = absint(get_user_meta( $user_id, '_wrs_origin_landing_id', true ));
        if ( $origin_initiative_id > 0 ) { $initiative_post = get_post( $origin_initiative_id );
            if ( $initiative_post && $initiative_post->post_status === 'publish' && $initiative_post->post_type === 'wrs_landing_page' ) { 
                $initiative_title = get_the_title( $initiative_post ); $initiative_permalink = get_permalink( $initiative_post );
                $referral_link = add_query_arg( 'ref', $user_login, $initiative_permalink ); $field_id = 'wrs_referral_link_field_user';
                echo '<p>' . esc_html__( 'Comparte el siguiente link para referir nuevos usuarios a través de la iniciativa por la que ingresaste:', 'woodmart-referral-system' ) . '</p>';
                echo '<div class="wrs-referral-link-item"><strong>' . esc_html( $initiative_title ) . '</strong><div class="wrs-copy-wrapper"><input type="text" id="' . esc_attr($field_id) . '" readonly value="' . esc_url( $referral_link ) . '" class="wrs-referral-input" onclick="this.select();" /><button type="button" class="button wrs-copy-button" data-clipboard-target="#' . esc_attr($field_id) . '">' . esc_html__('Copiar','woodmart-referral-system') . '</button></div></div>';
                if (!wp_script_is('clipboard')) { wp_enqueue_script('clipboard', 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js', array('jquery'), $plugin_version, true); wp_add_inline_script('clipboard', 'jQuery(document).ready(function($){ if(typeof ClipboardJS !== "undefined"){ var clipboard = new ClipboardJS(".wrs-copy-button"); clipboard.on("success", function(e) { var originalText = $(e.trigger).text(); $(e.trigger).text("'.esc_js(__('Copiado!','woodmart-referral-system')).'"); setTimeout(function(){ $(e.trigger).text(originalText); }, 1500); e.clearSelection(); }); } });');}
            } else { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'La iniciativa original ya no está disponible o no es válida.', 'woodmart-referral-system' ), 'warning' );}}
        } else { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Tu registro no está asociado a una iniciativa específica de origen.', 'woodmart-referral-system' ), 'notice' ); }}
    }
}

/**
 * Muestra el contenido para el endpoint 'genealogy-tree'.
 * (Esta es la versión que encola Cytoscape directamente y que te funcionó para el frontend)
 */
function wrs_genealogy_tree_endpoint_content() {
    // --- PEGA AQUÍ TU CÓDIGO COMPLETO Y FUNCIONAL de wrs_genealogy_tree_endpoint_content QUE TE FUNCIONÓ PERFECTO ---
    // Esta es la función que encola directamente cytoscape-lib, popper-js-core, cytoscape-popper-js,
    // y tu wrs-cytoscape-custom-chart.js, y luego localiza wrsChartData.
    // (La versión de la respuesta #46 o la que me pasaste en #50 que confirmaste que funcionaba)
    echo '<h2>' . esc_html__( 'Mi Red de Referidos', 'woodmart-referral-system' ) . '</h2>';
    if ( ! is_user_logged_in() ) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Debes iniciar sesión para ver tu red.', 'woodmart-referral-system' ), 'error' ); } return; }
    $user_id = get_current_user_id(); $current_user_info = get_userdata($user_id);
    if (!$current_user_info) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Error al obtener datos del usuario.', 'woodmart-referral-system' ), 'error' ); } return; }
    if (!function_exists('wrs_get_user_genealogy_data') || !function_exists('wrs_format_data_for_cytoscape')) { if (function_exists('wc_print_notice')) { wc_print_notice( esc_html__( 'Error: Funciones para generar el árbol no disponibles.', 'woodmart-referral-system' ), 'error' ); } error_log('[WRS Mi Red V.FUNCIONAL-FRONTEND] Error Crítico: Funciones de lógica de referido no definidas.'); return; }
    $first_name = get_user_meta( $user_id, 'wrs_nombres', true ); $last_name = get_user_meta( $user_id, 'wrs_apellidos', true );
    $full_name = trim( $first_name . ' ' . $last_name ); $display_name_final = !empty($full_name) ? $full_name : $current_user_info->display_name;
    $direct_referrals_query_root = new WP_User_Query(array('meta_key' => '_wrs_referrer_id', 'meta_value' => $user_id, 'count_total' => true, 'fields' => 'ID'));
    $root_direct_referrals_count = $direct_referrals_query_root->get_total();
    $root_node_data = array('id' => $user_id, 'display_name' => $display_name_final, 'email' => $current_user_info->user_email, 'direct_referrals_count' => $root_direct_referrals_count);
    $genealogy_children_data = wrs_get_user_genealogy_data( $user_id ); 
    $cytoscape_elements = wrs_format_data_for_cytoscape( $root_node_data, $genealogy_children_data, true );
    error_log('[WRS Mi Red V.FUNCIONAL-FRONTEND - User Data (Endpoint)]: ' . count($cytoscape_elements) . ' elementos.');
    if ( !empty( $cytoscape_elements ) ) { 
        echo '<p>' . esc_html__( 'Aquí está tu red de usuarios referidos:', 'woodmart-referral-system' ) . '</p>';
        echo '<div id="wrs_cytoscape_chart_user_div" class="wrs-genealogy-chart-container" style="height: 600px; width: 100%; border: 1px solid #00FF00;"></div>'; 
        $plugin_dir_url = defined('WRS_PLUGIN_DIR_URL') ? WRS_PLUGIN_DIR_URL : plugin_dir_url( dirname( __FILE__, 2 ) ) . 'woodmart-referral-systemV3/';
        $plugin_version = defined('WRS_PLUGIN_VERSION') ? WRS_PLUGIN_VERSION : 'FS.1.0'; 
        $cytoscape_custom_js_url = $plugin_dir_url . 'assets/js/wrs-cytoscape-chart.js';
        $chart_styles_url = $plugin_dir_url . 'assets/css/wrs-frontend-styles.css';
        wp_enqueue_style('wrs-chart-styles-user-view', $chart_styles_url, array(), $plugin_version);
        wp_print_script_tag( array( 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.28.1/cytoscape.min.js', 'id' => 'cytoscape-lib-direct' ) );
        wp_print_script_tag( array( 'src' => 'https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js', 'id' => 'popper-js-core-direct' ) );
        wp_print_script_tag( array( 'src' => 'https://unpkg.com/cytoscape-popper@2.0.0/cytoscape-popper.js', 'id' => 'cytoscape-popper-js-direct' ) );
        wp_print_script_tag( array( 'src' => includes_url( '/js/jquery/jquery.min.js' ), 'id' => 'jquery-core-direct' ) );
        echo "<script type='text/javascript' id='wrs-chart-data-inline-script'>";
        echo "/* <![CDATA[ */\n";
        echo "var wrsChartData = " . wp_json_encode(array( 'elements' => $cytoscape_elements, 'chartDivId' => 'wrs_cytoscape_chart_user_div', 'layoutName' => 'cose', 'isUserView' => true, 'rootNodeIds' => array('#user_' . $user_id) )) . ";\n";
        echo "/* ]]> */";
        echo "</script>\n";
        wp_print_script_tag( array( 'src' => esc_url($cytoscape_custom_js_url) . '?ver=' . $plugin_version, 'id' => 'wrs-cytoscape-custom-chart-direct' ) );
        error_log('[WRS Mi Red V.FUNCIONAL-FRONTEND - Endpoint] Scripts impresos directamente y datos localizados.');
    } else { echo '<p>' . esc_html__( 'Aún no tienes usuarios referidos o no hay datos para el gráfico.', 'woodmart-referral-system' ) . '</p>'; }
}

/**
 * Muestra el contenido para el endpoint 'my-profile' (formulario de edición).
 * Se integra con los hooks de guardado de WooCommerce.
 */
function wrs_my_profile_endpoint_content() {
    if ( ! is_user_logged_in() ) {
        if (function_exists('wc_print_notice')) {
            wc_print_notice( __( 'Debes iniciar sesión para editar tu perfil.', 'woodmart-referral-system' ), 'error' );
        }
        return;
    }
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    if (!$user) { 
        if (function_exists('wc_print_notice')) {
            wc_print_notice( __( 'No se pudo obtener la información del usuario.', 'woodmart-referral-system' ), 'error' );
        }
        return; 
    }

    echo '<h2>' . esc_html__( 'Mi Perfil WRS', 'woodmart-referral-system' ) . '</h2>';
    
    if (function_exists('wc_print_notices')) {
        wc_print_notices(); 
    }
    ?>
    <form class="woocommerce-EditAccountForm edit-account wrs-profile-edit-form" action="" method="post">

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <fieldset>
            <legend><?php esc_html_e( 'Información Básica', 'woodmart-referral-system' ); ?></legend>
            <p class="form-row form-row-first">
                <label for="account_first_name"><?php esc_html_e( 'Nombres', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
            </p>
            <p class="form-row form-row-last">
                <label for="account_last_name"><?php esc_html_e( 'Apellidos', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
            </p>
            <div class="clear"></div>
            <p class="form-row form-row-wide">
                <label for="account_display_name"><?php esc_html_e( 'Nombre visible', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
                <span><em><?php esc_html_e( 'Así se mostrará tu nombre en la sección de cuenta y en las valoraciones.', 'woocommerce' ); ?></em></span>
            </p>
            <div class="clear"></div>
            <p class="form-row form-row-wide">
                <label for="account_email"><?php esc_html_e( 'Correo electrónico', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
            </p>
        </fieldset>
        
        <fieldset style="margin-top: 30px; padding-top: 10px;">
            <legend><?php esc_html_e( 'Datos', 'woodmart-referral-system' ); ?></legend>
            
            <?php
            $wrs_profile_fields = array(
                'wrs_tipo_doc' => array('label' => __('Tipo Documento', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'tipo_doc', 'required' => true, 'class' => 'form-row-first'),
                'wrs_numero_doc' => array('label' => __('Número Documento', 'woodmart-referral-system'), 'type' => 'text', 'required' => true, 'class' => 'form-row-last'),
                'wrs_telefono' => array('label' => __('Número de Teléfono', 'woodmart-referral-system'), 'type' => 'tel', 'required' => true, 'class' => 'form-row-first'),
                'wrs_genero' => array('label' => __('Género', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'genero', 'class' => 'form-row-last'),
                'wrs_fecha_nacimiento' => array('label' => __('Fecha de Nacimiento', 'woodmart-referral-system'), 'type' => 'date', 'class' => 'form-row-first'),
                'wrs_estado_civil' => array('label' => __('Estado Civil', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'estado_civil', 'class' => 'form-row-last'),
                'wrs_departamento_viv' => array('label' => __('Departamento Vivienda', 'woodmart-referral-system'), 'type' => 'select_dep', 'id_attr' => 'reg_wrs_departamento_viv', 'class' => 'form-row-first'),
                'wrs_municipio_viv' => array('label' => __('Municipio Vivienda', 'woodmart-referral-system'), 'type' => 'select_mun', 'id_attr' => 'reg_wrs_municipio_viv', 'class' => 'form-row-last'),
                'wrs_localidad' => array('label' => __('Localidad (Bogotá)', 'woodmart-referral-system'), 'type' => 'select_loc', 'id_attr' => 'reg_wrs_localidad', 'class_wrapper' => 'form-row-first wrs-localidad-field', 'class_p_extra' => 'wrs-localidad-field'),
                'wrs_barrio' => array('label' => __('Barrio', 'woodmart-referral-system'), 'type' => 'text', 'id_attr' => 'reg_wrs_barrio', 'class' => 'form-row-last'), 
                'wrs_vereda' => array('label' => __('Vereda', 'woodmart-referral-system'), 'type' => 'text', 'class' => 'form-row-first'),
                'wrs_direccion' => array('label' => __('Dirección', 'woodmart-referral-system'), 'type' => 'text', 'class' => 'form-row-last'),
                'wrs_nivel_educativo' => array('label' => __('Nivel educativo', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'nivel_educativo', 'class' => 'form-row-first'),
                'wrs_profesion' => array('label' => __('Profesión', 'woodmart-referral-system'), 'type' => 'text', 'class' => 'form-row-last'),
                'wrs_ocupacion' => array('label' => __('Ocupación', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'ocupacion', 'class' => 'form-row-first'),
                'wrs_es_cuidador' => array('label' => __('¿Es Cuidador?', 'woodmart-referral-system'), 'type' => 'select', 'options_key' => 'es_cuidador', 'class' => 'form-row-last'),
                'wrs_num_hijos' => array('label' => __('Número de Hijos', 'woodmart-referral-system'), 'type' => 'number', 'class' => 'form-row-wide', 'extra_attrs' => 'min="0" style="width:120px;"'),
                'wrs_temas_interes' => array('label' => __('Temas de Interés', 'woodmart-referral-system'), 'type' => 'checkbox_group', 'options_key' => 'temas_interes', 'class' => 'form-row-wide'),
                'wrs_observaciones' => array('label' => __('Observaciones', 'woodmart-referral-system'), 'type' => 'textarea', 'class' => 'form-row-wide'),
            );

            $departamentos_data = function_exists('wrs_get_colombia_geo_data') ? wrs_get_colombia_geo_data() : array();
            $departamentos_keys = !empty($departamentos_data) ? array_keys($departamentos_data) : array(); 
            if(!empty($departamentos_keys)) sort($departamentos_keys);
            $localidades_bogota = function_exists('wrs_get_bogota_localidades') ? wrs_get_bogota_localidades() : array();
            $current_depto_val_wrs = get_user_meta($user_id, 'wrs_departamento_viv', true);

            foreach ($wrs_profile_fields as $meta_key => $field_args) {
                $value = get_user_meta( $user_id, $meta_key, true );
                $field_id = $field_args['id_attr'] ?? 'profile_' . $meta_key;
                $p_class = $field_args['class'];
                $p_extra_class = $field_args['class_p_extra'] ?? '';
                $p_extra_style = '';
                if ($meta_key === 'wrs_localidad') { $p_class = $field_args['class_wrapper'] ?? $p_class; if ($current_depto_val_wrs !== 'BOGOTA_DC') { $p_extra_style = 'style="display: none;"'; }}
                if ($meta_key === 'wrs_barrio') { $p_class = ($current_depto_val_wrs === 'BOGOTA_DC') ? 'form-row form-row-last' : 'form-row form-row-wide';}
                echo '<p class="' . esc_attr(trim('form-row ' . $p_class . ' ' . $p_extra_class)) . '" ' . $p_extra_style . '>';
                echo '<label for="' . esc_attr($field_id) . '">' . esc_html($field_args['label']) . (isset($field_args['required']) && $field_args['required'] ? '&nbsp;<span class="required">*</span>' : '') . '</label>';
                switch ($field_args['type']) {
                    case 'select': echo '<select name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" class="input-select">'; if (function_exists('wrs_get_options') && !empty($field_args['options_key'])) { $options = wrs_get_options($field_args['options_key']); foreach ($options as $opt_val => $opt_label) { echo '<option value="'.esc_attr($opt_val).'" '.selected($value, $opt_val, false).'>'.$opt_label.'</option>'; }} echo '</select>'; break;
                    case 'select_dep': echo '<select name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" class="input-select wrs-departamento-select"><option value="">'.esc_html__('Selecciona...', 'woodmart-referral-system').'</option>'; if (!empty($departamentos_data)) { foreach ($departamentos_keys as $k) { echo '<option value="'.esc_attr($k).'" '.selected($value, $k, false).'>'.html_entity_decode($departamentos_data[$k]['name']).'</option>'; }} echo '</select>'; break;
                    case 'select_mun': $current_municipio_val = get_user_meta($user_id, 'wrs_municipio_viv', true); echo '<select name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" class="input-select" '.(empty($current_depto_val_wrs) ? 'disabled' : '').'>'; if (!empty($current_depto_val_wrs) && !empty($current_municipio_val) && isset($departamentos_data[$current_depto_val_wrs]['cities'][$current_municipio_val]['name'])) { echo '<option value="'.esc_attr($current_municipio_val).'" selected>'.esc_html(html_entity_decode($departamentos_data[$current_depto_val_wrs]['cities'][$current_municipio_val]['name'])).'</option>'; } elseif (!empty($current_municipio_val)) { echo '<option value="'.esc_attr($current_municipio_val).'" selected>'.esc_html($current_municipio_val).'</option>'; } else { echo '<option value="">'.esc_html__('Selecciona departamento...','woodmart-referral-system').'</option>'; } echo '</select>'; break;
                    case 'select_loc': echo '<select name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" class="input-select">'; if (!empty($localidades_bogota)) { foreach ($localidades_bogota as $loc_val => $loc_label) { echo '<option value="'.esc_attr($loc_val).'" '.selected($value, $loc_val, false).'>'.$loc_label.'</option>'; }} echo '</select>'; break;
                    case 'textarea': echo '<textarea name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" class="input-text" rows="3">'.esc_textarea($value).'</textarea>'; break;
                    case 'checkbox_group': if (!is_array($value)) { $value = array(); } echo '<div class="wrs-checkbox-group-inner" style="max-height:150px; overflow-y:auto; border:1px solid #eee; padding:10px; margin-top:5px;">'; if(function_exists('wrs_get_options') && !empty($field_args['options_key'])){ $options = wrs_get_options($field_args['options_key']); foreach($options as $opt_v => $opt_l) { echo '<p style="margin-bottom:3px;"><label class="checkbox-label" style="font-weight:normal; display:block;"><input type="checkbox" name="'.esc_attr($meta_key).'[]" value="'.esc_attr($opt_v).'" '.checked(in_array($opt_v, $value), true, false).'> '.html_entity_decode($opt_l).'</label></p>'; }} echo '</div>'; break;
                    default: $input_type = $field_args['type']; $extra_attrs_str = isset($field_args['extra_attrs']) ? $field_args['extra_attrs'] : ''; echo '<input type="'.esc_attr($input_type).'" name="'.esc_attr($meta_key).'" id="'.esc_attr($field_id).'" value="'.esc_attr($value).'" class="input-text" '.$extra_attrs_str.' />'; break;
                }
                echo '</p>';
                if (strpos($p_class, 'form-row-last') !== false || strpos($p_class, 'form-row-wide') !== false) { echo '<div class="clear"></div>'; }
            }
            ?>
        </fieldset>

        <fieldset style="margin-top: 30px;">
            <legend><?php esc_html_e( 'Cambio de contraseña', 'woocommerce' ); ?></legend>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_current"><?php esc_html_e( 'Contraseña actual (déjala en blanco para no cambiar)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_1"><?php esc_html_e( 'Nueva contraseña (déjala en blanco para no cambiar)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password_2"><?php esc_html_e( 'Confirmar nueva contraseña', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
            </p>
        </fieldset>
        <div class="clear"></div>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <p style="margin-top: 20px;">
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Guardar cambios', 'woocommerce' ); ?>"><?php esc_html_e( 'Guardar cambios', 'woocommerce' ); ?></button>
            <input type="hidden" name="action" value="save_account_details" />
        </p>

        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>
    <?php
    // Encolar script de dinámicas directamente aquí para "Mi Perfil"
    if (function_exists('is_account_page') && is_account_page() && function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('my-profile')) {
        error_log('[WRS My Profile Endpoint DEBUG v21] Encolando wrs-registration-dynamics.js para perfil.');
        $plugin_dir_url_local = defined('WRS_PLUGIN_DIR_URL') ? WRS_PLUGIN_DIR_URL : plugin_dir_url( dirname( __FILE__, 2 ) ) . 'woodmart-referral-systemV3/';
        $plugin_version_local = defined('WRS_PLUGIN_VERSION') ? WRS_PLUGIN_VERSION : 'FS.1.0'; 
        wp_enqueue_script('wrs-registration-dynamics-profile', $plugin_dir_url_local . 'assets/js/wrs-registration-dynamics.js', array('jquery'), $plugin_version_local, true);
        wp_localize_script('wrs-registration-dynamics-profile', 'wrs_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
    }
}

/**
 * Valida los campos WRS del formulario de "Mi Perfil" antes de que WooCommerce los guarde.
 * (Asegúrate de que esta función esté definida UNA SOLA VEZ)
 */
function wrs_validate_my_profile_details_on_save( $errors, $user ) {
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'save_account_details' ) {
        $required_wrs_fields = array(
            'wrs_tipo_doc'         => __( 'El <strong>Tipo de Documento</strong> es obligatorio.', 'woodmart-referral-system' ),
            'wrs_numero_doc'       => __( 'El <strong>Número de Documento</strong> es obligatorio.', 'woodmart-referral-system' ),
            'wrs_telefono'         => __( 'El <strong>Teléfono</strong> es obligatorio.', 'woodmart-referral-system' ),
        );
        foreach($required_wrs_fields as $key => $message) {
            if ( isset( $_POST[$key] ) && empty( $_POST[$key] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce 'save_account_details' verificado por WooCommerce
                $errors->add( $key . '_error_profile', $message );
            }
        }
    }
}

/**
 * Guarda los campos WRS personalizados cuando se actualiza el perfil desde "Mi Cuenta".
 * (Asegúrate de que esta función esté definida UNA SOLA VEZ)
 */
function wrs_save_my_profile_details_wrs_fields( $user_id ) {
    // El nonce 'save-account-details-nonce' y la acción 'save_account_details' ya son verificados por WooCommerce.
    error_log('[WRS Profile Save Hook v21] Guardando campos WRS para user_id: ' . $user_id);
    $wrs_fields_to_save_map = array(
        'wrs_tipo_doc' => 'sanitize_text_field', 'wrs_numero_doc' => 'sanitize_text_field', 'wrs_telefono' => 'sanitize_text_field',
        'wrs_genero' => 'sanitize_text_field', 'wrs_fecha_nacimiento' => 'sanitize_text_field', 'wrs_departamento_viv' => 'sanitize_text_field',
        'wrs_municipio_viv' => 'sanitize_text_field', 'wrs_localidad' => 'sanitize_text_field', 'wrs_barrio' => 'sanitize_text_field',
        'wrs_vereda' => 'sanitize_text_field', 'wrs_direccion' => 'sanitize_text_field', 'wrs_estado_civil' => 'sanitize_text_field',
        'wrs_nivel_educativo' => 'sanitize_text_field', 'wrs_profesion' => 'sanitize_text_field', 'wrs_ocupacion' => 'sanitize_text_field',
        'wrs_es_cuidador' => 'sanitize_text_field', 'wrs_num_hijos' => 'absint', 'wrs_observaciones' => 'sanitize_textarea_field',
    );
    foreach ($wrs_fields_to_save_map as $field_key => $sanitize_callback) {
        if (isset($_POST[$field_key])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $value = wp_unslash($_POST[$field_key]); // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $value_to_save = call_user_func( $sanitize_callback, $value );
            update_user_meta($user_id, $field_key, $value_to_save);
        }
    }
    if ( isset( $_POST['wrs_temas_interes'] ) && is_array( $_POST['wrs_temas_interes'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $sanitized_temas = array_map( 'sanitize_text_field', wp_unslash( $_POST['wrs_temas_interes'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
        update_user_meta( $user_id, 'wrs_temas_interes', $sanitized_temas );
    } else { 
        update_user_meta( $user_id, 'wrs_temas_interes', array() ); 
    }
}

// NO AÑADIR LAS FUNCIONES wrs_handle_my_profile_update() NI wrs_handle_my_profile_update_on_template_redirect() aquí.
?>