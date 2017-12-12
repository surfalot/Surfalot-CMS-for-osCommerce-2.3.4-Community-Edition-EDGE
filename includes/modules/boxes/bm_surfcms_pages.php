<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  class bm_surfcms_pages {
    var $code = 'bm_surfcms_pages';
    var $group = 'boxes';
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;

    function __construct() {
      $this->title = MODULE_BOXES_SURFCMS_PAGES_TITLE;
      $this->description = MODULE_BOXES_SURFCMS_PAGES_DESCRIPTION;

      if ( defined('MODULE_BOXES_SURFCMS_PAGES_STATUS') ) {
        $this->sort_order = MODULE_BOXES_SURFCMS_PAGES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_SURFCMS_PAGES_STATUS == 'True');

        $this->group = ((MODULE_BOXES_SURFCMS_PAGES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    function execute() {
      global $languages_id, $oscTemplate, $surfcms;
	  
	  if ( isset($surfcms) && is_object($surfcms) && (get_class($surfcms) == 'surfcms') ) {
	    $nav_array = $surfcms->getNavigation('box');
	  }

	  if (is_array($nav_array) && sizeof($nav_array) > 0) {
		  
	    ob_start();
        include('includes/modules/boxes/templates/surfcms_pages.php');
        $data = ob_get_clean();
		
        $oscTemplate->addBlock($data, $this->group, $this->sort_order);
		
	  }
    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_BOXES_SURFCMS_PAGES_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Surfalot CMS Module', 'MODULE_BOXES_SURFCMS_PAGES_STATUS', 'True', 'Do you want to add the module to your shop?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Content Placement', 'MODULE_BOXES_SURFCMS_PAGES_CONTENT_PLACEMENT', 'Left Column', 'Should the module be loaded in the left or right column?', '6', '1', 'tep_cfg_select_option(array(\'Left Column\', \'Right Column\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_BOXES_SURFCMS_PAGES_SORT_ORDER', '3000', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_BOXES_SURFCMS_PAGES_STATUS', 'MODULE_BOXES_SURFCMS_PAGES_CONTENT_PLACEMENT', 'MODULE_BOXES_SURFCMS_PAGES_SORT_ORDER');
    }
  }
?>
