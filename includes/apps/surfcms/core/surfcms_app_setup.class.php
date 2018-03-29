<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/


  // /////////////////////////////////////////////////////
  // App Setup Singleton Class
  //
  final class surfcms_app_setup {
	  
    private static $instance = null;

    // /////////////////////////////////////////////////////
	// construct / destruct
	//
    protected function __construct() {} 
    public function __destruct() {} 
	
    // /////////////////////////////////////////////////////
    // Privatize Magic Methods
    //
    // no cloning
    private function __clone() {} 
    // no serialize
    private function __sleep() {}
    private function __wakeup() {} 
	
    // /////////////////////////////////////////////////////
    // Singleton instance
    //
    public static function i() {
     if ( !self::$instance instanceof surfcms_setup ) {
       self::$instance = new self;
     }
     return self::$instance;
    }
	
    // /////////////////////////////////////////////////////
    // isInstalled()
    //
    public function isInstalled() {
		
      $surfcms_config_query = tep_db_query("SELECT `configuration_value` as vers FROM `configuration` WHERE `configuration_key` = 'SURFCMS_VERSION_INSTALLED'");

      if ($surfcms_config = tep_db_fetch_array($surfcms_config_query)) {
        return $surfcms_config['vers'];
      }
		
      return false;
    }
	
    // /////////////////////////////////////////////////////
    // Class Config Functions
    //
	public function installDatabase($new_version) {
		
	  $this->createTables();
	  $this->insertConfiguration($new_version);
		
      return true;
	}
	
    public function uninstallDatabase() {
		
      $this->dropTables();
      $this->deleteConfiguration();
		
      return true;
	}

	public function updateDatabase($new_version) {
		
      $this->updateVersion($new_version);
		
      return true;
	}

    public function resetConfiguration() {
		
      if ($vers = $this->isInstalled()) {
	    $this->deleteConfiguration();
        $this->insertConfiguration($vers);
	  }
		
      return true;
    }
	
	public function installSampleMenu() {
		
	  $this->insertSampleMenu();
		
      return true;
	}
	

    // /////////////////////////////////////////////////////
    // createTables
    //
    private function createTables() {

      $sql = "
        CREATE TABLE `surfcms_content` (
          `surfcms_content_id` int(11) NOT NULL auto_increment,
          `surfcms_content_group` varchar(64) NOT NULL default '',
          `surfcms_content_type` tinyint(1) NOT NULL default '0',
          `surfcms_content_url` varchar(192) NOT NULL default '',
          `surfcms_content_menu_parent` int(11) NOT NULL default '0',
          `surfcms_content_menu_icon` varchar(40) NOT NULL default '',
          `surfcms_content_width` tinyint(1) NOT NULL default '0',
          `surfcms_content_sort_order` int(11) NOT NULL default '0',
          `surfcms_content_show_in_nav` tinyint(1) NOT NULL default '0',
          `surfcms_content_show_in_box` tinyint(1) NOT NULL default '0',
          `surfcms_content_administrators` varchar(64) NOT NULL default '',
          `surfcms_content_status` tinyint(1) NOT NULL default '0',
          PRIMARY KEY  (`surfcms_content_id`),
          KEY `surfcms_content_group` (`surfcms_content_group`),
          KEY `surfcms_content_type` (`surfcms_content_type`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
      tep_db_query($sql);

      $sql = "
        CREATE TABLE `surfcms_content_description` (
          `surfcms_content_id` int(11) NOT NULL default '0',
          `language_id` int(11) NOT NULL default '0',
          `surfcms_content_menu_text` varchar(64) NOT NULL default '',
          `surfcms_content_title` varchar(128) NOT NULL default '',
          `surfcms_content_body` text NOT NULL,
          `surfcms_content_navbar_title` varchar(64) NOT NULL default '',
          `surfcms_content_meta_title` varchar(128) NOT NULL default '',
          `surfcms_content_meta_description` varchar(255) NOT NULL default '',
          `surfcms_content_meta_keywords` varchar(255) NOT NULL default '',
          PRIMARY KEY  (`surfcms_content_id`,`language_id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
      tep_db_query($sql);
      
      return true;
    }

    // /////////////////////////////////////////////////////
    // dropTables
    //
    private function dropTables() {

      tep_db_query("DROP TABLE `surfcms_content`");
      tep_db_query("DROP TABLE `surfcms_content_description`");
      
      return true;
    }
    
    // /////////////////////////////////////////////////////
    // keys
    //
    private function keys() {
      return array('SURFCMS_VERSION_INSTALLED', 
	               'SURFCMS_CONFIGURATION_GROUP', 
				   'SURFCMS_MODULE_CONTENT_ADMIN_GROUP', 
				   'SURFCMS_MODULE_CONTENT_UTILITY_GROUP', 
				   'SURFCMS_MODULE_CONTENT_LIST_GROUP', 
				   'SURFCMS_MODULE_CONTENT_INSTALLED', 
				   'SURFCMS_DISPLAY_MENU_SAMPLE_BOX', 
				   'SURFCMS_DISPLAY_CONTENT_GROUP_BOX', 
				   'SURFCMS_DISPLAY_BLOCK_GROUP_BOX', 
				   'SURFCMS_ENABLE_MENU_LINKS', 
				   'SURFCMS_ENABLE_BLOCK_GROUP_LINKS', 
				   'SURFCMS_ENABLE_BLOCK_LINK', 
				   'SURFCMS_ENABLE_CONTENT_GROUP_LINKS', 
				   'SURFCMS_ENABLE_WYSIWYG', 
				   'SURFCMS_BOX_TITLE', 
				   'SURFCMS_BOX_STYLE');
    }
    
    // /////////////////////////////////////////////////////
    // insertConfiguration
    //
    private function insertConfiguration($version) {

      // insert SurfCMS Database Version into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS Version Installed', 'SURFCMS_VERSION_INSTALLED', '" . $version . "', 'Surfalot CMS version number that is currently installed.', '6', (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      // insert SurfCMS Configuration Group
      tep_db_query("INSERT INTO `configuration_group` (`configuration_group_title`, `configuration_group_description`, `sort_order`) VALUES ('Surfalot CMS', 'Surfalot CMS App Settings', (SELECT MAX( `sort_order` )+1 FROM `configuration_group` sort) );");
      $group_id = tep_db_insert_id();
      
      // insert SurfCMS Configuration Group ID into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS Configuration Group ID', 'SURFCMS_CONFIGURATION_GROUP', '" . $group_id . "', 'Surfalot CMS Configuration Group ID that config items are installed.', '6', (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      // insert SurfCMS modules List into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS Installed Modules', 'SURFCMS_MODULE_CONTENT_INSTALLED', '', 'This is automatically updated. No need to edit.', 6, (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      // insert SurfCMS content admin group into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS Content Admin Group', 'SURFCMS_MODULE_CONTENT_ADMIN_GROUP', '', 'This is automatically updated. No need to edit.', 6, (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      // insert SurfCMS content utility group into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS Utility Admin Group', 'SURFCMS_MODULE_CONTENT_UTILITY_GROUP', '', 'This is automatically updated. No need to edit.', 6, (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      // insert SurfCMS content list group into group 6
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('SurfCMS List Admin Group', 'SURFCMS_MODULE_CONTENT_LIST_GROUP', '', 'This is automatically updated. No need to edit.', 6, (SELECT MAX( `sort_order` )+1 FROM `configuration` sort), now());");

      $sort = 1;
      // insert SurfCMS General Configuration Items
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('<span style=\"color:#00f;\">Box Heading Name</span>', 'SURFCMS_BOX_TITLE', 'Information', 'Title of sidebar box.', '" . $group_id . "', '" . $sort++ . "', now());");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('<span style=\"color:#00f;\">Box Style</span>', 'SURFCMS_BOX_STYLE', 'bubble', 'Style of the sidebar box. Bubble is uses the same styles as the Categories box. List uses a basic list format.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('bubble', 'list'),\");");
      
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Enable New Menu Item Links', 'SURFCMS_ENABLE_MENU_LINKS', 'True', 'Enable New Menu Item links on the admin box and Surfalot CMS List page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Enable New Block Links', 'SURFCMS_ENABLE_BLOCK_LINK', 'False', 'Enable New Block links on the admin box and Surfalot CMS List page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Enable New Block Group Links', 'SURFCMS_ENABLE_BLOCK_GROUP_LINKS', 'False', 'Enable New Block Group links on the admin box.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Enable New Page Content Links', 'SURFCMS_ENABLE_CONTENT_GROUP_LINKS', 'True', 'Enable New Page Content links on the admin box and Surfalot CMS List page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");

      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Show Menu Sample Box', 'SURFCMS_DISPLAY_MENU_SAMPLE_BOX', 'True', 'Menu Sample Box in the admin list page. View a sample of what the Navbar and Sidebar will look like below the content list of the List All page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Show Content Group Box', 'SURFCMS_DISPLAY_CONTENT_GROUP_BOX', 'False', 'Content Group Box in the admin list page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Show Block Group Box', 'SURFCMS_DISPLAY_BLOCK_GROUP_BOX', 'False', 'Block Group Box in the admin list page.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");

      tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `set_function`) VALUES ('Enable WYSIWYG editor', 'SURFCMS_ENABLE_WYSIWYG', 'True', 'Enable WYSIWYG editor.', '" . $group_id . "', '" . $sort++ . "', now(), \"tep_cfg_select_option(array('True', 'False'),\");");
      
      // example use_function configuration entry:
      // tep_db_query("INSERT INTO `configuration` (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`, `use_function`, `set_function`) VALUES ('Configuration Utility Call', 'SURFCMS_', 'false', 'Configuration Utility Call', '" . $group_id . "', '" . $sort++ . "', now(), 'tep_surfcms_configure_functions', \"tep_cfg_select_option(array('opt1', 'opt1'),\");");
      //
      // The use_function function resides:  inlcudes/apps/surfcms/admin/functions/configuration.php
      // include the following script in:  includes/hooks/admin/global/configuration.php  to make function available.
      //    class hook_admin_global_configuration {
      //      function __construct() {
      //        if ( !function_exists('tep_surfcms_configure_functions') ) {
      //          include(DIR_FS_CATALOG . 'inlcudes/apps/surfcms/admin/functions/configuration.php');
      //        }
      //      }
      //      function listen_surfcmsGlobal() {
      //        return true;
      //      }
      //    }

      return true;
    }
    
    // /////////////////////////////////////////////////////
    // insert sample menu
    //
    private function insertSampleMenu() {

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_brand.php', 4, '', 0, '', 0, 100, 1, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'Brand Home')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_new_products.php', 4, '', 0, 'fa-sun-o', 0, 200, 2, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'New Products')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_special_offers.php', 4, '', 0, 'fa-star', 0, 300, 2, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'Special Offers')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_reviews.php', 4, '', 0, 'fa-newspaper-o', 0, 400, 2, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'Reviews')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_languages.php', 4, '', 0, 'fa-bullhorn', 0, 600, 3, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'Languages')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_currencies.php', 4, '', 0, 'fa-money', 0, 700, 3, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'Currencies')");

      tep_db_query("INSERT INTO `surfcms_content` (`surfcms_content_group`, `surfcms_content_type`, `surfcms_content_url`, `surfcms_content_menu_parent`, `surfcms_content_menu_icon`, `surfcms_content_width`, `surfcms_content_sort_order`, `surfcms_content_show_in_nav`, `surfcms_content_show_in_box`, `surfcms_content_status`) VALUES ('nb_account.php', 4, '', 0, 'fa-user', 0, 800, 3, 0, 1)");
      $insert_id = tep_db_insert_id();
      tep_db_query("INSERT INTO `surfcms_content_description` (`surfcms_content_id`, `language_id`, `surfcms_content_menu_text`) VALUES (".$insert_id.", 1, 'My Account')");

      return true;
    }
    
    // /////////////////////////////////////////////////////
    // deleteConfiguration
    //
    private function deleteConfiguration() {

      $conf = tep_db_fetch_array(tep_db_query("SELECT `configuration_value` as id FROM `configuration` WHERE `configuration_key` LIKE 'SURFCMS_CONFIGURATION_GROUP' LIMIT 1"));

	  tep_db_query("DELETE FROM `configuration_group` WHERE `configuration_group_id` = '" . $conf['id'] . "'");
      tep_db_query("DELETE FROM `configuration` WHERE `configuration_key` in ('" . implode("', '", $this->keys()) . "')");

      return true;
    }

    // /////////////////////////////////////////////////////
    // updateVersion
    //
    private function updateVersion($version) {

      tep_db_query("UPDATE `configuration` SET `configuration_value` = '" . $version . "' WHERE `configuration_key` = 'SURFCMS_VERSION_INSTALLED'");

      return true;
    }




    
  } // class surfcms_setup
?>