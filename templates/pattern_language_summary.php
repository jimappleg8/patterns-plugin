<?php
/**
 * Pattern Language Summary template.
 *
 * Available variables:
 *
 *  WP_Term  $group
 *
 */

$root_id = $group[0]->term_id;

function hierarchical_group_tree( $cat )
{
   // wpse-41548 // alchymyth // a hierarchical list of all categories //

   $next = get_terms( array(
      'taxonomy' => 'pattern-groups',
      'hide_empty' => 0,
      'orderby' => 'term_order',
      'order' => 'ASC',
      'parent' => $cat,
   ) );

   if ( $next ) :    
      foreach( $next as $group ) :

         wp_reset_query();
         
         echo '<p class="group"><strong>' . $group->name . ':</strong> ' . $group->description . '</p>';

         $args = array(
            'posts_per_page' => -1,
            'post_type' => 'patterns',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'tax_query' => array(
               array(
                  'taxonomy' => 'pattern-groups',
                  'field' => 'slug',
                  'terms' => $group->slug,
               ),
            ),
         );
         
         $loop = new WP_Query($args);
         echo '<ul>';
         while($loop->have_posts()) : $loop->the_post();
            echo '<li><a href="' . get_permalink() . '" class="pattern-name">' . get_the_title() . '</a></li>';
         endwhile;
         echo '</ul>';

         hierarchical_group_tree( $group->term_id );

      endforeach;    
   endif;

   echo "\n";
   
}  

?>

<div id="pattern-language-summary">

<h2><?php echo $group[0]->name; ?></h2>
<p class="section"><?php echo $group[0]->description; ?></h2>

<?php hierarchical_group_tree( $root_id ); ?>

</div>