<div class="wrap">
   <?php screen_icon('options-general'); ?> <h2>Patterns Settings</h2>
   
   <?php if ($updated == TRUE): ?>
   <div id="message" class="updated">Settings saved</div>
   <?php endif;?>
   
   <?php if ( ! empty($this->result_msg)): ?>
      <?php foreach ($this->result_msg AS $msg): ?>
         <?=$msg;?>
      <?php endforeach; ?>
   <?php endif; ?>

   <form method="post" action="">

      <table class="form-table">
      
      <tr valign="top">
      <th scope="row">
         <label for="site_id">Pattern groups</label>
      </th>
      <td>
         <input type="radio" id="edit-patterns-use-groups-true" name="use_groups" value="1" <?php checked('1', $options['use_groups']); ?> class="form-radio" />  <label class="option" for="edit-patterns-use-groups-true">Group different sizes of the same product under the master record. </label>
         <br /><input type="radio" id="edit-patterns-use-groups-false" name="use_groups" value="0" <?php checked('0', $options['use_groups']); ?> class="form-radio" />  <label class="option" for="edit-patterns-use-groups-false">Treat different sizes of the same product as individual products. </label>
         <p class="description">Define how you want to handle cases where there are multiple sizes of the same product.</p>
      </td>
      </tr>
      
      <tr valign="top">
      <th scope="row">
         <label for="edit-patterns-restrict-to-categories">Restrict sync to the following category codes</label>
      </th>
      <td>
 <input type="text" id="edit-patterns-restrict-to-categories" name="restrict_to_categories" value="<?=$options['restrict_to_categories'];?>" size="60" maxlength="128" class="form-text" />
 <p class="description">Comma separated (no spaces).  Leave blank to import all. Please save changes before doing a sync.</p>
      </td>
      </tr>

      <tr valign="top">
      <th scope="row">
         <label for="search_tpl">Categories in URLs</label>
      </th>
      <td>
         <input type="checkbox" id="edit-patterns-alias-category" name="alias_category" value="1" <?php checked('1', $options['alias_category']); ?> class="form-checkbox" />  <label class="option" for="edit-patterns-alias-category">Include category name in URL aliases </label>
         <p class="description">Please save changes before doing a sync.</p>
      </td>
      </tr>

      <tr valign="top">
      <th scope="row">
         <label for="search_tpl">Category and Product Templates</label>
      </th>
      <td>
         <input type="checkbox" id="edit-patterns-templates" name="templates" value="1" <?php checked('1', $options['templates']); ?> class="form-checkbox" />  <label class="option" for="edit-patterns-templates">Use default templates if not found in theme </label>
      </td>
      </tr>

      </table>

      <input type="hidden" name="update_settings" value="Y" />
      
      <p><input type="submit" name="Submit" value="Save Changes" class="button button-primary" /></p>
   </form>
</div>
