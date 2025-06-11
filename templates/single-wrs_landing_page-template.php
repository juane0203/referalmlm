<?php
/**
 * Plantilla básica para mostrar una única Landing Page Refer (CPT: wrs_landing_page)
 * Intenta simular una página de ancho completo sin sidebar ni comentarios.
 */

get_header(); // Carga la cabecera de tu tema (WoodMart) ?>

<div id="primary" class="content-area"> <?php // Usa clases comunes que el tema podría estilizar ?>
    <main id="main" class="site-main" role="main">

        <?php
        // Start the loop.
        while ( have_posts() ) :
            the_post();

            // Aquí podrías incluir partes de plantilla si sabes cuáles usa tu tema para el contenido,
            // o simplemente mostrar el título y contenido directamente.
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <?php // Clases estándar de post ?>

                <header class="entry-header">
                    <?php // No mostramos el título aquí usualmente, ya que la landing lo tendrá dentro de su contenido ?>
                    <?php // the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><div class="entry-content">
                    <?php
                    the_content(); // Muestra el contenido creado con el editor (Elementor, Gutenberg, etc.)

                    // Opcional: Añadir paginación si el contenido se divide
                    // wp_link_pages( ... );
                    ?>
                </div><?php
                // Si quisieras permitir edición rápida desde el frontend (raro para landings)
                // edit_post_link( ... );
                ?>

            </article><?php

            // NO incluimos la sección de comentarios aquí:
            // if ( comments_open() || get_comments_number() ) :
            //     comments_template();
            // endif;

        // End the loop.
        endwhile;
        ?>

    </main></div><?php // NO incluimos la barra lateral aquí: get_sidebar(); ?>

<?php get_footer(); // Carga el pie de página de tu tema ?>