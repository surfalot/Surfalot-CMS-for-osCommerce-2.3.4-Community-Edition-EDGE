<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  /* surfcms_page abstract class */
  require_once DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_page.abstract.php';

  /* surfcms_app_setup singleton class */
  require_once DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_app_setup.class.php';


  // /////////////////////////////////////////////////////
  // SurfCMS Main Class
  //
  class surfcms extends surfcms_page {
      
    protected $_code = 'surfcms';
    protected $_title = 'Surfalot CMS App';

    protected $_config_id;
	protected $_admins;
	protected $_restricted_admins;
	protected $_restricted_utilities;
	protected $_restricted_lists;


    // /////////////////////////////////////////////////////
    // Construct / Destruct
    //
    public function __construct($group_name = null, $page_id = null, $languages_id = null, $defs = null) {

      $this->setLanguage();
      $this->loadLanguageFile('surfcms.class.php');
      $this->getVersion();
      $this->checkVersion();
      
      if ( isset($group_name) ) {
        $this->getPageGroup($group_name, $languages_id, $defs);
      } elseif ( isset($page_id) ) {
        $this->getPageGroup($this->getGroupName($page_id), $languages_id, $defs);
      }
      
      $this->_title .= ' v' . $this->getVersion() . ' ' . $this->getDef('text_content_app_subver');

    }

    public function __destruct() {}
    

    // /////////////////////////////////////////////////////
    // no serialize
    //
    private function __clone() {} 
    private function __sleep() {}
    private function __wakeup() {} 


    // /////////////////////////////////////////////////////
    // Gets
    //
    public function getGroupName($id) {
        
      $return = false;
      if ( isset($id) ) {
        $item_query = tep_db_query("select surfcms_content_group from surfcms_content where surfcms_content_id = '" . (int)$id . "' limit 1");
        if ( $item_row = tep_db_fetch_array($item_query) ) {
          $return = $item_row['surfcms_content_group'];
        }
      }
      
      return $return;
    }

    public function getItemType($id) {
        
      $return = false;
      if ( isset($id) ) {
        $item_query = tep_db_query("select oc.surfcms_content_type from surfcms_content oc, surfcms_content_description ocd where oc.surfcms_content_id = ocd.surfcms_content_id and oc.surfcms_content_id = '" . (int)$id . "' limit 1");
        if ( $item_row = tep_db_fetch_array($item_query) ) {
          $return = $item_row['surfcms_content_type'];
        }
      }
      
      return $return;
    }

    public function getItemTitle($id) {
        
      $return = false;
      if ( isset($id) ) {
        $item_query = tep_db_query("select ocd.surfcms_content_menu_text from surfcms_content oc, surfcms_content_description ocd where oc.surfcms_content_id = ocd.surfcms_content_id and oc.surfcms_content_id = '" . (int)$id . "' limit 1");
        if ( $item_row = tep_db_fetch_array($item_query) ) {
          $return = $item_row['surfcms_content_menu_text'];
        }
      }
      
      return $return;
    }

    public function itemExists($item_name, $types = null) {

      $return = false;
      if (tep_not_null($item_name)) {

        if ( is_array($item_name) ) {
          $where_group = " (surfcms_content_group like '" . implode("' or surfcms_content_group like '", tep_db_prepare_input($item_name)) . "')";
        } else {
          $where_group = " surfcms_content_group like '" . tep_db_prepare_input($item_name) . "'";
        }
        
        $where_type = '';
        if ( is_array($types) ) {
          $where_types = " and surfcms_content_type in ('" . implode("','", tep_db_prepare_input($types)) . "')";
        } elseif ( tep_not_null($types) ) {
          $where_types = " and surfcms_content_type = '" . (int)$types . "'";
        }
        
        $item_query = tep_db_query("select count(surfcms_content_id) as count from surfcms_content where" . $where_group . $where_types);
        if ($item = tep_db_fetch_array($item_query)) {
          $return = $item['count'];
        }

      }

      return $return;
    }

    public function getBlockItems($items = null, $languages_id = null) {
        
      return $this->getItems($items, '2', $languages_id);
      
    }

    public function getContentItems($items = null, $languages_id = null) {
        
      return $this->getItems($items, '1', $languages_id);
      
    }
    
    public function getItems($items = null, $types = null, $languages_id = null) {
        
      $return = false;
      if ( !isset($languages_id) ) { $languages_id = $this->languages_id; }
      
      $where_group = (is_array($items) ? " and oc.surfcms_content_group in ('" . implode("','",$items) . "')" : (tep_not_null($items) ? " and oc.surfcms_content_group like '" . $items . "'" : ''));
      $where_type = (is_array($types) ? " and oc.surfcms_content_type in ('" . implode("','",$types) . "')" : (tep_not_null($types) ? " and oc.surfcms_content_type = '" . $types . "'" : ''));

      $item_query = tep_db_query("select oc.surfcms_content_id, oc.surfcms_content_group, oc.surfcms_content_type, oc.surfcms_content_width, oc.surfcms_content_sort_order, oc.surfcms_content_administrators, ocd.surfcms_content_menu_text, ocd.surfcms_content_body from surfcms_content oc, surfcms_content_description ocd where oc.surfcms_content_id = ocd.surfcms_content_id" . $where_group . " and oc.surfcms_content_status = '1'" . $where_type . " and ocd.language_id = '" . (int)$languages_id . "' order by oc.surfcms_content_sort_order");
      if ( tep_db_num_rows($item_query) ) {
        $return = array();
        while ( $item_row = tep_db_fetch_array($item_query) ) {
          $return[] = array( 'id' => $item_row['surfcms_content_id'], 'group' =>  $item_row['surfcms_content_group'], 'type' =>  $item_row['surfcms_content_type'], 'width' =>  $item_row['surfcms_content_width'], 'sort_order' =>  $item_row['surfcms_content_sort_order'], 'admins' =>  $item_row['surfcms_content_administrators'], 'menu_text' =>  $item_row['surfcms_content_menu_text'], 'body' =>  $item_row['surfcms_content_body']);
        }
      }

      return $return;
    }

    public function getNavigation($nav = 'navbar', $languages_id = null) {

      $return = false;
      $navigation = array();
      if ( !isset($languages_id) ) { $languages_id = $this->languages_id; }

      $where = ($nav == 'navbar' ? " and oc.surfcms_content_show_in_nav != '0'" : " and oc.surfcms_content_show_in_box = '1'");
      $order = ($nav == 'navbar' ? " order by oc.surfcms_content_show_in_nav, oc.surfcms_content_sort_order" : " order by oc.surfcms_content_sort_order");
      $item_query = tep_db_query("select oc.surfcms_content_id as id, oc.surfcms_content_group as `group`, oc.surfcms_content_type as type, oc.surfcms_content_show_in_nav as nav, oc.surfcms_content_url as url, oc.surfcms_content_menu_parent as parent, oc.surfcms_content_menu_icon as icon, oc.surfcms_content_sort_order as sort_order, oc.surfcms_content_administrators as admins, ocd.surfcms_content_menu_text as menu_text, ocd.surfcms_content_meta_title as meta_title, ocd.surfcms_content_body as body from surfcms_content oc, surfcms_content_description ocd where oc.surfcms_content_id = ocd.surfcms_content_id and oc.surfcms_content_status = '1'" . $where . " and ocd.language_id = '" . (int)$languages_id . "'" . $order);

      // assemble nodes per ID
	  while ( $item_row = tep_db_fetch_array($item_query) ) {
        
        $item_row['link'] = $this->getLink($item_row['id'], $item_row['type'], $item_row['group'], $item_row['url']);
          
        // compile sets with sub-menu items below parents with IDs as keys
        if ( (int)$item_row['parent'] == 0 || $nav == 'box' ) {
          if ( isset($navigation[$item_row['id']]) && is_array( $navigation[$item_row['id']]['sub'] ) ) {
            $item_row['sub'] = $navigation[$item_row['id']]['sub']; } // add existing [sub] to $item_row
          $navigation[$item_row['id']] = $item_row; 
        } else {
          $navigation[$item_row['parent']]['sub'][$item_row['id']] = $item_row;
        }
        
      }
      
      // rearrange with sort_order as keys
      if ( count($navigation) ) {

        $return = array();
        if ($nav == 'navbar') {
            
          // separate and sort nav items
          foreach ( $navigation as $data ) {
            if ( isset($data['type']) ) { // skip disabled top menu items
              for ($i=0; isset($return[$data['nav']][((int)$data['sort_order']+$i)]); $i++) {}
              $return[$data['nav']][((int)$data['sort_order']+$i)] = $data;
            }
          }
          // sort left and right nav groups. home should only contain one
          if (count($return['2']))
              ksort($return['2']);
          if (count($return['3']))
              ksort($return['3']);
              
        } elseif ($nav == 'box') {
            
          foreach ( $navigation as $data ) {
            for ($i=0; isset($return[((int)$data['sort_order']+$i)]); $i++) {}
            $return[((int)$data['sort_order']+$i)] = $data;
          }
          if (count($return))
              ksort($return);
              
        }
        
      }
      
      return $return;
    }

    // /////////////////////////////////////////////////////
    // Get Link
    //
    public function getLink($id, $type = null, $group = null, $url = null) {
        
      $return = false;
      if (function_exists('tep_catalog_href_link')) { $funct = 'tep_catalog_href_link'; } else { $funct = 'tep_href_link'; }
      
      if ( !isset($type) || !isset($group) ) {
        $item_query = tep_db_query("select oc.surfcms_content_group, oc.surfcms_content_type, oc.surfcms_content_url from surfcms_content oc where oc.surfcms_content_id = '" . $id . "' limit 1");
        if ( $item_row = tep_db_fetch_array($item_query) ) {
          $group = $item_row['surfcms_content_group'];
          $type = $item_row['surfcms_content_type'];
          $url = $item_row['surfcms_content_url'];
        }
      }
      
      if ( tep_not_null($type) && $type == '4' ) {
        $return = $url; 
      } elseif ( tep_not_null($type) && $type == '3' ) {
        $return = '#'; 
      } elseif (tep_not_null($group) && file_exists(DIR_FS_CATALOG . $group . '.php')) {
        $return = $funct($group . '.php' . (tep_not_null($url) ? (substr($url,0,1) == '?' ? $url : '?'.$url) : '')); 
      } else {
        $return = $funct('surfcms.php?pg=' . $id); 
      }
      
      return $return;
    }
    
    // /////////////////////////////////////////////////////
    // Type Conversion
    //
    public function navGroup($enum) {
        
      switch ($enum) { 
        case '1': $return = 'navbar_modules_home'; break; 
        case '2': $return = 'navbar_modules_left'; break; 
        case '3': $return = 'navbar_modules_right'; break; 
      }
        
      return $return;
    }

    public function typeValue($type) {
        
      switch ($type) { 
        case $this->getDef('text_content_type_page'): $return = '0'; break; 
        case $this->getDef('text_content_type_content'): $return = '1'; break; 
        case $this->getDef('text_content_type_block'): $return = '2'; break; 
        case $this->getDef('text_content_type_sub_menu'): $return = '3'; break; 
        case $this->getDef('text_content_type_menu_link'): $return = '4'; break; 
      }
        
      return $return;
    }

    public function typeName($type) {
      
      switch ((string)$type) { 
        case '0': $return = $this->getDef('text_content_type_page'); break; 
        case '1': $return = $this->getDef('text_content_type_content'); break; 
        case '2': $return = $this->getDef('text_content_type_block'); break; 
        case '3': $return = $this->getDef('text_content_type_sub_menu'); break; 
        case '4': $return = $this->getDef('text_content_type_menu_link'); break; 
      }
      
      return $return;
    }

    // /////////////////////////////////////////////////////
    // Nav Templates
    //
    public function navNode($node) {

      ob_start();
      include(DIR_FS_CATALOG . 'includes/apps/surfcms/core/templates/nav_node.php');
      $data = ob_get_clean();

      return $data;
    }

    public function navSubmenuNode($node) {

      ob_start();
      include(DIR_FS_CATALOG . 'includes/apps/surfcms/core/templates/nav_sub_menu_node.php');
      $data = ob_get_clean();

      return $data;
    }

    // /////////////////////////////////////////////////////
    // Admin Access Restrictions
    //

    // returns array of restricted admins
    public function access_installed() {

      return file_exists(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/utilities_modules/access_administrator.php');
    }

    // returns array of restricted admins
    public function restricted_admins() {

      if ( !isset($this->_restricted_admins) ) {
        if ( $this->access_installed() ) {
		  $admins_config = tep_db_fetch_array(tep_db_query("SELECT configuration_value as users FROM configuration WHERE configuration_key LIKE 'SURFCMS_MODULE_CONTENT_ADMIN_GROUP'"));
          if ( !empty($admins_config['users']) )
		    $this->_restricted_admins = explode(',', $admins_config['users']);
		}
		if ( !isset($this->_restricted_admins) )
		  $this->_restricted_admins = array();
	  }

      return $this->_restricted_admins;
    }

    // returns array of restricted utilities
	public function restricted_utilities() {

      if ( !isset($this->_restricted_utilities) ) {
        if ( $this->access_installed() ) {
          $utilities_config = tep_db_fetch_array(tep_db_query("SELECT configuration_value as utilities FROM configuration WHERE configuration_key LIKE 'SURFCMS_MODULE_CONTENT_UTILITY_GROUP'"));
          if ( !empty($utilities_config['utilities']) )
            $this->_restricted_utilities = explode(',', $utilities_config['utilities']);
		}
		if ( !isset($this->_restricted_utilities) )
		  $this->_restricted_utilities = array();
	  }

      return $this->_restricted_utilities;
    }

    // returns array of restricted list boxes
	public function restricted_lists() {

      if ( !isset($this->_restricted_lists) ) {
        if ( $this->access_installed() ) {
          $lists_config = tep_db_fetch_array(tep_db_query("SELECT configuration_value as lists FROM configuration WHERE configuration_key LIKE 'SURFCMS_MODULE_CONTENT_LIST_GROUP'"));
          if ( !empty($lists_config['lists']) )
            $this->_restricted_lists = explode(',', $lists_config['lists']);
		}
		if ( !isset($this->_restricted_lists) )
		  $this->_restricted_lists = array();
	  }

      return $this->_restricted_lists;
    }

    // returns array of all admins
	// if $id is passed, method returns associated name
	// if $id is array, return array of admin names
	public function admins($id = null) {

      if ( !isset($this->_admins) ) {
        if ( $this->access_installed() ) {
          $this->_admins = array();
          $admin_query = tep_db_query("SELECT id, user_name FROM administrators");
          while ( $item_row = tep_db_fetch_array($admin_query) ) {
            $this->_admins[$item_row['id']] = $item_row['user_name'];
          }
		} else {
		  $this->_admins = array();
		}
	  }

      if ($id != null && is_array($id))
	    return array_uintersect_uassoc($this->_admins, $id, function($a, $b) { return 0; }, function($a, $b) { if ($a == $b) return 0; else return -1; } );
	  elseif ($id != null) 
	    return $this->_admins[$id];
	  else
	    return $this->_admins;
    }

    // Returns true if $admin is in restricted list AND $item is NULL
    // Returns true if $admin is in restricted list AND $item is in its restricted list
    public function isRestricted($admin_id = null, $item = null) {

      return ( in_array($admin_id, $this->restricted_admins())
	             && ( (($item == null) || (($item != null) && in_array($item, $this->restricted_utilities())))
	                  || (($item == null) || (($item != null) && in_array($item, $this->restricted_lists()))) )
			  );

    }


    // /////////////////////////////////////////////////////
    // Configuration Group ID
    //
    public function getConfigID() {

      if (!isset($this->_config_id)) {
	    $config = tep_db_fetch_array(tep_db_query("SELECT `configuration_value` as id FROM `configuration` WHERE `configuration_key` LIKE 'SURFCMS_CONFIGURATION_GROUP' LIMIT 1"));
	    $this->_config_id = $config['id'];
	  }

      return $this->_config_id;
    }


    // /////////////////////////////////////////////////////
    // Version Control
    //
    public function checkVersion() {

      if ( isset($this->_version) ) {

        // get Data version
        $surfcms_config_query = tep_db_query("SELECT `configuration_value` as vers FROM `configuration` WHERE `configuration_key` = 'SURFCMS_VERSION_INSTALLED'");
        if ($surfcms_config = tep_db_fetch_array($surfcms_config_query)) {
          $db_version = $surfcms_config['vers'];
        } else {
          trigger_error($this->getAppTitle() . $this->getDef('db_version_error'));
        }
        
        // check for install/update
        if ( isset($db_version) ) {
          if ( $this->_version != $db_version ) {
            trigger_error($this->getAppTitle() . sprintf($this->getDef('db_update_warning'),$db_version,$this->_version));
            $this->updateApp();
          }
        } else {
          trigger_error($this->getAppTitle() . sprintf($this->getDef('db_installation_warning'),$this->_version));
          $this->installApp();
        }
        
      }
      
      return $this->_version;
    }
    
    public function installApp() {
        
      surfcms_app_setup::i()->installDatabase($this->getVersion());
        
      return true;
    }
    
    public function updateApp() {
        
      surfcms_app_setup::i()->updateDatabase($this->getVersion());
        
      return true;
    }
    

    
  } // class surfcms
?>