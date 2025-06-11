<?php
/**
 * Contenido y lógica para la página de administración "Red de Referidos".
 * v21 - El encolado de scripts y la preparación de datos para el gráfico se manejan en wrs_admin_enqueue_assets.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Muestra el contenido de la página de administración de la Red de Referidos.
 * Incluye botón para exportar a CSV y filtro por Iniciativa.
 * El gráfico se renderizará en el div #wrs_admin_chart_div por el script JS encolado.
 */
function wrs_render_admin_network_page_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'No tienes permisos suficientes para acceder a esta página.', 'woodmart-referral-system' ) );
    }

    echo '<div class="wrap wrs-admin-network-page">'; // Clase contenedora para estilos
    echo '<h1>' . esc_html__( 'Red de Referidos Completa', 'woodmart-referral-system' ) . '</h1>';

    // --- Botón/Enlace de Exportación ---
    $export_action_url = admin_url( 'admin.php' ); 
    $export_link_args = array(
        'action'   => 'export_wrs_network',
        '_wpnonce' => wp_create_nonce( 'export_wrs_network_nonce' )
    );
    $export_url_with_nonce = add_query_arg( $export_link_args, $export_action_url );

    echo '<a href="' . esc_url( $export_url_with_nonce ) . '" class="button button-primary" style="margin-bottom: 20px; margin-right: 10px;">';
    echo esc_html__( 'Exportar Red a CSV', 'woodmart-referral-system' );
    echo '</a>';
    
    // --- Filtro por Iniciativa ---
    $args_iniciativas = array(
        'post_type'      => 'wrs_landing_page', // Nombre programático del CPT "Iniciativas"
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $iniciativas = get_posts($args_iniciativas);
    
    // Sanitizar el valor del filtro actual
    $current_filter_initiative_id_get = isset($_GET['wrs_initiative_filter']) ? sanitize_text_field(wp_unslash($_GET['wrs_initiative_filter'])) : 'all';
    $current_filter_initiative_id = 'all'; // Default
    if ($current_filter_initiative_id_get === 'all') {
        $current_filter_initiative_id = 'all';
    } elseif (is_numeric($current_filter_initiative_id_get) && $current_filter_initiative_id_get > 0 ) {
        $current_filter_initiative_id = absint($current_filter_initiative_id_get);
    }
    
    ?>
    <form method="GET" action="" style="display: inline-block; margin-bottom: 20px;">
        <input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? esc_attr(sanitize_text_field(wp_unslash($_REQUEST['page']))) : ''; ?>">
        <label for="wrs_initiative_filter" style="margin-left: 10px; font-weight: bold;"><?php esc_html_e('Filtrar por Iniciativa:', 'woodmart-referral-system'); ?></label>
        <select name="wrs_initiative_filter" id="wrs_initiative_filter" style="min-width: 200px;">
            <option value="all" <?php selected($current_filter_initiative_id, 'all'); ?>><?php esc_html_e('Todas las Iniciativas', 'woodmart-referral-system'); ?></option>
            <?php if (!empty($iniciativas)) : ?>
                <?php foreach ($iniciativas as $iniciativa_item) : ?>
                    <option value="<?php echo esc_attr($iniciativa_item->ID); ?>" <?php selected($current_filter_initiative_id, $iniciativa_item->ID); ?>>
                        <?php echo esc_html($iniciativa_item->post_title); ?> (ID: <?php echo esc_html($iniciativa_item->ID); ?>)
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <input type="submit" class="button" value="<?php esc_attr_e('Filtrar', 'woodmart-referral-system'); ?>">
        <?php if ($current_filter_initiative_id !== 'all' && $current_filter_initiative_id !== 0): ?>
            <a href="<?php echo esc_url(admin_url('users.php?page=wrs-referral-network&wrs_initiative_filter=all')); ?>" class="button" style="margin-left: 5px;"><?php esc_html_e('Mostrar Todas', 'woodmart-referral-system'); ?></a>
        <?php endif; ?>
    </form>
    <br>
    <?php
    
    echo '<p>' . esc_html__( 'Este gráfico muestra la estructura de referidos. Puede tomar unos momentos en cargar si la red es grande.', 'woodmart-referral-system' ) . '</p>';

    // Contenedor del gráfico con ID y altura definida para Cytoscape.js
    // El borde rojo es para depuración y ver si el div se imprime.
    echo '<div id="wrs_admin_chart_div" class="wrs-genealogy-chart-container" style="height: 700px; width: 100%; border: 1px solid #FF0000; background-color: #f9f9f9;">';
    // El script JS buscará este div para dibujar el gráfico.
    // El mensaje "Cargando..." puede ser útil si la preparación de datos JS es asíncrona o toma tiempo.
    // En nuestro caso, los datos se pasan con wp_localize_script, así que el JS debería tenerlos al ejecutarse.
    // Si el JS indica que no hay datos, mostrará un mensaje.
    echo '</div>';

    echo '</div>'; // Cierre de .wrap
}
// El add_action para 'admin_menu' que llama a 'wrs_add_referral_network_admin_menu' 
// (y esa función a su vez usa 'wrs_render_admin_network_page_content' como callback)
// está en tu archivo principal del plugin.
?>