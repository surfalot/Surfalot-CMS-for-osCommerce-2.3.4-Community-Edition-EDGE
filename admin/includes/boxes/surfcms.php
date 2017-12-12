<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  echo '<link rel="stylesheet" type="text/css" href="../includes/apps/surfcms/admin/css/styles.boxes.css">'."\n";
  include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/functions/boxes.php');

  $cl_box_groups[] = array('heading' => MODULES_ADMIN_MENU_SURFCMS_HEADING,
                           'apps' => app_surfcms_get_admin_box_links());
?>
