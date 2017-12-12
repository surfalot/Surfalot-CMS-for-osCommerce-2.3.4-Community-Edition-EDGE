<?php 
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
  
  // this file is inlcuded from /includes/hooks/admin/global/surfcms.php
  
*/

  class global_surfcms {

    function __construct() {
      global $surfcms, $language;
	  
      include('includes/languages/' . $language . '/surfcms.php');
      include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/functions/general.php');

	  if (!class_exists('surfcms')) 
		 require(DIR_FS_CATALOG . 'includes/apps/surfcms/surfcms.class.php');
	  
	  if ( !isset($surfcms) || !is_object($surfcms) || (get_class($surfcms) != 'surfcms') )
		$surfcms = new surfcms();

    }

    function execute() {
      global $cache_blocks;

      if (is_array($cache_blocks)) 
        $cache_blocks[] = array('title' => TEXT_CACHE_SURFCMS_CONTENT, 'code' => 'surfcms_content', 'file' => 'surfcms_content-language.cache', 'multiple' => true);

    }

  } // class global_surfcms
  
?>