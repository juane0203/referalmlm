<?php
/**
 * Lógica Central del Sistema de Referidos WRS.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wrs_capture_referrer_and_initiative_session() {
    // error_log('[WRS Capture V7 WC_Session] wrs_capture_referrer_and_initiative_session CALLED. URL: ' . (isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : "N/A"));

    global $post; 

    // --- Captura de ID de Iniciativa de Origen (PRIMERO y usando Sesión WC) ---
    $landing_post_id = 0;
    $is_on_initiative_page = false;

    if ( is_object($post) && isset($post->ID) && get_post_type($post->ID) === 'wrs_landing_page' ) {
        $landing_post_id = $post->ID;
        $is_on_initiative_page = true;
    } 
    elseif ( function_exists('is_singular') && is_singular('wrs_landing_page') ) {
        $queried_object_id = get_queried_object_id();
        if ($queried_object_id && is_numeric($queried_object_id)) {
            $landing_post_id = $queried_object_id;
            $is_on_initiative_page = true;
        }
    }

    if ( $is_on_initiative_page && $landing_post_id > 0 ) {
        if ( function_exists('WC') && WC()->session ) { 
            // Asegurar que la sesión de WC esté iniciada si es necesario
            if ( !WC()->session->has_session() ) {
                WC()->session->set_customer_session_cookie(true);
                error_log('[WRS Capture V7 WC_Session] Sesión de WC iniciada explícitamente.');
            }
            WC()->session->set('wrs_origin_landing_id_wc', absint($landing_post_id)); // Usar un nombre diferente para la prueba o el mismo
            error_log('[WRS Capture V7 WC_Session] WC()->session[\'wrs_origin_landing_id_wc\'] ESTABLECIDA A: ' . absint($landing_post_id));
        } else {
            error_log('[WRS Capture V7 WC_Session] ERROR: WC()->session no disponible al intentar establecer iniciativa.');
        }
    }
    // --- Fin Captura de Iniciativa ---

    // --- Captura de Referente y guardado en Cookie (SEGUNDO) ---
    if ( ! is_admin() && isset( $_GET['ref'] ) ) {
        $ref_code_raw = wp_unslash( $_GET['ref'] );
        // error_log('[WRS Capture V7 WC_Session] Parámetro ?ref detectado. Valor RAW: "' . $ref_code_raw . '"');
        $ref_code = sanitize_user( $ref_code_raw, true );
        if ( !empty( $ref_code ) ) {
            $referrer_user = get_user_by( 'login', $ref_code );
            if ( $referrer_user instanceof WP_User ) {
                $referrer_id = $referrer_user->ID;
                // error_log('[WRS Capture V7 WC_Session] Usuario referente encontrado. ID: ' . $referrer_id);
                if ( !is_user_logged_in() || get_current_user_id() != $referrer_id ) {
                    $cookie_name_ref = 'wrs_referrer_id';
                    $expiry_time = time() + ( DAY_IN_SECONDS * 30 ); 
                    $cookie_path = COOKIEPATH ? COOKIEPATH : '/';
                    $cookie_domain = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
                    $setcookie_result = setcookie( $cookie_name_ref, strval($referrer_id), $expiry_time, $cookie_path, $cookie_domain, is_ssl(), true );
                    // if ($setcookie_result) { // error_log('[WRS Capture V7 WC_Session] COOKIE ' . $cookie_name_ref . ' ESTABLECIDA con valor: ' . $referrer_id); } 
                    // else { error_log('[WRS Capture V7 WC_Session] setcookie() para ' . $cookie_name_ref . ' devolvió FALSE.');}
                }
            }
        }
    }
    // --- Fin Captura de Referente ---
    // error_log("====================== [WRS Capture V7 WC_Session DEBUG END] ======================");
}
// El hook add_action('template_redirect', 'wrs_capture_referrer_and_initiative_session', 5); está en tu archivo principal.

// El add_action para wrs_capture_referrer_and_initiative_session debe estar en 'template_redirect' en tu archivo principal.
// add_action('template_redirect', 'wrs_capture_referrer_and_initiative_session', 5);

// ... (tus funciones wrs_get_user_genealogy_data y wrs_format_data_for_cytoscape sin cambios) ...
/**
 * Obtiene los datos de la genealogía (downline) de un usuario de forma recursiva.
 * Calcula el número de referidos directos para cada nodo.
 */
function wrs_get_user_genealogy_data( $user_id, $max_depth = 10, $current_depth = 0 ) {
    $downline_data = array();
    if ( $current_depth >= $max_depth ) {
        return $downline_data;
    }

    $direct_referral_args = array(
        'meta_key'   => '_wrs_referrer_id',
        'meta_value' => $user_id,
        'fields'     => 'ID', 
        'orderby'    => 'ID',
        'order'      => 'ASC',
        'number'     => -1 // Obtener todos
    );
    $direct_referral_ids = get_users( $direct_referral_args );
    
    if ( ! empty( $direct_referral_ids ) ) {
        foreach ( $direct_referral_ids as $referral_id ) {
            $referral_user_data = get_userdata( $referral_id );
            if ($referral_user_data) {
                $first_name = get_user_meta( $referral_id, 'wrs_nombres', true );
                $last_name = get_user_meta( $referral_id, 'wrs_apellidos', true );
                $full_name = trim( $first_name . ' ' . $last_name );
                $display_name_final = !empty($full_name) ? $full_name : $referral_user_data->display_name;

                // Contar referidos directos para este nodo hijo
                $child_direct_referrals_query = new WP_User_Query(array(
                    'meta_key' => '_wrs_referrer_id', 'meta_value' => $referral_id, 'count_total' => true
                ));
                $child_direct_referrals_count = $child_direct_referrals_query->get_total();

                $downline_data[] = array(
                    'id'                     => $referral_id,
                    'display_name'           => $display_name_final,
                    'email'                  => $referral_user_data->user_email,
                    'direct_referrals_count' => $child_direct_referrals_count,
                    'children'               => wrs_get_user_genealogy_data( $referral_id, $max_depth, $current_depth + 1 ),
                );
            }
        }
    }
    return $downline_data; 
}

/**
 * Formatea los datos de genealogía para Cytoscape.js.
 */
function wrs_format_data_for_cytoscape( $user_node_data, $tree_data, $is_root_node_principal = true ) {
    $elements = array();
    $sub_colors = array('blue1', 'magenta1', 'blue2', 'magenta2');
    $color_count = count($sub_colors);
    $color_index = 0;

    // Nodo Raíz/Principal
    $root_id_cytoscape = 'user_' . $user_node_data['id'];
    $elements[] = array(
        'group' => 'nodes',
        'data'  => array(
            'id'             => $root_id_cytoscape,
            'name'           => $user_node_data['display_name'], // Nombre para tooltip
            'email'          => $user_node_data['email'] ?? '',    // Email para tooltip
            'isPrincipal'    => $is_root_node_principal,
            'downlineCount'  => $user_node_data['direct_referrals_count'] ?? 0,
            'colorType'      => $is_root_node_principal ? 'green' : $sub_colors[$color_index++ % $color_count]
        )
    );

    // Función interna recursiva para procesar hijos
    $process_children_recursive = function($children_array, $parent_id_cy) use (&$elements, &$process_children_recursive, $sub_colors, $color_count, &$color_index) {
        foreach ($children_array as $child_node) {
            $child_id_cy = 'user_' . $child_node['id'];
            $elements[] = array(
                'group' => 'nodes',
                'data'  => array(
                    'id'             => $child_id_cy,
                    'name'           => $child_node['display_name'],
                    'email'          => $child_node['email'] ?? '',
                    'isPrincipal'    => false, 
                    'downlineCount'  => $child_node['direct_referrals_count'] ?? 0,
                    'colorType'      => $sub_colors[$color_index++ % $color_count]
                )
            );
            $elements[] = array(
                'group' => 'edges',
                'data'  => array(
                    'id'     => 'edge_' . str_replace('user_','',$parent_id_cy) . '_' . str_replace('user_','',$child_id_cy),
                    'source' => $parent_id_cy,
                    'target' => $child_id_cy
                )
            );
            if (!empty($child_node['children'])) {
                $process_children_recursive($child_node['children'], $child_id_cy);
            }
        }
    };

    if (!empty($tree_data)) {
        $process_children_recursive($tree_data, $root_id_cytoscape);
    }
    
    return $elements;
}
?>