<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
			?>

			<article id="pattern-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php $pattern_num = ''; //get_post_meta($post->ID, 'pattern_num', TRUE); ?>
					<?php $certainty = get_post_meta($post->ID, 'certainty', TRUE); ?>
					<h1><?php if ($pattern_num) : ?><span class="num"><?= $pattern_num; ?></span> <?php endif; ?><?= get_the_title() ?><?php if ($certainty) : echo $certainty; endif; ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
				
                   <div class="context">
                    <h3>Context</h3>
					<p><?= do_shortcode(get_post_meta($post->ID, 'context', TRUE)); ?></p>
					</div>

                    <div class="problem">
                    <h3>Problem</h3>
					<p><?= do_shortcode(get_post_meta($post->ID, 'problem', TRUE)); ?></p>
					</div>

                    <div class="forces">
                    <h3>Forces</h3>
					<p><?= do_shortcode(get_post_meta($post->ID, 'forces', TRUE)); ?></p>
					</div>

                    <div class="solution">
                    <h3>Solution</h3>
					<p><?= do_shortcode(get_post_meta($post->ID, 'solution', TRUE)); ?></p>
				  </div>

                 <?php 
                 $rationale = get_post_meta($post->ID, 'rationale', TRUE);
                 if ( $rationale ) :
                 ?>
                    <div class="rationale">
                    <h3>Rationale</h3>
					<?= do_shortcode($rationale); ?>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $indications = get_post_meta($post->ID, 'indications', TRUE);
                 if ( $indications ) :
                 ?>
                    <div class="indications">
                    <h3>Indications</h3>
					<p><?= do_shortcode($indications); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $resulting_context = get_post_meta($post->ID, 'resulting_context', TRUE);
                 if ( $resulting_context ) :
                 ?>
                    <div class="resulting-context">
                    <h3>Resulting Context</h3>
					<p><?= do_shortcode($resulting_context); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $related_patterns = get_post_meta($post->ID, 'related_patterns', TRUE);
                 if ( $related_patterns ) :
                 ?>
                    <div class="related-patterns">
                    <h3>Related Patterns</h3>
					<p><?= do_shortcode($related_patterns); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $examples = get_post_meta($post->ID, 'examples', TRUE);
                 if ( $examples ) :
                 ?>
                    <div class="examples">
                    <h3>Examples</h3>
					<p><?= do_shortcode($examples); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $aliases = get_post_meta($post->ID, 'aliases', TRUE);
                 if ( $aliases ) :
                 ?>
                    <div class="aliases">
                    <h3>Aliases</h3>
					<p><?= do_shortcode($aliases); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

                 <?php 
                 $acknowledgements = get_post_meta($post->ID, 'acknowledgements', TRUE);
                 if ( $acknowledgements ) :
                 ?>
                    <div class="acknowledgements">
                    <h3>Acknowledgements</h3>
					<p><?= do_shortcode($acknowledgements); ?></p>
					</div>
				 <?php
				 endif;
				 ?>

				</div><!-- .entry-content -->

			</article>

            <?php
			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
