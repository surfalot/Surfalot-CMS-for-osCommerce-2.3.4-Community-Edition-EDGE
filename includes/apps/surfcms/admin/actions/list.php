<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $content = 'list.php';
  
  // do page titles 
  $page_title = '';

  if (tep_not_null($_GET['list_type'])) {
	$page_title = $surfcms->getDef('app_content_page_title') .  ': ' . ucwords($surfcms->typeName((int)$_GET['list_type']));
  } elseif (tep_not_null($_GET['list_group'])) {
	$page_title = $surfcms->getDef('app_content_page_title') . ': ' . tep_db_prepare_input($_GET['list_group']);
  } else {
	$page_title = $surfcms->getDef('app_content_page_title') . ' ' . $surfcms->getDef('text_admin_all'); 
  }
  
?>
