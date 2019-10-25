<?php
/**
 * @package Patterns
 * @author Jim Applegate <jimappleg8@gmail.com>
 * @version 0.1beta
 */
/*
Plugin Name: Patterns
Plugin URI: https://github.com/jimappleg8/patterns-plugin
Description: A plugin to implement pattern languages and patterns in Wordpress.
Version: 0.1beta
Author: Jim Applegate
Author URI: https://www.jimapplegate.com/
*/

// =======================================
// = Define constants used by the plugin =
// =======================================

if ( ! defined('PATTERNS_THEME_DIR'))
   define('PATTERNS_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if ( ! defined('PATTERNS_PLUGIN_NAME'))
   define('PATTERNS_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if ( ! defined('PATTERNS_PLUGIN_DIR'))
   define('PATTERNS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . PATTERNS_PLUGIN_NAME);

if ( ! defined('PATTERNS_PLUGIN_URL'))
   define('PATTERNS_PLUGIN_URL', WP_PLUGIN_URL . '/' . PATTERNS_PLUGIN_NAME);

if ( ! defined('PATTERNS_VERSION_KEY'))
   define('PATTERNS_VERSION_KEY', 'patterns_version');

if ( ! defined('PATTERNS_VERSION_NUM'))
   define('PATTERNS_VERSION_NUM', '0.1beta');

if ( ! defined('PATTERNS_DB_VERSION_NUM'))
   define('PATTERNS_DB_VERSION_NUM', '1.0');

add_option(PATTERNS_VERSION_KEY, PATTERNS_VERSION_NUM);

// The version constants above allow me to check the version in future
// upgrades and act accordingly. Sample code is below for when I need it.
// http://wp.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
/*
$new_version = '2.0';

if (get_option(PATTERNS_VERSION_KEY) != $new_version)
{
   // Execute your upgrade logic here

   // Then update the version value
   update_option(PATTERNS_VERSION_KEY, $new_version);
}
*/

// ================================
// = Include libries and handlers =
// ================================

// Include admin portion of plugin
if ((include_once PATTERNS_PLUGIN_DIR . '/admin/admin.php') == FALSE)
{
   patterns_error_log("Unable to load admin/admin.php");
   return;
}

// Include Custom Post Type portion of plugin
if ((include_once PATTERNS_PLUGIN_DIR . '/includes/patterns.type.php') == FALSE)
{
   patterns_error_log("Unable to load includes/patterns.type.php");
   return;
}

// Include Summary class portion of plugin
if ((include_once PATTERNS_PLUGIN_DIR . '/includes/summary.class.php') == FALSE)
{
   patterns_error_log("Unable to load includes/summary.class.php");
   return;
}

// =================================
// = Define the patterns_plugin class =
// =================================

class patterns_plugin {

   /**
    * Instansiate Custom Post Type class
    *
    * @var string
    * @access private
   **/
   var $patterns_type;

   /**
    * Instansiate option class - provides access to plugin options and debug logging
    *
    * @var string
    * @access private
   **/
   var $patterns_admin;

   // ------------------------------------------------------------

   public function __construct()
   {
      // Create the Custom Post Type
      $this->patterns_type = new patterns_type();

      // Retrieve plugin options
      $this->patterns_admin = new patterns_options();
      $this->patterns_admin->patterns_type = $this->patterns_type;
      
      // register style sheets
      add_action('wp_enqueue_scripts', array(&$this, 'register_styles'), 999);
      
      // Checks if the database needs to be updated
      add_action('plugins_loaded', array(&$this, 'update_db_check'));
      
      // Removes the New Product menu item from admin menu
      add_action('admin_menu', array(&$this, 'remove_submenu_links'), 999);
      
      // Removes "Products" from the +New menu at the top of the admin
      add_action('wp_before_admin_bar_render', array(&$this, 'remove_admin_bar_links'));
      
      // Hides the "Add New" buttons in the admin headings
//      add_action('admin_head', array(&$this, 'remove_admin_head_links'));
      
      // Adds a settings link to the Plugins install page
      add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
      
      // includes the default templates if needed
      add_filter('template_include', array(&$this, 'set_template'));
      
      // activate the summary shortcode
      add_shortcode('patternsummary', array(&$this, 'summary_shortcode'));

      // activate the plink shortcode
      add_shortcode('plink', array(&$this, 'plink_shortcode'));

   }

   // ------------------------------------------------------------

   /**
    * Activate the plugin
    * 
    * Reference: http://codex.wordpress.org/Creating_Tables_with_Plugins
    */
   public static function activate()
   {
      global $wpdb;
      
      // ----------------------------------------------
      // create default options
      // ----------------------------------------------

      add_option('patterns_alias_category', '1');
      add_option('patterns_db_version', PATTERNS_DB_VERSION_NUM);
      add_option('patterns_restrict_to_categories', '');
      add_option('patterns_use_groups', '1');
      add_option('patterns_version', PATTERNS_VERSION_NUM);
      
      // ----------------------------------------------
      // copy template files into the current theme directory
      // ----------------------------------------------

      $template_path = PATTERNS_PLUGIN_DIR.'/templates';
      $theme_path = get_template_directory().'/patterns';
      
      if ( ! file_exists($theme_path))
      {
         mkdir($theme_path, 0777, true);
      }
      
      patterns_plugin::copy_templates($template_path, $theme_path);

   }

   // ------------------------------------------------------------

   /**
    * Deactivate the plugin
    */     
   public static function deactivate()
   {
      global $wpdb;
      
      // ----------------------------------------------
      // remove options
      // ----------------------------------------------

      delete_option('patterns_alias_category');
      delete_option('patterns_db_version');
      delete_option('patterns_restrict_to_categories');
      delete_option('patterns_use_groups');
      delete_option('patterns_version');
      delete_option('patterns-categories_children');

      // ----------------------------------------------
      // leave the template files in the theme directory
      // ----------------------------------------------

      // ----------------------------------------------
      // TODO: delete patterns from the database
      // ----------------------------------------------

      // I might want to add code to delete the pattern entries
      //  and pattern-categories entries in the database.
      //  This is tricker than it seems:
      //  https://wordpress.org/support/topic/deleting-post-revisions-do-not-use-the-abc-join-code-you-see-everywhere
      
//      wp_delete_post( $postid, $force_delete );
      
   }

   // ------------------------------------------------------------

   /**
    * Checks, each time the plugin loads, whether the database used by 
    *  this plugin needs to updated.
    * 
    * Reference: http://codex.wordpress.org/Creating_Tables_with_Plugins
    */     
   public function update_db_check()
   {
      global $jal_db_version;
      if (get_site_option('patterns_db_version') != PATTERNS_DB_VERSION_NUM)
      {
         $this->activate();
      }
   }

   // ------------------------------------------------------------

   /**
    * Registers the needed CSS styles with WordPress
    *
    */
   public function register_styles()
   {
      wp_register_style('patterns-styles', PATTERNS_PLUGIN_URL.'/css/patterns.css');
      wp_enqueue_style('patterns-styles');
   }

   // ------------------------------------------------------------

   /**
    * Adds a settings link to the Plugins install page
    */
   public function plugin_action_links($links, $file)
   {
      static $this_plugin;

      if ( ! $this_plugin)
      {
         $this_plugin = plugin_basename(__FILE__);
      }

      if ($file == $this_plugin)
      {
         $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=patterns-options">Settings</a>';
         array_unshift($links, $settings_link);
      }

      return $links;
   }

   // ------------------------------------------------------------

   /**
    * Removes the Add New Pattern menu from admin
    */
   public function remove_submenu_links()
   {
      $page = remove_submenu_page('edit.php?post_type=patterns', 'post-new.php?post_type=patterns');
   }

   // ------------------------------------------------------------

   /**
    * Removes "Patterns" from the +New menu at the top of the admin
    */
   public function remove_admin_bar_links()
   {
      global $wp_admin_bar;
      $wp_admin_bar->remove_menu('new-patterns');
   }

   // ------------------------------------------------------------

   /**
    * Hides the "Add New" buttons in the admin headings
    */
   public function remove_admin_head_links()
   {
      if (get_post_type() == 'patterns')
      {
         echo '<style type="text/css">
  #favorite-actions {display: none;}
  .add-new-h2 {display: none;}
  .tablenav {display: none;}
</style>';
      }
   }

   // ------------------------------------------------------------

   /**
    * Set template
    *
    * This is a filter for the WP template_include() function
    *
    * Checks if WP is loading a template for a patterns post
    * type and will try to load the template from the correct
    * directory. It uses locate_template to determine if there
    * is a copy of the template in the themes folder.
    *
    */   
   public function set_template( $template )
   {
      $file = '';
      
      if ( is_singular( 'patterns' ) ) :
         $file = 'single-patterns.php';
      endif;
      
      if ( $file ) :
         if ( file_exists( $this->locate_template( $file ) ) ) :
            $template = $this->locate_template( $file );
         endif;
      endif;
      
      return $template;
   }

   // ------------------------------------------------------------

   /**
    * Locate template.
    *
    * https://jeroensormani.com/how-to-add-template-files-in-your-plugin/
    * @author Jeroen Sormani
    *
    * Locate the called template.
    * Search Order:
    * 1. /themes/theme/patterns/$template_name
    * 2. /plugins/patterns/templates/$template_name
    *
    * @since 1.0.0
    *
    * @param    string 	 $template_name         Template to load.
    * @param    string 	 $template_path         Path to templates.
    * @param    string	 $default_path          Default path to template files.
    * @return   string                          Path to the template file.
    */
   private function locate_template( $template_name, $template_path = '', $default_path = '' ) 
   {
      // Set variable to search in woocommerce-plugin-templates folder of theme.
      if ( ! $template_path ) :
         $template_path = get_template_directory() . '/patterns/';
      endif;

      // Set default plugin templates path.
      if ( ! $default_path ) :
         $default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder
      endif;

      // Search template file in theme folder.
      $template = locate_template( array(
         $template_path . $template_name,
         $template_name
      ) );

      // Get plugins template file.
      if ( ! $template ) :
         $template = $default_path . $template_name;
      endif;

      return apply_filters( 'patterns_locate_template', $template, $template_name, $template_path, $default_path );
   }

   // ------------------------------------------------------------

   /**
    * Copies the builtin template files to the active WordPress theme
    *
    * Handles copying the builting template files to the patterns/ 
    * directory of the currently active WordPress theme.  Strips out the 
    * header comment block which includes a warning about editing the 
    * builtin templates.
    *
    * Copied from the Shopp Wordpress plugin: https://shopplugin.net/
    *
    * @author Jonathan Davis, John Dillick
    *
    * @param    string  $src    The source directory for the builtin template files
    * @param    string  $target The target directory in the active theme
    * @return   void
    */
   public function copy_templates($src, $target)
   {
      $builtin = array_filter(scandir($src), 'patterns_plugin::filter_dotfiles');
      foreach ($builtin as $template)
      {
         $target_file = $target.'/'.$template;
         if ( ! file_exists($target_file))
         {
            $src_file = file_get_contents($src . '/' . $template);
            $file = fopen($target_file, 'w');
            $src_file = preg_replace('/^<\?php\s\/\*\*\s+(.*?\s)*?\*\*\/\s\?>\s/', '', $src_file); // strip warning comments

            fwrite($file, $src_file);
            fclose($file);
            chmod($target_file, 0666);
        }
      }
   }

   // ------------------------------------------------------------

   /**
    * Callback to filter out files beginning with a dot
    *
    * Copied from the Shopp Wordpress plugin: https://shopplugin.net/
    
    * @author Jonathan Davis
    *
    * @param string $name The filename to check
    * @return boolean
    */
   public static function filter_dotfiles($name)
   {
      return (substr($name,0,1) != ".");
   }

   // ------------------------------------------------------------

   /**
    * Implements the [patternsummary language="category"] shortcode
    *
    */
   public function summary_shortcode($atts, $content = null)
   {
      $a = shortcode_atts( array(
         'language' => 'something',
      ), $atts );

      if (!is_array($a) || !isset($a['language']))
      {
         return '<!-- Missing pattern language ID -->';
      } 
      elseif (!is_string($a['language']))
      {
         return '<!-- Invalid pattern language ID -->';
      }
      
      // get category structure of this language
      $group = get_terms( array(
         'taxonomy' => 'pattern-groups',
         'name' => $a['language'],
         'hide_empty' => false,
      ) );
      
      if (!$group[0] instanceof WP_Term)
      {
         return '<!-- No pattern language found -->';
      }

      $summary_generator = new Patterns_SummaryGenerator(
         __DIR__ . '/templates/pattern_language_summary.php',
         'patterns_language_summary_template_path',
         'pattern-language-summary'
      );
 
      return $summary_generator->generate($group);
   }

   // ------------------------------------------------------------

   /**
    * Implements the [plink]Pattern Title[/plink] shortcode
    *
    */
   public function plink_shortcode($atts = null, $content)
   {
      if (!isset($content))
      {
         return '<!-- Missing pattern ID -->';
      } 
      elseif (!is_string($content))
      {
         return '<!-- Invalid pattern ID -->';
      }
      
      // search existing patterns for the title
      $pattern = get_page_by_title(trim($content), OBJECT, 'patterns');
      
      if (! $pattern instanceof WP_Post)
      {
         return '<span class="pattern-name" style="color:red;">' . $content.'</span>';
      }
      
      $link = '<a href="'.get_permalink($pattern).'" class="pattern-name">'.get_the_title($pattern).'</a>';

      return $link;
   }



} /* end patterns_plugin class */


// ------------------------------------------------------------

function patterns_error_log($msg)
{
	global $patterns_errors;

	if ( ! is_array( $patterns_errors ) ) {
		add_action('admin_footer', 'patterns_error_log_display');
		$patterns_errors = array();
	}
	
	array_push($patterns_errors, PATTERNS_PLUGIN_NAME . $msg);
}

// ------------------------------------------------------------

/**
 * Display errors logged when the plugin options module is not available.
 */
function patterns_error_log_display()
{
	echo "<div class='error'><p><a href='options-media.php'>" . PATTERNS_PLUGIN_NAME 
		. "</a> unable to initialize correctly.  Error(s):<br />";
	foreach ($patterns_errors as $line) {
		echo "$line<br/>\n";
	}
	echo "</p></div>";
}

// =========================
// = Plugin initialization =
// =========================

// Installation and uninstallation hooks
register_activation_hook(__FILE__, array('patterns_plugin', 'activate'));
register_deactivation_hook(__FILE__, array('patterns_plugin', 'deactivate'));

// Instantiate the plugin class
$patterns = new patterns_plugin();

