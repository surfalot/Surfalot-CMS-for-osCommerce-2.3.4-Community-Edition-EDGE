<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

	if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
		
	  if (isset($_GET['ocID'])) {
		tep_db_query("update surfcms_content set surfcms_content_status='".(int)$_GET['flag']."' where surfcms_content_id='".(int)$_GET['ocID']."'");
	  }
	  
	  if (USE_CACHE == 'true') {
		tep_reset_cache_block('surfcms_content');
	  }
	  
	}
	
	if ( ($_GET['navflag'] == '0') || ($_GET['navflag'] == '1') || ($_GET['navflag'] == '2') || ($_GET['navflag'] == '3') ) {
		
	  if (isset($_GET['ocID'])) {
		tep_db_query("update surfcms_content set surfcms_content_show_in_nav='".(int)$_GET['navflag']."' where surfcms_content_id='".(int)$_GET['ocID']."'");
	  }
	  
	  if (USE_CACHE == 'true') {
		tep_reset_cache_block('surfcms_content');
	  }
	  
	}
	
	if ( ($_GET['boxflag'] == '0') || ($_GET['boxflag'] == '1') ) {
		
	  if (isset($_GET['ocID'])) {
		tep_db_query("update surfcms_content set surfcms_content_show_in_box='".(int)$_GET['boxflag']."' where surfcms_content_id='".(int)$_GET['ocID']."'");
	  }
	  
	  if (USE_CACHE == 'true') {
		tep_reset_cache_block('surfcms_content');
	  }
	  
	}
	
	

	tep_redirect(tep_href_link('surfcms.php', $sticky_get_params . 'action=list&ocID=' . $_GET['ocID']));

?>
