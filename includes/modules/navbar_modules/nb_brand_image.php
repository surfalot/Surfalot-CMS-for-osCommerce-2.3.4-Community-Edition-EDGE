<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions 
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class nb_brand_image {
    var $code = 'nb_brand_image';
    var $group = 'navbar_modules_home';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;    
    
    function __construct() {
      $this->title = MODULE_NAVBAR_BRAND_IMAGE_TITLE;
      $this->description = MODULE_NAVBAR_BRAND_IMAGE_DESCRIPTION;

      if ( defined('MODULE_NAVBAR_BRAND_IMAGE_STATUS') ) {
        $this->sort_order = MODULE_NAVBAR_BRAND_IMAGE_SORT_ORDER;
        $this->enabled = (MODULE_NAVBAR_BRAND_IMAGE_STATUS == 'True');
        
        switch (MODULE_NAVBAR_BRAND_IMAGE_CONTENT_PLACEMENT) {
          case 'Home':
          $this->group = 'navbar_modules_home';
          break;
          case 'Left':
          $this->group = 'navbar_modules_left';
          break;
          case 'Right':
          $this->group = 'navbar_modules_right';
          break;
        } 
      }
    }

    function getOutput() {
      global $oscTemplate;
      
      ob_start();
      require('includes/modules/navbar_modules/templates/brand_image.php');
      $data = ob_get_clean();

      $oscTemplate->addBlock($data, $this->group, $this->sort_order);
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_NAVBAR_BRAND_IMAGE_STATUS');
    }

    function install() {
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Brand Logo Module', 'MODULE_NAVBAR_BRAND_IMAGE_STATUS', 'True', 'Do you want to add the module to your Navbar?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Image File Name', 'MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME', '".STORE_LOGO."', 'The image name of your brand logo. Upload the file to root of your images folder of your shop. FYI: The standard Navbar height is 50 pixels.', '6', '2', '', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_NAVBAR_BRAND_IMAGE_CONTENT_PLACEMENT', 'Home', 'This module must be placed in the Home area of the Navbar.', '6', '3', 'tep_cfg_select_option(array(\'Home\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_NAVBAR_BRAND_IMAGE_SORT_ORDER', '505', 'Sort order of display. Lowest is displayed first.', '6', '4', now())");
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_NAVBAR_BRAND_IMAGE_STATUS', 'MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME', 'MODULE_NAVBAR_BRAND_IMAGE_CONTENT_PLACEMENT', 'MODULE_NAVBAR_BRAND_IMAGE_SORT_ORDER');
    }
  }
  