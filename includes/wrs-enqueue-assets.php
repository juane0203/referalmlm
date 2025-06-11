<?php
/**
 * Encolado de Scripts y Estilos para el Plugin WRS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Encola scripts y estilos para el frontend.
 */
function wrs_frontend_enqueue_assets() {
    if (!defined('WRS_PLUGIN_DIR_URL') || !defined('WRS_PLUGIN_VERSION')) {
        error_log('WRS_ENQUEUE_ERROR: Constantes WRS_PLUGIN_DIR_URL o WRS_PLUGIN_VERSION NO están definidas.');
        return; 
    }
    $plugin_dir_url = WRS_PLUGIN_DIR_URL;
    $plugin_version = WRS_PLUGIN_VERSION;

    error_log('WRS_ENQUEUE_DEBUG (Mi Perfil Fix): wrs_frontend_enqueue_assets() EJECUTADA.');

    // Estilos generales del frontend
    $load_frontend_styles = false;
    if (function_exists('is_account_page') && is_account_page()) {
        if (function_exists('is_wc_endpoint_url')) {
            // Cargar estilos si es my-profile O genealogy-tree
            if (is_wc_endpoint_url('my-profile') || is_wc_endpoint_url('genealogy-tree')) {
                $load_frontend_styles = true;
            }
        }
    }
    if (function_exists('is_singular') && is_singular('wrs_landing_page')) {
        $load_frontend_styles = true;
    }

    if ($load_frontend_styles) {
        wp_enqueue_style( 'wrs-frontend-styles', $plugin_dir_url . 'assets/css/wrs-frontend-styles.css', array(), $plugin_version );
        error_log('WRS_ENQUEUE_DEBUG (Mi Perfil Fix): wrs-frontend-styles.css ENCOLADO.');
    } else {
        error_log('WRS_ENQUEUE_DEBUG (Mi Perfil Fix): wrs-frontend-styles.css NO encolado esta vez.');
    }

    // JS para dinámicas de Departamento/Municipio
    // YA NO SE ENCOLA DESDE AQUÍ para 'my-profile'. Se hará en el callback del endpoint.
    // Mantener para el formulario de registro si es diferente.
    if ( function_exists('is_account_page') && is_account_page() && 
         ( !is_user_logged_in() || (isset($_GET['action']) && $_GET['action'] === 'register') ) 
       ) {
        // Esta condición ahora solo debería aplicar al formulario de registro público
        error_log('WRS_ENQUEUE_DEBUG (Mi Perfil Fix): Intentando encolar reg-dynamics para Registro Público.');
        wp_enqueue_script( 'wrs-registration-dynamics', $plugin_dir_url . 'assets/js/wrs-registration-dynamics.js', array('jquery'), $plugin_version, true );
        wp_localize_script( 'wrs-registration-dynamics', 'wrs_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
    
    // El encolado para 'genealogy-tree' se hace en su propia función de callback.
    // El encolado para 'my-profile' (para wrs-registration-dynamics.js) se hará en su propia función de callback.
}

 add_action('wp_enqueue_scripts', 'wrs_frontend_enqueue_assets');


/**
 * Encola scripts y estilos necesarios en páginas específicas del admin.
 * CONFIGURACIÓN: Layout 'cose' para admin. Tooltips Manuales.
 * Popper y Dagre están eliminados/comentados.
 */
function wrs_admin_enqueue_assets( $hook_suffix ) {
    $screen = get_current_screen();
    $plugin_dir_url = defined('WRS_PLUGIN_DIR_URL') ? WRS_PLUGIN_DIR_URL : plugin_dir_url(dirname(__FILE__, 2) . '/');
    $plugin_version = defined('WRS_PLUGIN_VERSION') ? WRS_PLUGIN_VERSION : '1.1.0';
    $cytoscape_custom_js_url = $plugin_dir_url . 'assets/js/wrs-cytoscape-chart.js';

    if ( $hook_suffix == 'profile.php' || $hook_suffix == 'user-edit.php' || ($screen && $screen->id === 'users_page_wrs-referral-network') ) {
        wp_enqueue_style( 'wrs-admin-styles', $plugin_dir_url . 'assets/css/wrs-admin-styles.css', array(), $plugin_version );
        if ($screen && $screen->id === 'users_page_wrs-referral-network') {
             wp_enqueue_style( 'wrs-chart-node-styles-for-admin', $plugin_dir_url . 'assets/css/wrs-frontend-styles.css', array(), $plugin_version );
        }
    }

    if ( $screen && $screen->id === 'users_page_wrs-referral-network' ) {
        
        error_log('[WRS Admin Enqueue - Usando Layout COSE]');

        wp_enqueue_script('jquery');

        wp_enqueue_script( 
            'cytoscape-lib', 
            'https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.28.1/cytoscape.min.js', 
            array('jquery'), '3.28.1', true 
        );
        
        $custom_script_handle = 'wrs-cytoscape-custom-chart-admin'; 
        wp_enqueue_script( 
            $custom_script_handle, 
            $cytoscape_custom_js_url, 
            array('jquery', 'cytoscape-lib'), // Solo jQuery y Cytoscape Lib
            $plugin_version . '_' . time(), 
            true 
        );
        
        // --- Preparación de datos para el gráfico de admin (TU LÓGICA COMPLETA AQUÍ) ---
        $current_initiative_filter_id = 'all';
        if (isset($_GET['wrs_initiative_filter']) && $_GET['wrs_initiative_filter'] !== '') {
            if ($_GET['wrs_initiative_filter'] === 'all') { $current_initiative_filter_id = 'all'; } 
            elseif (is_numeric($_GET['wrs_initiative_filter'])) { $current_initiative_filter_id = absint($_GET['wrs_initiative_filter']);}
        }
        $cytoscape_elements_admin = array(); 
        $all_users_processing_args = array('number' => -1, 'fields' => array('ID', 'display_name', 'user_email'));
        // $root_nodes_for_layout_admin_ids = array(); // 'cose' no los usa directamente, pero tu lógica de datos podría.

        if ($current_initiative_filter_id !== 'all') {
            $root_users_args = array( 'meta_query' => array(array('key' => '_wrs_origin_landing_id', 'value' => $current_initiative_filter_id, 'compare' => '=')), 'fields' => 'ID', 'number' => -1 );
            $initial_root_ids_for_filter = get_users($root_users_args); 
            $users_to_render_ids = array();
            if (!empty($initial_root_ids_for_filter)) {
                // $root_nodes_for_layout_admin_ids = $initial_root_ids_for_filter; // Guardar por si otro layout los necesita
                $users_to_render_ids = array_merge($users_to_render_ids, $initial_root_ids_for_filter);
                if (function_exists('wrs_get_all_descendants_ids')) {
                    $descendant_ids = wrs_get_all_descendants_ids($initial_root_ids_for_filter);
                    if (!empty($descendant_ids)) { $users_to_render_ids = array_merge($users_to_render_ids, $descendant_ids); }
                }
                $users_to_render_ids = array_unique($users_to_render_ids);
            }
            if (empty($users_to_render_ids)) $users_to_render_ids = array(0); 
            $all_users_processing_args['include'] = $users_to_render_ids;
        }
        
        $all_users_for_processing = get_users($all_users_processing_args);
        $nodes_map = []; 
        if (!empty($all_users_for_processing)) {
            // Lógica para $is_principal_node_for_this_view si necesitas diferenciar visualmente raíces con cose
            // (Aunque cose no usa 'roots' para el layout, tu estilo sí puede usar 'isPrincipal')
             $root_nodes_for_style_determination = array();
             if ($current_initiative_filter_id !== 'all' && isset($initial_root_ids_for_filter)) {
                 $root_nodes_for_style_determination = $initial_root_ids_for_filter;
             } else if ($current_initiative_filter_id === 'all') {
                 $all_user_ids_in_current_set_for_root_find = wp_list_pluck($all_users_for_processing, 'ID');
                 foreach ($all_users_for_processing as $user_obj_for_root_check) {
                    $ref_id_check = absint(get_user_meta($user_obj_for_root_check->ID, '_wrs_referrer_id', true));
                    if (empty($ref_id_check) || !in_array($ref_id_check, $all_user_ids_in_current_set_for_root_find) ) { 
                        $root_nodes_for_style_determination[] = $user_obj_for_root_check->ID; 
                    }
                }
                $root_nodes_for_style_determination = array_unique($root_nodes_for_style_determination);
             }


            $admin_sub_colors = array('blue1', 'magenta1', 'blue2', 'magenta2'); $admin_color_count = count($admin_sub_colors); $admin_color_index = 0;
            foreach ($all_users_for_processing as $user_obj) {
                $user_id = $user_obj->ID; $node_id_cytoscape = 'user_' . $user_id;
                $first_name = get_user_meta( $user_id, 'wrs_nombres', true ); $last_name = get_user_meta( $user_id, 'wrs_apellidos', true );
                $full_name = trim( $first_name . ' ' . $last_name ); $display_name_final = !empty($full_name) ? $full_name : $user_obj->display_name;
                $direct_referrals_query = new WP_User_Query(array('meta_key' => '_wrs_referrer_id', 'meta_value' => $user_id, 'count_total' => true)); 
                $downline_count_for_node = $direct_referrals_query->get_total();
                
                $is_principal_node_for_this_view = in_array($user_id, $root_nodes_for_style_determination);
                $node_color_type = $is_principal_node_for_this_view ? 'green' : $admin_sub_colors[$admin_color_index % $admin_color_count];
                if (!$is_principal_node_for_this_view) $admin_color_index++;

                $nodes_map[$user_id] = array('group' => 'nodes', 'data'  => array('id' => $node_id_cytoscape, 'name' => $display_name_final, 'email' => $user_obj->user_email, 'isPrincipal' => $is_principal_node_for_this_view, 'downlineCount'  => $downline_count_for_node, 'colorType' => $node_color_type));
            }
            foreach ($nodes_map as $user_id_key => $node_data_for_cy) {
                $cytoscape_elements_admin[] = $node_data_for_cy;
                $referrer_id_val = absint(get_user_meta( $user_id_key, '_wrs_referrer_id', true ));
                if ($referrer_id_val > 0 && $referrer_id_val != $user_id_key && isset($nodes_map[$referrer_id_val])) {
                    $parent_id_cytoscape = 'user_' . $referrer_id_val; 
                    $current_node_id_cytoscape = $node_data_for_cy['data']['id']; 
                    $cytoscape_elements_admin[] = array('group' => 'edges', 'data'  => array('id' => 'edge_' . $referrer_id_val . '_' . $user_id_key, 'source' => $parent_id_cytoscape, 'target' => $current_node_id_cytoscape ));
                }
            }
       }
       // --- FIN Lógica de Preparación de Datos ---
        
        error_log('[WRS Admin Enqueue - Usando Layout COSE] Datos Admin ANTES de Localizar: ' . count($cytoscape_elements_admin) . ' elementos.');
        
        $layout_admin = 'cose'; // Usando 'cose' como layout principal para el admin
        
        wp_localize_script( 
            $custom_script_handle, 
            'wrsAdminChartData', 
            array( 
                'elements'    => !empty($cytoscape_elements_admin) ? $cytoscape_elements_admin : array(),
                'chartDivId'  => 'wrs_admin_chart_div',
                'layoutName'  => $layout_admin, 
                'isUserView'  => false,
                'rootNodeIds' => null // Cose no usa rootNodeIds para su funcionamiento básico
            ) 
        );
        error_log('[WRS Admin Enqueue - Usando Layout COSE] Datos localizados. Layout: ' . $layout_admin . '. Elementos: ' . count($cytoscape_elements_admin));
    }
}
// add_action('admin_enqueue_scripts', 'wrs_admin_enqueue_assets');
?>