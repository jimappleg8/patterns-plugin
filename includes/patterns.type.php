<?php
/**
 * Class that creates the patterns custom post type
 * 
 *
 */
class patterns_type
{
   public $_fields = array(
      'pattern_num' => array(
         'id' => 'pattern_num',
         'label' => 'Pattern Number',
         'allow_tags' => FALSE,
         'control' => 'text',
      ),
      'certainty' => array(
         'id' => 'certainty',        
         'label' => 'Certainty',
         'allow_tags' => FALSE,
         'control' => 'select',
         'options' => array(
            array('value' => 'none', 'text' => ''),
            array('value' => 'low', 'text' => '*'),
            array('value' => 'medium', 'text' => '**'),
            array('value' => 'high', 'text' => '***'),
         ),
      ),
      'context' => array(
         'id' => 'context', 
         'label' => 'Context',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'rationale' => array(
         'id' => 'rationale', 
         'label' => 'Rationale',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'solution' => array(
         'id' => 'solution', 
         'label' => 'Solution',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
   );

   // ------------------------------------------------------------

   /**
    * The Constructor
    */
   public function __construct()
   {
      // register actions
      add_action('init', array(&$this, 'init'), 0);
      add_action('admin_init', array(&$this, 'admin_init'));
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's init action hook
    */
   public function init()
   {
      // Initialize Post Type
      $this->create_post_type();
      add_action('save_post', array(&$this, 'save_post'));
   }

   // ------------------------------------------------------------

   /**
    * Create the post type
    */
   public function create_post_type()
   {
      $labels = array( 
         'name' => 'Pattern Groups',
         'singular_name' => 'Pattern Group',
         'search_items' => 'Search Pattern Groups',
         'popular_items' => 'Popular Pattern Groups',
         'all_items' => 'All Pattern Groups',
         'parent_item' => 'Parent Pattern Group',
         'parent_item_colon' => 'Parent Pattern Group:',
         'edit_item' => 'Edit Pattern Group',
         'update_item' => 'Update Pattern Group',
         'add_new_item' => 'Add New Pattern Group',
         'new_item_name' => 'New Pattern Group',
         'separate_items_with_commas' => 'Separate groups with commas',
         'add_or_remove_items' => 'Add or remove groups',
         'choose_from_most_used' => 'Choose from the most used groups',
         'menu_name' => 'Pattern Groups',
      );

      $args = array( 
         'labels' => $labels,
         'public' => true,
         'show_in_nav_menus' => true,
         'show_ui' => true,
         'show_tagcloud' => true,
         'hierarchical' => true,
         'rewrite' => array('slug' => 'pattern-groups'),
         'query_var' => true
      );

      register_taxonomy('pattern-groups', array('patterns'), $args );
	
      $labels = array(
         'name' => 'Patterns',
         'singular_name' => 'Patterns',
         'add_new' => 'Add New Pattern',
         'add_new_item' => 'Add New Pattern',
         'edit_item' => 'Edit Pattern',
         'new_item' => 'New Pattern',
         'view_item' => 'View Pattern',
         'search_items' => 'Search Patterns',
         'not_found' => 'No Patterns found',
         'not_found_in_trash' => 'No Patterns found in Trash',
         'parent_item_colon' => 'Parent Pattern:',
         'menu_name' => 'Patterns',
      );

      $args = array( 
         'labels' => $labels,
         'hierarchical' => false,
         'description' => 'Patterns',
         'supports' => array('title', 'editor', 'thumbnail'),
         'public' => true,
         'show_ui' => true,
         'show_in_menu' => true,
         'menu_position' => 5,
         'show_in_nav_menus' => true,
         'publicly_queryable' => true,
         'exclude_from_search' => false,
         'has_archive' => true,
         'query_var' => true,
         'can_export' => true,
         'rewrite' => array('slug' => 'patterns'),
         'capability_type' => 'post'
      );
      
      register_post_type('patterns', $args);

   }

   // ------------------------------------------------------------

   /**
    * Save the metaboxes for this custom post type
    */
   public function save_post($post_id)
   {
      // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      {
         return;
      }
      
      // check that the form is being submitted legitimately
      if ( ! wp_verify_nonce($_POST['patterns_nonce'], plugin_basename( __FILE__ )))
      {
         return;
      }

      if (isset($_POST['post_type']) && $_POST['post_type'] == 'patterns' && current_user_can('edit_post', $post_id))
      {
         foreach ($this->_fields as $field_name)
         {
            // Update the post's meta field
            update_post_meta($post_id, $field_name['id'], $_POST[$field_name]);
         }
      }
      else
      {
         return;
      }
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's admin_init action hook
    */
   public function admin_init()
   {           
      // Add metaboxes
      add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's add_meta_boxes action hook
    */
   public function add_meta_boxes()
   {
      global $post;
      
      $patternData = array();
      foreach ($this->_fields as $field_name)
      {
         $patternData[$field_name['id']] = get_post_meta($post->ID, $field_name['id'], true);
      }

      add_meta_box('meta_box_api_fields', 'Patterns', array(&$this, 'meta_box_api_fields_content'), 'patterns', 'normal', 'default', $patternData);

      add_filter("postbox_classes_patterns_meta_box_api_fields", array(&$this, 'minify_metabox'));
   }

   // ------------------------------------------------------------
   
   /*
    * Callback for add_meta_box() in $this->add_meta_boxes()
    */
   function meta_box_api_fields_content($post, $args)
   {
      wp_nonce_field(plugin_basename( __FILE__ ), 'patterns_nonce' );

      foreach ($this->_fields as $field_name)
      {
            $data = null;
            if ($field_name['control'] == 'select')
            {
               $data = $field_name['options'];
            }
            echo patterns_get_control($field_name['control'], $field_name['label'], 'patterns_settings_'.$field_name['id'], $field_name['id'], ((isset($args['args'][$field_name['id']]))?$args['args'][$field_name['id']]:''), $data);
         }
   }

} // END class PostTypeTemplate
