<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $content = 'utilities.php';

  $module_base = DIR_FS_CATALOG . 'includes/apps/surfcms/admin/utilities_modules/';

  // do page title 
  $page_title = $surfcms->getDef('app_content_page_title');

  // collect utility modules & class names, and include them
  $surfcms_modules = array();
  foreach (glob($module_base."*.php") as $filename) {
    $surfcms_modules[basename($filename, '.php')] = array ( 'file' => basename($filename), 'class' => 'surfcms_' . basename($filename, '.php') );
	include_once($module_base . basename($filename));
  }
  ksort($surfcms_modules);


  // if there is a key the same name as the utility, 
  // we will execute a method of the utility class
  if ( !empty($_GET[$_GET['utility']]) ) {

    // include current
    $utility_class = 'surfcms_' . tep_sanitize_string($_GET['utility']);
    $utility_action = tep_sanitize_string($_GET[$_GET['utility']]);
      
    // execute utility action
    $utility_class::i()->$utility_action();

  }
  
  
?>