<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
  
  // this file is inlcuded from /includes/hooks/shop/application/surfcms.php

*/

  class global_surfcms {
    
    function __construct() {
      global $surfcms;

      if ( !class_exists('surfcms') )
        include(DIR_FS_CATALOG . 'includes/apps/surfcms/surfcms.class.php');

      if ( !isset($surfcms) || !is_object($surfcms) || (get_class($surfcms) != 'surfcms') )
        $surfcms = new surfcms();
      
    }

    function execute() {
      global $surfcms, $oscTemplate;
      $page = null;
      
      // prevent error when admin modules_hooks.php loads hooks
	  if ( isset($oscTemplate) && is_object($oscTemplate) && (get_class($oscTemplate) == 'oscTemplate') ) {

        if ( tep_not_null($_GET['pg']) ) {
          $page = $surfcms->getGroupName((int)$_GET['pg']);
        } else {
          $page = basename($_SERVER["SCRIPT_FILENAME"], '.php');
        }

        // do template content for the currently loaded page
        // This will seek all content that begins with current page name. 
        // i.e. "index" will find "index_nested" also and process them.
        // That allow developers to define multiple blocks per page ready to be inserted when called.
		// Also collects any block content for generic page areas.
        $contents = $surfcms->getPageGroup($page, null, true); // group content records where previously stored by getPageGroup($page, null, true); in Global Hook
        if ( $contents )
          foreach ($contents as $content) {
            if ($content['type'] == '2') {
              $oscTemplate->addBlock($content['body'], $content['group'], $content['sort_order']);
			} else {
			  $oscTemplate->addContent($surfcms->gridWrap($content['body'], $content['width']), $content['group'], $content['sort_order']);
			}
          }

      } // if ( isset($oscTemplate) )

      return true;
    } // function execute()


  } // class global_surfcms
?>
