<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  class hook_admin_global_surfcms {
	
    function __construct() {
		
      if ( !class_exists('global_surfcms') ) {
        include(DIR_FS_CATALOG . 'includes/apps/surfcms/hooks/admin/global/surfcms.php');
      }

      $hook = new global_surfcms();
      $hook->execute();
		
    }
  
    function listen_surfcmsGlobal() {
	  return true;
    }
  
  } // hook_admin_global_surfcms
?>