<?php
// $Id$

/**
 * @file
 * Shared functions for the patterns plugin
 *
 */

// ------------------------------------------------------------------------

/**
 * Entities to ASCII
 *
 * Converts character entities back to ASCII
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	string
 */	
function _patterns_entities_to_ascii($str, $all = TRUE)
{
  if (preg_match_all('/\&#(\d+)\;/', $str, $matches)) {

    for ($i = 0, $s = count($matches['0']); $i < $s; $i++)
    {
      $digits = $matches['1'][$i];
      $out = '';

      if ($digits < 128) {
           $out .= chr($digits);
      }
      elseif ($digits < 2048) {
           $out .= chr(192 + (($digits - ($digits % 64)) / 64));
           $out .= chr(128 + ($digits % 64));
      }
      else {
           $out .= chr(224 + (($digits - ($digits % 4096)) / 4096));
           $out .= chr(128 + ((($digits % 4096) - ($digits % 64)) / 64));
           $out .= chr(128 + ($digits % 64));
      }
      $str = str_replace($matches['0'][$i], $out, $str);        
    }
  }

  if ($all) {
    $str = str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;","&trade;"), array("&","<",">","\"", "'", "-", "�"), $str);
  }
  return $str;
}

// ------------------------------------------------------------------------

/**
 * Get Control
 *
 * Simplifies the creation of form elements
 *
 * @access	public
 * @param	string     the element type
 * @param	string     the element label
 * @param	string     the element id
 * @param	string     the element value
 * @param	string     the element data (e.g. for option lists)
 * @param	string     the element info (instructions for element)
 * @param	string     the element style
 * @return	string
 */	
function patterns_get_control($type, $label, $id, $name, $value = '',  $data = null, $info = '', $style = 'input widefat')
{
   $output = '<p>';
   switch($type) {
      case 'hidden':
         return '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="'.$style.'">';
         break;
      case 'text':
         $output .= '<label for="'.$name.'">'.$label.'</label>:';
         $output .= '<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="'.$style.'">';
         break;
      case 'checkbox':
         $output .= '<input type="checkbox" id="'.$id.'" name="'.$name.'" value="1" class="input" '.checked($value, 1, false).' />';
         $output .= '<label for="'.$name.'">'.$label.'</label>';			
         break;	
      case 'textarea':
         $output .= '<label for="'.$name.'">'.$label.'</label>:<br />';
         $output .= '<textarea id="'.$id.'" name="'.$name.'" class="'.$style.'" style="height: 100px;">'.$value.'</textarea>';			
         break;
      case 'textarea-big':
         $output .= '<label for="'.$name.'">'.$label.'</label>:<br />';
         $output .= '<textarea id="'.$id.'" name="'.$name.'" class="'.$style.'" style="height: 300px;">'.$value.'</textarea>';			
         break;
      case 'select':
         $output .= '<label for="'.$name.'">'.$label.'</label>:';
         $output .= '<select id="'.$id.'" name="'.$name.'" class="'.$style.'">';
         if ($data)
         {
            foreach($data as $option)
            {
               $output .= '<option value="'.$option['value'].'" '.selected($value, $option['value'], false).'>'.$option['text'].'</option>';
            }
         }
         $output .= '</select>';
         break;
      case 'upload':
         $output .= '<label for="'.$name.'">'.$label.'</label>:<br />';
         $output .= '<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="'.$style.'" style="width: 74%;" />';
         $output .= '<input type="button" value="Upload Image" class="xa_secure_uploader_button" id="upload_image_button" style="width: 25%;" />';
         break;
      case 'multiselect':
         $output .= '<label for="'.$name.'">'.$label.'</label>:<br />';
         $output .= '<select id="'.$id.'" name="'.$name.'" class="'.$style.'" multiple="multiple" style="height: 220px">';
         if ($data)
         {
            foreach($data as $option)
            {
               if (is_array($value) && in_array($option['value'], $value))
               {
                  $output .= '<option value="'.$option['value'].'" selected="selected">'.$option['text'].'</option>';
               }
               else
               {
                  $output .= '<option value="'.$option['value'].'">'.$option['text'].'</option>';
               }
            }
         }
         $output .= '</select>';
         break;
   }
   if ($info != '')
   {
      $output .= '<small>'.$info.'</small>';
   }
   $output .= '</p>';
   return $output;
}
