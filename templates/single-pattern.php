<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** These templates are part of the core patterns files
 ** and will be overwritten when upgrading patterns.
 **
 ** This template was automatically copied into your site's active
 ** theme directory (in the patterns/ subdirectory) when the 
 ** plugin was activated. Please edit that copy of the template.
 **
 **/
?>

<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="pattern-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>

					<p><?=get_post_meta($post->ID, 'teaser', TRUE);?></p>

					<p><?=get_post_meta($post->ID, 'long_description', TRUE);?></p>

					<p><strong>Ingredients:</strong> <?=get_post_meta($post->ID, 'ingredients', TRUE);?></p>

					<?=get_post_meta($post->ID, 'nutrition_facts', TRUE);?>

				</div><!-- .entry-content -->

			</article>

		<?php endwhile; /* end of the loop. */ ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar('content'); ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>