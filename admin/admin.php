<?php
/**
 * patterns_options class to manage options
 *
 * @package patterns
 * @author Jim Applegate <jim.applegate@hain.com>
 * 
 * This file is part of patterns, a plugin for Wordpress.
 *
 **/

require_once PATTERNS_PLUGIN_DIR . '/includes/patterns.helpers.php';


class patterns_options
{

   public $debug_log_enabled = FALSE;
   
   private $result_msg = '';
   
   /**
    * Instansiate Custom Post Type class
    *
    * @var string
    * @access private
   **/
   public $patterns_type;
   
   // ------------------------------------------------------------

   public function __construct()
   {
      // Registers the admin menu with WordPress
      add_action('admin_menu', array(&$this, 'admin_menu'));
   }

   // ------------------------------------------------------------

   /**
    * Registers the admin menu with WordPress
    */
   public function admin_menu()
   {
      add_options_page(
         'Patterns Options', 
         'Patterns', 
         'manage_options', 
         'patterns-options', 
         array(&$this, 'settings_page')
      );
   }

   // ------------------------------------------------------------

   /**
    * Implements the Settings page
    */
   public function settings_page()
   {
      $theme_path = get_template_directory().'/patterns';
      
      if ( ! current_user_can('manage_options'))
      {
         wp_die( __('You do not have sufficient permissions to access this page.'));
      }
      
      $options = array();
      
      $options['use_groups'] = (get_option('patterns_use_groups') != '') ? get_option('patterns_use_groups') : '1';
      $options['restrict_to_categories'] = (get_option('patterns_restrict_to_categories') != '') ? get_option('patterns_restrict_to_categories') : '';
      $options['alias_category'] = (get_option('patterns_alias_category') != '') ? get_option('patterns_alias_category') : '1';
      $options['templates'] = (get_option('patterns_templates') != '') ? get_option('patterns_templates') : '1';

      
      // See if the user has posted us some information
      $updated = FALSE;
      if (isset($_POST['update_settings']) && $_POST['update_settings'] == 'Y')
      {
         if ( ! isset($_POST['alias_category']))
         {
            $_POST['alias_category'] = '0';
         }
            
         if ( ! isset($_POST['templates']))
         {
            $_POST['templates'] = '0';
         }
            
         foreach ($options AS $key => $value)
         {
            // Read their posted value
            $options[$key] = $_POST[$key];
            
            // double-check that the template directory exists
            if ($key == 'patterns_templates' && $options[$key] == 1 && ! is_dir($theme_path))
            {
               $options['patterns_templates'] = '0';
               $this->set_result_msg('patterns theme templates can\'t be used because they don\'t exist.', 'error');
			}

            // Save the posted value in the database
            update_option('patterns_'.$key, $options[$key]);

            // Put an settings updated message on the screen
            $updated = TRUE;
         }
      }
      include PATTERNS_PLUGIN_DIR . '/includes/settings.php';
      
   }

   // ------------------------------------------------------------

   /**
    * Set a Result Message
    *
    * @access public
    * @return void
   **/
   public function set_result_msg($msg, $type)
   {
      if ($type == 'error')
      {
         $this->result_msg[] = '<div class="error">'.$msg.'</div>';
      }
      elseif ($type == 'warning')
      {
         $this->result_msg[] = '<div class="warning">'.$msg.'</div>';
      }
      else
      {
         $this->result_msg[] = '<div class="notice">'.$msg.'</div>';
      }
   }
	
  // ------------------------------------------------------------

   /**
    * Log a debug message
    *
    * @access public
    * @return void
   **/
   public function debug_log($msg)
   {
      if ($this->debug_log_enabled)
      {
         array_push($this->debug_log, date("Y-m-d H:i:s") . " " . $msg);
      }
   }
	
   // ------------------------------------------------------------

   /**
    * Save the error log if it's enabled.  Must be called before server code exits to preserve
    * any log messages recorded during session.
    *
    * @access public
    * @return void
   **/
   public function save_debug_log()
   {
      if ($this->debug_log_enabled)
      {
         $options = get_option('patterns_plugin_settings');
         $options['debug_log'] = $this->debug_log;
         update_option('patterns_plugin_settings', $options);
      }
   }
	
   // ------------------------------------------------------------

   /**
    * Log errors to server log and debug log
    *
    * @access public
    * @return void
   **/
   public function error_log($msg)
   {
      error_log(HCGPRODUCT_PLUGIN_NAME . ": " . $msg);
      $this->debug_log($msg);
   }

}  // End Class hcgStores_options