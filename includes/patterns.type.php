<?php
/**
 * Class that creates the patterns custom post type
 * 
 *
 */
class patterns_type
{
   public $_mandatory_fields = array(
      'context' => array(
         'id' => 'context', 
         'label' => 'Context',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'problem' => array(
         'id' => 'problem', 
         'label' => 'Problem',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'forces' => array(
         'id' => 'forces', 
         'label' => 'Forces',
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
   
   public $_optional_fields = array(
      'rationale' => array(
         'id' => 'rationale', 
         'label' => 'Rationale',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'indications' => array(
         'id' => 'indications', 
         'label' => 'Indications',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'resulting_context' => array(
         'id' => 'resulting_context', 
         'label' => 'Resulting Context',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'related_patterns' => array(
         'id' => 'related_patterns', 
         'label' => 'Related Patterns',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'examples' => array(
         'id' => 'examples', 
         'label' => 'Examples',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'aliases' => array(
         'id' => 'aliases', 
         'label' => 'Aliases',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
      'acknowledgements' => array(
         'id' => 'acknowledgements', 
         'label' => 'Acknowledgements',
         'allow_tags' => TRUE,
         'control' => 'textarea',
      ),
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
      add_action('save_post', array(&$this, 'save_pattern'));
   }

   // ------------------------------------------------------------

   /**
    * hook into WP's init action hook
    */
   public function init()
   {
      // Initialize Post Type
      $this->create_post_type();
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
         'supports' => array('title', 'thumbnail'),
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
   public function save_pattern($post_id)
   {
      /*
       * We need to verify this came from the our screen and with proper authorization,
       * because save_post can be triggered at other times.
       */

      // Check if our nonce is set.
      if ( ! isset( $_POST['patterns_meta_boxes_nonce'] ) ) {
         return $post_id;
      }
 
      $nonce = $_POST['patterns_meta_boxes_nonce'];
 
      // Verify that the nonce is valid.
      if ( ! wp_verify_nonce( $nonce, 'patterns_meta_boxes' ) ) {
         return $post_id;
      }
 
      /*
       * If this is an autosave, our form has not been submitted,
       * so we don't want to do anything.
       */
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
         return $post_id;
      }
 
      // Check the user's permissions.
      if ( 'patterns' == $_POST['post_type'] ) {
         if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
         }
      }
 
      /* OK, it's safe for us to save the data now. */ 
      
      foreach ($this->_mandatory_fields as $field_name)
      {
         // Update the meta field.
         update_post_meta( $post_id, $field_name['id'], $_POST[$field_name['id']] );
      }
      foreach ($this->_optional_fields as $field_name)
      {
         // Update the meta field.
         update_post_meta( $post_id, $field_name['id'], $_POST[$field_name['id']] );
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
      // Add mandatory elements
      add_meta_box(
         'meta_box_mandatory_fields', 
         'Mandatory Elements', 
         array(&$this, 'meta_box_mandatory_fields_content'), 
         'patterns', 
         'normal', 
         'high', 
      );

     // Add optional elements
      add_meta_box(
         'meta_box_optional_fields', 
         'Optional Elements', 
         array(&$this, 'meta_box_optional_fields_content'), 
         'patterns', 
         'normal', 
         'default', 
      );
   }

   // ------------------------------------------------------------
   
   /*
    * Callback for add_meta_box() in $this->add_meta_boxes()
    */
   function meta_box_mandatory_fields_content($post)
   {
      wp_nonce_field('patterns_meta_boxes', 'patterns_meta_boxes_nonce' );

      foreach ($this->_mandatory_fields as $field_name)
      {
         $data = null;
         if ($field_name['control'] == 'select')
         {
            $data = $field_name['options'];
         }
         // Use get_post_meta to retrieve an existing value from the database.
         $value = get_post_meta( $post->ID, $field_name['id'], true );
         echo patterns_get_control(
            $field_name['control'],
            $field_name['label'],
            'patterns_settings_'.$field_name['id'],
            $field_name['id'],
            $value,
            $data
         );
      }
   }

   // ------------------------------------------------------------
   
   /*
    * Callback for add_meta_box() in $this->add_meta_boxes()
    */
   function meta_box_optional_fields_content($post, $args)
   {
      foreach ($this->_optional_fields as $field_name)
      {
         $data = null;
         if ($field_name['control'] == 'select')
         {
            $data = $field_name['options'];
         }
         // Use get_post_meta to retrieve an existing value from the database.
         $value = get_post_meta( $post->ID, $field_name['id'], true );
         echo patterns_get_control(
            $field_name['control'],
            $field_name['label'],
            'patterns_settings_'.$field_name['id'],
            $field_name['id'],
            $value,
            $data
         );
      }
   }


} // END class PostTypeTemplate
