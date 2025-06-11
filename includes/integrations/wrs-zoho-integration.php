<?php
/**
 * Funcionalidad de Integración con Zoho CRM para WRS Plugin.
 * - Página de Ajustes para credenciales API.
 * - Envío de datos de nuevos usuarios a Zoho CRM.
 */

// Prevenir acceso directo al archivo.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * --------------------------------------------------------------------------
 * PARTE A: PÁGINA DE AJUSTES DE ZOHO CRM EN WP ADMIN
 * --------------------------------------------------------------------------
 */

/**
 * Añade la página de ajustes de Zoho CRM al menú de administración de WordPress.
 */
function wrs_add_zoho_settings_page_menu() {
    add_options_page(
        __('Ajustes de Zoho CRM para WRS', 'woodmart-referral-system'), // Título de la página
        __('Zoho CRM (WRS)', 'woodmart-referral-system'),             // Título del menú
        'manage_options',                                             // Capacidad requerida
        'wrs-zoho-settings',                                          // Slug de la página
        'wrs_zoho_settings_page_html'                                 // Función que renderiza el HTML de la página
    );
}
add_action('admin_menu', 'wrs_add_zoho_settings_page_menu');

/**
 * Renderiza el HTML de la página de ajustes de Zoho CRM.
 */
function wrs_zoho_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('wrs_zoho_settings_group'); // Grupo de ajustes
            do_settings_sections('wrs-zoho-settings');   // Slug de la página de secciones
            submit_button(__('Guardar Ajustes', 'woodmart-referral-system'));
            ?>
        </form>
        <div style="border-top: 1px solid #ccc; padding-top: 15px; margin-top: 20px;">
            <h3><?php esc_html_e('Instrucciones para Obtener Credenciales de Zoho CRM (OAuth 2.0)', 'woodmart-referral-system'); ?></h3>
            <p><?php esc_html_e('Para integrar este plugin con Zoho CRM, necesitas registrar una nueva aplicación cliente en la Consola de Desarrolladores de Zoho.', 'woodmart-referral-system'); ?></p>
            <ol>
                <li><?php printf(wp_kses(__('Ve a la <a href="%s" target="_blank">Consola de Desarrolladores de Zoho API</a> e inicia sesión.', 'woodmart-referral-system'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://api-console.zoho.com/')); ?></li>
                <li><?php esc_html_e('Elige "Crear Cliente" o "Añadir Cliente".', 'woodmart-referral-system'); ?></li>
                <li><?php esc_html_e('Selecciona el tipo de cliente "Self Client". Este método es adecuado para integraciones de servidor a servidor.', 'woodmart-referral-system'); ?></li>
                <li><?php esc_html_e('Al crear el cliente "Self Client", se te proporcionará un Client ID y un Client Secret.', 'woodmart-referral-system'); ?></li>
                <li><?php esc_html_e('Luego, genera un "Grant Token" usando los scopes necesarios (ej. ZohoCRM.modules.Leads.CREATE, ZohoCRM.modules.Contacts.CREATE, ZohoCRM.settings.ALL). El scope mínimo para este plugin es crear Leads o Contacts.', 'woodmart-referral-system'); ?></li>
                <li><?php esc_html_e('Intercambia este Grant Token por un Refresh Token y un Access Token inicial. Guarda el Client ID, Client Secret y el Refresh Token en los campos de arriba.', 'woodmart-referral-system'); ?></li>
                <li><?php printf(wp_kses(__('Consulta la <a href="%s" target="_blank">documentación de Zoho sobre Self Client OAuth</a> para más detalles específicos.', 'woodmart-referral-system'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://www.zoho.com/crm/developer/docs/api/v5/server-side-apps.html')); ?></li>
                <li><?php esc_html_e('Elige el Dominio API correcto para tu cuenta de Zoho (ej. .com, .eu, .in).', 'woodmart-referral-system'); ?></li>
            </ol>
             <p><strong><?php esc_html_e('Nota Importante sobre Scopes:', 'woodmart-referral-system'); ?></strong> <?php esc_html_e('Los scopes mínimos recomendados para esta integración son:', 'woodmart-referral-system'); ?> <code>ZohoCRM.modules.Leads.CREATE</code> <?php esc_html_e('y/o', 'woodmart-referral-system'); ?> <code>ZohoCRM.modules.Contacts.CREATE</code>. <?php esc_html_e('Si planeas leer o actualizar datos en el futuro, necesitarás scopes adicionales.', 'woodmart-referral-system'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Inicializa los ajustes, secciones y campos para la página de Zoho CRM.
 */
function wrs_zoho_settings_init() {
    register_setting(
        'wrs_zoho_settings_group', 
        'wrs_zoho_options',        
        'wrs_zoho_options_sanitize' 
    );

    add_settings_section(
        'wrs_zoho_connection_section',                 
        __('Configuración de Conexión API', 'woodmart-referral-system'), 
        'wrs_zoho_connection_section_callback',      
        'wrs-zoho-settings'                            
    );

    add_settings_field(
        'wrs_zoho_enable_integration',
        __('Habilitar Integración con Zoho', 'woodmart-referral-system'),
        'wrs_zoho_field_enable_integration_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_enable_integration_id']
    );
    add_settings_field(
        'wrs_zoho_api_domain',
        __('Dominio Base de la API de Zoho', 'woodmart-referral-system'),
        'wrs_zoho_field_api_domain_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_api_domain_id', 'class' => 'wrs-input-row']
    );
    add_settings_field(
        'wrs_zoho_client_id',
        __('Client ID', 'woodmart-referral-system'),
        'wrs_zoho_field_client_id_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_client_id_id', 'class' => 'wrs-input-row']
    );
    add_settings_field(
        'wrs_zoho_client_secret',
        __('Client Secret', 'woodmart-referral-system'),
        'wrs_zoho_field_client_secret_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_client_secret_id', 'class' => 'wrs-input-row']
    );
    add_settings_field(
        'wrs_zoho_refresh_token',
        __('Refresh Token', 'woodmart-referral-system'),
        'wrs_zoho_field_refresh_token_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_refresh_token_id', 'class' => 'wrs-input-row']
    );
    add_settings_field(
        'wrs_zoho_module',
        __('Módulo de Zoho a Crear', 'woodmart-referral-system'),
        'wrs_zoho_field_module_cb',
        'wrs-zoho-settings',
        'wrs_zoho_connection_section',
        ['label_for' => 'wrs_zoho_module_id']
    );
}
add_action('admin_init', 'wrs_zoho_settings_init');

function wrs_zoho_connection_section_callback($args) {
    ?>
    <p id="<?php echo esc_attr($args['id']); ?>">
        <?php esc_html_e('Ingresa aquí tus credenciales de la API de Zoho CRM (OAuth 2.0). Estos datos son necesarios para enviar la información de los nuevos usuarios registrados.', 'woodmart-referral-system'); ?>
    </p>
    <?php
}

function wrs_zoho_field_enable_integration_cb($args) {
    $options = get_option('wrs_zoho_options');
    $checked = isset($options['enable_integration']) ? $options['enable_integration'] : 0;
    ?>
    <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[enable_integration]" value="1" <?php checked(1, $checked, true); ?>>
    <p class="description"><?php esc_html_e('Marca esta casilla para activar el envío de datos a Zoho CRM cuando un nuevo usuario se registra.', 'woodmart-referral-system'); ?></p>
    <?php
}

function wrs_zoho_field_api_domain_cb($args) {
    $options = get_option('wrs_zoho_options');
    $value = isset($options['api_domain']) ? $options['api_domain'] : 'www.zohoapis.com';
    $allowed_domains = [
        'www.zohoapis.com' => '.com (Global)', 
        'www.zohoapis.eu' => '.eu (Europa)', 
        'www.zohoapis.in' => '.in (India)', 
        'www.zohoapis.com.au' => '.com.au (Australia)',
        'www.zohoapis.jp' => '.jp (Japón)'
    ];
    ?>
    <select id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[api_domain]">
        <?php foreach ($allowed_domains as $domain_value => $domain_label) : ?>
            <option value="<?php echo esc_attr($domain_value); ?>" <?php selected($value, $domain_value); ?>>
                <?php echo esc_html($domain_label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">
        <?php esc_html_e('Selecciona el dominio base correcto para tu centro de datos de Zoho.', 'woodmart-referral-system'); ?>
    </p>
    <?php
}

function wrs_zoho_field_client_id_cb($args) {
    $options = get_option('wrs_zoho_options');
    $value = isset($options['client_id']) ? $options['client_id'] : '';
    ?>
    <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[client_id]" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function wrs_zoho_field_client_secret_cb($args) {
    $options = get_option('wrs_zoho_options');
    $value = isset($options['client_secret']) ? $options['client_secret'] : '';
    ?>
    <input type="password" id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[client_secret]" value="<?php echo esc_attr($value); ?>" class="regular-text">
     <p class="description"><?php esc_html_e('El Client Secret se guarda, pero se muestra como campo de contraseña por seguridad.', 'woodmart-referral-system'); ?></p>
    <?php
}

function wrs_zoho_field_refresh_token_cb($args) {
    $options = get_option('wrs_zoho_options');
    $value = isset($options['refresh_token']) ? $options['refresh_token'] : '';
    ?>
    <textarea id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[refresh_token]" rows="3" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
     <p class="description"><?php esc_html_e('El Refresh Token es de larga duración y se usa para generar Access Tokens.', 'woodmart-referral-system'); ?></p>
    <?php
}

function wrs_zoho_field_module_cb($args) {
    $options = get_option('wrs_zoho_options');
    $selected_module = isset($options['module']) ? $options['module'] : 'Leads';
    $modules = [
        'Leads' => __('Leads (Prospectos)', 'woodmart-referral-system'),
        'Contacts' => __('Contacts (Contactos)', 'woodmart-referral-system'),
    ];
    ?>
    <select id="<?php echo esc_attr($args['label_for']); ?>" name="wrs_zoho_options[module]">
        <?php foreach ($modules as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_module, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php esc_html_e('Selecciona el módulo en Zoho CRM donde se crearán los nuevos registros.', 'woodmart-referral-system'); ?></p>
    <?php
}

function wrs_zoho_options_sanitize($input) {
    $sanitized_input = array();
    $options = get_option('wrs_zoho_options'); 

    $sanitized_input['enable_integration'] = isset($input['enable_integration']) ? 1 : 0;
    
    $allowed_api_domains = ['www.zohoapis.com', 'www.zohoapis.eu', 'www.zohoapis.in', 'www.zohoapis.com.au', 'www.zohoapis.jp'];
    if (isset($input['api_domain']) && in_array($input['api_domain'], $allowed_api_domains)) {
        $sanitized_input['api_domain'] = $input['api_domain'];
    } else {
        $sanitized_input['api_domain'] = isset($options['api_domain']) ? $options['api_domain'] : 'www.zohoapis.com';
    }

    if (isset($input['client_id'])) {
        $sanitized_input['client_id'] = sanitize_text_field(trim($input['client_id']));
    } elseif (isset($options['client_id'])) {
        $sanitized_input['client_id'] = $options['client_id'];
    } else {
        $sanitized_input['client_id'] = '';
    }
    
    // Para client_secret y refresh_token, si están vacíos en el input pero existían, mantener el antiguo.
    // Si se envía un nuevo valor, se guarda. Si se borra explícitamente (enviando vacío), se borra.
    if (!empty($input['client_secret'])) {
        $sanitized_input['client_secret'] = sanitize_text_field(trim($input['client_secret']));
    } elseif (isset($options['client_secret']) && empty($input['client_secret_explicitly_empty'])) { 
        // ^_explicitly_empty es un campo hipotético si quisieras permitir borrar con un campo vacío. 
        //   De forma predeterminada, un campo de contraseña vacío en un formulario no sobrescribe un valor guardado.
        $sanitized_input['client_secret'] = $options['client_secret'];
    } else {
         $sanitized_input['client_secret'] = ''; // Si se envió vacío y no había nada o se quiere borrar
    }

    if (!empty($input['refresh_token'])) {
        $sanitized_input['refresh_token'] = sanitize_textarea_field(trim($input['refresh_token']));
    } elseif (isset($options['refresh_token'])) {
        $sanitized_input['refresh_token'] = $options['refresh_token'];
    } else {
        $sanitized_input['refresh_token'] = '';
    }
    
    if (isset($input['module']) && in_array($input['module'], ['Leads', 'Contacts'])) {
        $sanitized_input['module'] = $input['module'];
    } else {
        $sanitized_input['module'] = isset($options['module']) ? $options['module'] : 'Leads';
    }
    return $sanitized_input;
}


/**
 * --------------------------------------------------------------------------
 * PARTE B: LÓGICA DE ENVÍO DE DATOS A ZOHO CRM
 * --------------------------------------------------------------------------
 */

/**
 * Función de utilidad para registrar mensajes relacionados con Zoho.
 */
function wrs_zoho_log_message($message) {
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG === true) {
        error_log('[WRS Zoho]: ' . $message);
    }
}

/**
 * Obtiene un Access Token de Zoho CRM.
 * Maneja el almacenamiento y la renovación del token usando transitorios.
 *
 * @param array $options Las opciones de configuración de Zoho WRS.
 * @return string|false El Access Token o false en caso de error.
 */
function wrs_get_zoho_access_token($options) {
    $transient_name = 'wrs_zoho_access_token';
    $access_token = get_transient($transient_name);

    if (false !== $access_token) {
        return $access_token;
    }

    if (empty($options['refresh_token']) || empty($options['client_id']) || empty($options['client_secret']) || empty($options['api_domain'])) {
        wrs_zoho_log_message(__('Error: Faltan credenciales (Refresh Token, Client ID, Client Secret o API Domain) para generar Access Token.', 'woodmart-referral-system'));
        return false;
    }

    // Construir la URL del token de la cuenta (accounts.zoho.domain)
    // El $options['api_domain'] es como 'www.zohoapis.com', necesitamos 'zoho.com' para la URL de la cuenta.
    $account_server_domain = str_replace('www.zohoapis.', 'zoho.', $options['api_domain']);
    if ($options['api_domain'] === 'www.zohoapis.jp') { // Caso especial para Japón
      $account_server_domain = 'zoho.jp';
    }


    $token_url = sprintf(
        'https://accounts.%s/oauth/v2/token',
        $account_server_domain
    );

    $body = array(
        'refresh_token' => $options['refresh_token'],
        'client_id'     => $options['client_id'],
        'client_secret' => $options['client_secret'],
        'grant_type'    => 'refresh_token',
    );

    wrs_zoho_log_message(sprintf(__('Solicitando nuevo Access Token a: %s', 'woodmart-referral-system'), $token_url));

    $response = wp_remote_post($token_url, array(
        'method'    => 'POST',
        'body'      => $body,
        'timeout'   => 15,
    ));

    if (is_wp_error($response)) {
        wrs_zoho_log_message(__('Error de WP al solicitar Access Token de Zoho: ', 'woodmart-referral-system') . $response->get_error_message());
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $token_data = json_decode($response_body, true);

    if ($response_code === 200 && !empty($token_data['access_token'])) {
        $access_token = $token_data['access_token'];
        $expires_in = isset($token_data['expires_in']) ? intval($token_data['expires_in']) - 120 : 3480; // Guardar con margen de 2 minutos
        set_transient($transient_name, $access_token, $expires_in);
        wrs_zoho_log_message(sprintf(__('Nuevo Access Token de Zoho obtenido y guardado en transitorio. Expirará en %d segundos.', 'woodmart-referral-system'), $expires_in));
        return $access_token;
    } else {
        wrs_zoho_log_message(sprintf(
            __('Error al obtener Access Token de Zoho. Código: %s. Respuesta: %s. Data enviada (sin client_secret): %s', 'woodmart-referral-system'),
            $response_code,
            $response_body,
            wp_json_encode(array_diff_key($body, ['client_secret' => ''])) // No loguear client_secret
        ));
        return false;
    }
}

/**
 * Envía los datos del nuevo usuario a Zoho CRM.
 * Se ejecuta después de que el cliente es creado en WooCommerce.
 *
 * @param int $customer_id El ID del cliente/usuario recién creado.
 */
function wrs_send_user_data_to_zoho_crm($customer_id) {
    $options = get_option('wrs_zoho_options');

    if (empty($options['enable_integration']) || !$options['enable_integration']) {
        return; 
    }
    if (empty($options['api_domain'])) {
        wrs_zoho_log_message(__('Integración Zoho habilitada pero falta el Dominio API. No se enviarán datos.', 'woodmart-referral-system'));
        return;
    }

    wrs_zoho_log_message(sprintf(__('Intentando enviar datos del usuario ID %d a Zoho CRM.', 'woodmart-referral-system'), $customer_id));

    $access_token = wrs_get_zoho_access_token($options);
    if (!$access_token) {
        wrs_zoho_log_message(__('No se pudo obtener Access Token de Zoho. Abortando envío para usuario ID: ', 'woodmart-referral-system') . $customer_id);
        return;
    }

    $user_data = get_userdata($customer_id);
    if (!$user_data) {
        wrs_zoho_log_message(__('No se pudieron obtener datos para el usuario ID: ', 'woodmart-referral-system') . $customer_id);
        return;
    }

    $zoho_payload = array();
    $zoho_module_setting = isset($options['module']) ? $options['module'] : 'Leads';

    $zoho_payload['Last_Name'] = !empty($user_data->last_name) ? $user_data->last_name : $user_data->user_login;
    if (!empty($user_data->first_name)) {
        $zoho_payload['First_Name'] = $user_data->first_name;
    }
    $zoho_payload['Email'] = $user_data->user_email;
    
    $telefono_wrs = get_user_meta($customer_id, 'wrs_telefono', true);
    if (!empty($telefono_wrs)) {
        $zoho_payload['Phone'] = $telefono_wrs;
    }
    
    if ($zoho_module_setting === 'Leads') {
        $zoho_payload['Lead_Source'] = 'WordPress Referral System';
    }
    // Aquí se asume que las funciones wrs_get_colombia_geo_data, wrs_get_bogota_localidades, wrs_get_options
    // están definidas en el archivo principal del plugin y son accesibles globalmente.
    $all_geo_data = function_exists('wrs_get_colombia_geo_data') ? wrs_get_colombia_geo_data() : [];
    $localidades_bogota = function_exists('wrs_get_bogota_localidades') ? wrs_get_bogota_localidades() : [];
    
    $fields_map = [
        'wrs_tipo_doc'         => ['api_name' => 'Tipo_Documento_WRS', 'type' => 'option', 'option_group' => 'tipo_doc'],
        'wrs_numero_doc'       => ['api_name' => 'Numero_Documento_WRS', 'type' => 'text'],
        'wrs_genero'           => ['api_name' => 'Genero_WRS', 'type' => 'option', 'option_group' => 'genero'],
        'wrs_fecha_nacimiento' => ['api_name' => 'Fecha_Nacimiento_WRS', 'type' => 'date'],
        'wrs_departamento_viv' => ['api_name' => 'Departamento_Vivienda_WRS', 'type' => 'geo_depto'],
        'wrs_municipio_viv'    => ['api_name' => 'Municipio_Vivienda_WRS', 'type' => 'geo_municipio', 'depends_on' => 'wrs_departamento_viv'],
        'wrs_localidad'        => ['api_name' => 'Localidad_Bogota_WRS', 'type' => 'localidad_bogota', 'depends_on' => 'wrs_departamento_viv', 'trigger_value' => 'BOGOTA_DC'],
        'wrs_barrio'           => ['api_name' => 'Barrio_WRS', 'type' => 'text'],
        'wrs_vereda'           => ['api_name' => 'Vereda_WRS', 'type' => 'text'],
        'wrs_direccion'        => ['api_name' => 'Direccion_WRS', 'type' => 'text'],
        'wrs_estado_civil'     => ['api_name' => 'Estado_Civil_WRS', 'type' => 'option', 'option_group' => 'estado_civil'],
        'wrs_nivel_educativo'  => ['api_name' => 'Nivel_Educativo_WRS', 'type' => 'option', 'option_group' => 'nivel_educativo'],
        'wrs_profesion'        => ['api_name' => 'Profesion_WRS', 'type' => 'text'],
        'wrs_ocupacion'        => ['api_name' => 'Ocupacion_WRS', 'type' => 'option', 'option_group' => 'ocupacion'],
        'wrs_es_cuidador'      => ['api_name' => 'Es_Cuidador_WRS', 'type' => 'option', 'option_group' => 'es_cuidador'],
        'wrs_num_hijos'        => ['api_name' => 'Numero_Hijos_WRS', 'type' => 'number'],
        'wrs_temas_interes'    => ['api_name' => 'Temas_Interes_WRS', 'type' => 'multiselect_option', 'option_group' => 'temas_interes'],
        'wrs_observaciones'    => ['api_name' => 'Observaciones_WRS', 'type' => 'textarea'],
        // Los siguientes usan el mismo meta_key para diferentes transformaciones
        '_wrs_referrer_id'       => ['api_name' => 'Referido_Por_WRS', 'type' => 'referrer_name'],
        '_wrs_referrer_id_email' => ['api_name' => 'Email_Referente_WRS', 'type' => 'referrer_email', 'source_key' => '_wrs_referrer_id'], // Usar source_key
        '_wrs_referrer_id_val'   => ['api_name' => 'ID_Referente_WRS', 'type' => 'referrer_id', 'source_key' => '_wrs_referrer_id'],
        '_wrs_origin_landing_id' => ['api_name' => 'Iniciativa_Origen_WRS', 'type' => 'initiative_title'],
        '_wrs_origin_landing_id_val' => ['api_name' => 'ID_Iniciativa_Origen_WRS', 'type' => 'initiative_id', 'source_key' => '_wrs_origin_landing_id'],
        'user_registered'        => ['api_name' => 'Fecha_Registro_WP_WRS', 'type' => 'wp_registered_date']
    ];

    $user_meta_cache = [];
    $referrer_data_cache = null; 
    $initiative_data_cache = null;

    foreach ($fields_map as $wp_key => $zoho_field_data) {
        $value = null;
        $api_name = $zoho_field_data['api_name'];
        $source_key = isset($zoho_field_data['source_key']) ? $zoho_field_data['source_key'] : $wp_key;

        if (strpos($source_key, 'wrs_') === 0 || strpos($source_key, '_wrs_') === 0) {
            if (!isset($user_meta_cache[$source_key])) {
                $user_meta_cache[$source_key] = get_user_meta($customer_id, $source_key, true);
            }
            $value = $user_meta_cache[$source_key];
        } elseif ($source_key === 'user_registered') {
            $value = $user_data->user_registered;
        }

        if (!empty($value) || (is_numeric($value) && $value == 0) ) {
            $processed_value = null;

            switch ($zoho_field_data['type']) {
                 case 'option':
                    $options_list = function_exists('wrs_get_options') ? wrs_get_options($zoho_field_data['option_group']) : [];
                    $processed_value = isset($options_list[$value]) ? html_entity_decode($options_list[$value]) : $value;
                    break;
                 case 'geo_depto':
                    $processed_value = isset($all_geo_data[$value]['name']) ? html_entity_decode($all_geo_data[$value]['name']) : $value;
                    break;
                 case 'geo_municipio':
                    $depto_key = $user_meta_cache['wrs_departamento_viv'] ?? '';
                    $processed_value = isset($all_geo_data[$depto_key]['cities'][$value]['name']) ? html_entity_decode($all_geo_data[$depto_key]['cities'][$value]['name']) : $value;
                    break;
                case 'localidad_bogota':
                    $depto_key = $user_meta_cache['wrs_departamento_viv'] ?? '';
                    if ($depto_key === ($zoho_field_data['trigger_value'] ?? 'BOGOTA_DC')) {
                        $processed_value = isset($localidades_bogota[$value]) ? html_entity_decode($localidades_bogota[$value]) : $value;
                    } else {
                        continue 2; 
                    }
                    break;
                 case 'multiselect_option':
                    if (is_array($value) && !empty($value)) {
                        $options_list = function_exists('wrs_get_options') ? wrs_get_options($zoho_field_data['option_group']) : [];
                        $labels = [];
                        foreach ($value as $single_val) {
                            $labels[] = isset($options_list[$single_val]) ? html_entity_decode($options_list[$single_val]) : $single_val;
                        }
                        $processed_value = implode('; ', $labels);
                    } else {
                        $processed_value = is_string($value) ? $value : '';
                    }
                    break;
                case 'date':
                case 'wp_registered_date':
                    $timestamp = strtotime($value);
                    $processed_value = $timestamp ? date('Y-m-d', $timestamp) : null;
                    break;
                case 'number':
                    $processed_value = is_numeric($value) ? $value : null;
                    break;
                 case 'referrer_name':
                 case 'referrer_email':
                 case 'referrer_id':
                    if ($referrer_data_cache === null && absint($value) > 0) {
                        $referrer_data_cache = get_userdata(absint($value));
                    }
                    if ($referrer_data_cache) {
                        if ($zoho_field_data['type'] === 'referrer_name') {
                             $ref_name = trim($referrer_data_cache->first_name . ' ' . $referrer_data_cache->last_name);
                             $processed_value = !empty($ref_name) ? $ref_name : $referrer_data_cache->display_name;
                        } elseif ($zoho_field_data['type'] === 'referrer_email') {
                            $processed_value = $referrer_data_cache->user_email;
                        } elseif ($zoho_field_data['type'] === 'referrer_id') {
                            $processed_value = strval(absint($value));
                        }
                    }
                    break;
                case 'initiative_title':
                 case 'initiative_id':
                    if ($initiative_data_cache === null && absint($value) > 0) {
                        $post = get_post(absint($value));
                        if ($post && $post->post_type === 'wrs_landing_page') {
                            $initiative_data_cache = $post;
                        } else {
                             $initiative_data_cache = false; 
                        }
                    }
                    if ($initiative_data_cache) {
                        if ($zoho_field_data['type'] === 'initiative_title') {
                            $processed_value = get_the_title($initiative_data_cache);
                        } elseif ($zoho_field_data['type'] === 'initiative_id') {
                             $processed_value = strval(absint($value));
                        }
                    }
                    break;
                 case 'textarea':
                 case 'text':
                 default:
                    $processed_value = $value;
                    break;
            }
             if ($processed_value !== null && $processed_value !== '') {
                 $zoho_payload[$api_name] = $processed_value;
            }
        }
    }

    $request_body = array(
        'data' => array($zoho_payload),
    );

    $api_url = sprintf('https://%s/crm/v5/%s', $options['api_domain'], $zoho_module_setting);

    $args = array(
        'method'  => 'POST',
        'headers' => array(
            'Authorization' => 'Zoho-oauthtoken ' . $access_token,
            'Content-Type'  => 'application/json;charset=UTF-8',
        ),
        'body'    => wp_json_encode($request_body),
        'timeout' => 30,
    );

    wrs_zoho_log_message(sprintf(__('Enviando datos a Zoho API URL: %s. Módulo: %s. Payload (solo claves): %s', 'woodmart-referral-system'), $api_url, $zoho_module_setting, wp_json_encode(array_keys($zoho_payload)) ));
    // Para depuración completa del payload (puede ser muy largo y contener datos sensibles):
    // wrs_zoho_log_message('Payload completo: ' . wp_json_encode($request_body));


    $response = wp_remote_post($api_url, $args);

    if (is_wp_error($response)) {
        wrs_zoho_log_message(sprintf(
            __('Error de WP al enviar datos a Zoho para usuario ID %d: %s', 'woodmart-referral-system'),
            $customer_id,
            $response->get_error_message()
        ));
        update_user_meta($customer_id, '_wrs_zoho_sync_status', 'wp_error');
        update_user_meta($customer_id, '_wrs_zoho_error_message', $response->get_error_message());
        update_user_meta($customer_id, '_wrs_zoho_last_sync_time', current_time('mysql'));
        return;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body_raw = wp_remote_retrieve_body($response);
    $response_body = json_decode($response_body_raw, true);

    wrs_zoho_log_message(sprintf(
        __('Respuesta de Zoho API para usuario ID %d. Código: %s. Cuerpo: %s', 'woodmart-referral-system'),
        $customer_id,
        $response_code,
        $response_body_raw // Loguear el cuerpo raw para depuración completa
    ));

    if (in_array($response_code, [201, 202]) && isset($response_body['data'][0]['code']) && $response_body['data'][0]['code'] === 'SUCCESS') {
        $zoho_record_id = isset($response_body['data'][0]['details']['id']) ? $response_body['data'][0]['details']['id'] : 'N/A';
        wrs_zoho_log_message(sprintf(
            __('Usuario ID %d enviado exitosamente a Zoho CRM. Módulo: %s. Zoho Record ID: %s', 'woodmart-referral-system'),
            $customer_id,
            $zoho_module_setting,
            $zoho_record_id
        ));
        update_user_meta($customer_id, '_wrs_zoho_sync_status', 'success');
        update_user_meta($customer_id, '_wrs_zoho_record_id', $zoho_record_id);
        update_user_meta($customer_id, '_wrs_zoho_last_sync_time', current_time('mysql'));

    } else {
        $error_message = isset($response_body['data'][0]['message']) ? $response_body['data'][0]['message'] : $response_body_raw;
        if (isset($response_body['data'][0]['details']['api_name'])) {
             $error_message .= ' (Field API Name: ' . $response_body['data'][0]['details']['api_name'] . ')';
        } elseif (isset($response_body['message']) && empty($response_body['data'])) { 
             $error_message = $response_body['message'];
        }

        wrs_zoho_log_message(sprintf(
            __('Error al enviar usuario ID %d a Zoho CRM. Código de respuesta: %s. Mensaje: %s', 'woodmart-referral-system'),
            $customer_id,
            $response_code,
            $error_message
        ));
        update_user_meta($customer_id, '_wrs_zoho_sync_status', 'error');
        update_user_meta($customer_id, '_wrs_zoho_error_message', $error_message);
        update_user_meta($customer_id, '_wrs_zoho_last_sync_time', current_time('mysql'));
    }
}
add_action('woocommerce_created_customer', 'wrs_send_user_data_to_zoho_crm', 20, 1);

?>