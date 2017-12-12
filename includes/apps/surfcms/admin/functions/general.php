<?php /*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License

*/

  // return all $_get as hidden fields for a form
  if ( !function_exists('tep_surfcms_get_all_get_params_as_hidden') ) { 
    function tep_surfcms_get_all_get_params_as_hidden($excepts) {
      $return = '';
      $get_params = trim(tep_get_all_get_params($excepts),'&');
      $get_params_array = explode('&',$get_params);
      foreach ($get_params_array as $getpair) {
	    $getvar = explode('=',$getpair);
	    $return .= tep_draw_hidden_field($getvar[0],$getvar[1]);
      }
      return $return;
    }
  }


  // ////////////////////////////////////////////////////////////////////////////
  // Checkbox configuration values in the Administration config utility
  // ## set_function ##
  if ( !function_exists('tep_cfg_checkbox_option') ) {
    function tep_cfg_checkbox_option($init, $key_value, $key = '') {
        
      $name = (tep_not_null($key) ? 'configuration[' . $key . ']' : 'configuration_value');
      $value = (tep_not_null($key_value) ? $key_value : $init);
      $string = '<input type="hidden" name="' . $name . '" value="' . $value . '"><input type="checkbox"';
      if ( $value == 'True' ) 
          $string .= ' checked="checked"';
      $string .= ' onclick="if(this.checked){this.previousSibling.value=\'True\'}else{this.previousSibling.value=\'False\'}">';
  
      return $string;
    }
  
  }
  
  // /////////////////////////////////////////////////////////////////////////////////
  // for potential future use_function Administration config utility
  // ## use_function ##
  if ( !function_exists('tep_surfcms_configure_functions_stub') ) {
    function tep_surfcms_configure_functions_stub($action){        
      $return = null;
      
      switch ($action){
        case 'opt1':
          tep_db_query("UPDATE `configuration` SET `configuration_value` = '' WHERE `configuration_key` = 'SURFCMS_APP_'");
          $return = 'item1';
          break;
            
        case 'opt2':
          tep_db_query("UPDATE `configuration` SET `configuration_value` = '' WHERE `configuration_key` = 'SURFCMS_APP_'");
          $return = 'item2';
          break;
            
        default:
          $return = 'item3';
          break;
      }
      // return the value to 
      // be set in configuration:
      return $return;
    }
  }


?>