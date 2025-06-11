<?php
/**
 * Registro del Custom Post Type "Iniciativas" y su Taxonomía.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registra el Custom Post Type 'Iniciativas' (programáticamente 'wrs_landing_page').
 * Labels y slug actualizados a "Iniciativas".
 */
function wrs_register_initiatives_cpt() { // Nombre de función más descriptivo
    $labels = array(
        'name'                  => _x( 'Iniciativas', 'Post type general name', 'woodmart-referral-system' ),
        'singular_name'         => _x( 'Iniciativa', 'Post type singular name', 'woodmart-referral-system' ),
        'menu_name'             => _x( 'Iniciativas', 'Admin Menu text', 'woodmart-referral-system' ),
        'name_admin_bar'        => _x( 'Iniciativa', 'Add New on Toolbar', 'woodmart-referral-system' ),
        'add_new'               => __( 'Añadir Nueva', 'woodmart-referral-system' ),
        'add_new_item'          => __( 'Añadir Nueva Iniciativa', 'woodmart-referral-system' ),
        'new_item'              => __( 'Nueva Iniciativa', 'woodmart-referral-system' ),
        'edit_item'             => __( 'Editar Iniciativa', 'woodmart-referral-system' ),
        'view_item'             => __( 'Ver Iniciativa', 'woodmart-referral-system' ),
        'all_items'             => __( 'Todas las Iniciativas', 'woodmart-referral-system' ),
        'search_items'          => __( 'Buscar Iniciativas', 'woodmart-referral-system' ),
        'parent_item_colon'     => __( 'Iniciativa Padre:', 'woodmart-referral-system' ),
        'not_found'             => __( 'No se encontraron Iniciativas.', 'woodmart-referral-system' ),
        'not_found_in_trash'    => __( 'No se encontraron Iniciativas en la papelera.', 'woodmart-referral-system' ),
        'featured_image'        => _x( 'Imagen Destacada de Iniciativa', 'Overrides the “Featured Image” phrase for this post type.', 'woodmart-referral-system' ),
        'set_featured_image'    => _x( 'Establecer imagen destacada', 'Overrides the “Set featured image” phrase for this post type.', 'woodmart-referral-system' ),
        'remove_featured_image' => _x( 'Eliminar imagen destacada', 'Overrides the “Remove featured image” phrase for this post type.', 'woodmart-referral-system' ),
        'use_featured_image'    => _x( 'Usar como imagen destacada', 'Overrides the “Use as featured image” phrase for this post type.', 'woodmart-referral-system' ),
        'archives'              => _x( 'Archivo de Iniciativas', 'The post type archive label used in nav menus.', 'woodmart-referral-system' ),
        'insert_into_item'      => _x( 'Insertar en Iniciativa', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', 'woodmart-referral-system' ),
        'uploaded_to_this_item' => _x( 'Subido a esta Iniciativa', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', 'woodmart-referral-system' ),
        'filter_items_list'     => _x( 'Filtrar lista de Iniciativas', 'Screen reader text for the filter links heading on the post type listing screen.', 'woodmart-referral-system' ),
        'items_list_navigation' => _x( 'Navegación de lista de Iniciativas', 'Screen reader text for the pagination heading on the post type listing screen.', 'woodmart-referral-system' ),
        'items_list'            => _x( 'Lista de Iniciativas', 'Screen reader text for the items list heading on the post type listing screen.', 'woodmart-referral-system' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'iniciativa' ), // Slug público actualizado
        'capability_type'    => 'post',
        'has_archive'        => false, 
        'hierarchical'       => false,
        'menu_position'      => 20, 
        'menu_icon'          => 'dashicons-megaphone', 
        'supports'           => array( 'title', 'editor', 'thumbnail', 'elementor' ), // Añadido 'editor', 'thumbnail', 'elementor'
        'show_in_rest'       => true, 
    );

    register_post_type( 'wrs_landing_page', $args ); // El nombre programático se mantiene
}
// El add_action se moverá al archivo principal del plugin o a un cargador de hooks.

/**
 * Registra la taxonomía 'Categoría de Iniciativa' (programáticamente 'wrs_landing_category').
 * Labels y slug actualizados.
 */
function wrs_register_initiative_category_taxonomy() { // Nombre de función más descriptivo
    $labels = array(
        'name'              => _x( 'Categorías de Iniciativa', 'taxonomy general name', 'woodmart-referral-system' ),
        'singular_name'     => _x( 'Categoría de Iniciativa', 'taxonomy singular name', 'woodmart-referral-system' ),
        'search_items'      => __( 'Buscar Categorías de Iniciativa', 'woodmart-referral-system' ),
        'all_items'         => __( 'Todas las Categorías de Iniciativa', 'woodmart-referral-system' ),
        'parent_item'       => __( 'Categoría Padre', 'woodmart-referral-system' ),
        'parent_item_colon' => __( 'Categoría Padre:', 'woodmart-referral-system' ),
        'edit_item'         => __( 'Editar Categoría de Iniciativa', 'woodmart-referral-system' ),
        'update_item'       => __( 'Actualizar Categoría de Iniciativa', 'woodmart-referral-system' ),
        'add_new_item'      => __( 'Añadir Nueva Categoría de Iniciativa', 'woodmart-referral-system' ),
        'new_item_name'     => __( 'Nombre Nueva Categoría de Iniciativa', 'woodmart-referral-system' ),
        'menu_name'         => __( 'Categorías de Iniciativa', 'woodmart-referral-system' ),
    );
    $args = array(
        'hierarchical'      => true, 
        'labels'            => $labels,
        'show_ui'           => true, 
        'show_admin_column' => true, 
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categoria-iniciativa' ), // Slug público actualizado
        'show_in_rest'      => true, 
    );
    register_taxonomy(
        'wrs_landing_category',     // Nombre programático se mantiene
        array( 'wrs_landing_page' ), // Asociada al CPT (nombre programático)
        $args
    );
}
// El add_action se moverá al archivo principal del plugin o a un cargador de hooks.

/**
 * Filtra la plantilla a usar para mostrar single 'wrs_landing_page' (Iniciativas).
 * Fuerza el uso de nuestra plantilla personalizada dentro del plugin.
 *
 * @param string $template La ruta de la plantilla que WordPress iba a usar.
 * @return string La ruta de la plantilla a usar (la nuestra o la original).
 */
function wrs_include_initiative_template( $template ) { // Nombre de función más descriptivo
    // 'wrs_landing_page' es el nombre programático del CPT
    if ( is_singular( 'wrs_landing_page' ) ) {
        // Usar constante para la ruta si está definida, sino plugin_dir_path
        $plugin_path = defined('WRS_PLUGIN_DIR_PATH') ? WRS_PLUGIN_DIR_PATH : plugin_dir_path( dirname( __FILE__ ) ); // dirname para salir de includes/
        $new_template = $plugin_path . 'templates/single-wrs_landing_page-template.php'; // El nombre del archivo de plantilla no cambia

        if ( file_exists( $new_template ) ) {
            return $new_template; 
        }
    }
    return $template;
}
// El add_filter se moverá al archivo principal del plugin o a un cargador de hooks.