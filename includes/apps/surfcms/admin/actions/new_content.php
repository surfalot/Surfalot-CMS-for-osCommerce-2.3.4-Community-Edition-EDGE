<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $content = 'new_content.php';
  
  // make sure there is no duplicate "page"s (zero content types)
  if ( isset($_GET['commit_action']) && (int)$_POST['surfcms_content_type'] == 0 ) {

	if ( $_GET['commit_action'] == 'insert_content' ) {
	  $surfcms_count = tep_db_fetch_array(tep_db_query("select count(surfcms_content_id) as count from surfcms_content where surfcms_content_group like '".tep_db_prepare_input($_POST['surfcms_content_group'])."' and surfcms_content_type = '0' limit 1"));
	} else {
	  $surfcms_count = tep_db_fetch_array(tep_db_query("select count(surfcms_content_id) as count from surfcms_content where surfcms_content_group like '".tep_db_prepare_input($_POST['surfcms_content_group'])."' and surfcms_content_id != '".(int)$_GET['ocID']."' and surfcms_content_type = '0' limit 1"));
	}
	if ( $surfcms_count['count'] == 1 ) {
      // return to edit
	  $surfcms->addAlert(sprintf($surfcms->getDef('commit_error'),tep_db_prepare_input($_POST['surfcms_content_group'])), 'error');
	  unset($_GET['subaction']);
	  unset($_GET['commit_action']);
	}

  }
  
  // do page titles 
  $page_title = '';

  switch ($_GET['subtype']) {
	  case 'sidebox': $subtype = $surfcms->getDef('text_admin_sidebox'); break;
	  case 'navbox': $subtype = $surfcms->getDef('text_admin_navbox'); break;
	  case 'headtag': $subtype = $surfcms->getDef('text_admin_headtag'); break;
	  default: $subtype = '';
  }
  if (tep_not_null($_GET['type'])) {
	$page_title = $surfcms->getDef('app_content_page_title') . ': ' . (tep_not_null($subtype) ? $subtype : ucwords($surfcms->typeName((int)$_GET['type'])));
  } elseif (tep_not_null($_GET['ocID'])) {
	$page_title = $surfcms->getDef('app_content_edit_page_title') . ': ' . ucwords($surfcms->typeName((int)$surfcms->getItemType($_GET['ocID'])));
  }

?>
