<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

	if (isset($_POST['surfcms_content_id'])) {

	  tep_db_query("delete from surfcms_content where surfcms_content_id = '" . (int)$_POST['surfcms_content_id'] . "'");
	  tep_db_query("delete from surfcms_content_description where surfcms_content_id = '" . (int)$_POST['surfcms_content_id'] . "'");
	  
	  if (USE_CACHE == 'true') {
	    tep_reset_cache_block('surfcms_content');
	  }
	  
	}

	tep_redirect(tep_href_link('surfcms.php', $sticky_get_params . 'action=list'));

?>
