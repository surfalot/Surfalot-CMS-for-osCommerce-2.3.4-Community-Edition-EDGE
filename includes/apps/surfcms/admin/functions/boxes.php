<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  function app_surfcms_get_admin_box_links() {
	global $surfcms, $admin;
	
	$results = (isset($_GET['results']) ? '&results=' . $_GET['results'] : '');
	
	$surfcms_menu = array(
	  array('code' => 'surfcms.php',
			'title' => MODULES_ADMIN_MENU_SURFCMS_LIST,
			'link' => tep_href_link('surfcms.php', 'action=list'.$results)),
	  array('code' => 'surfcms.php',
			'title' => MODULES_ADMIN_MENU_SURFCMS_LIST_PAGES,
			'link' => tep_href_link('surfcms.php', 'action=list&list_type=0'.$results)),
	  array('code' => 'surfcms.php',
			'title' => MODULES_ADMIN_MENU_SURFCMS_UTILITIES,
			'link' => tep_href_link('surfcms.php', 'action=utilities&origin=list'.$results))
    );
if (defined('SURFCMS_VERSION_INSTALLED') && !$surfcms->isRestricted($admin['id'], 'new_page')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_PAGE,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=0&wysiwyg=true'.$results));
						  
if (SURFCMS_ENABLE_BLOCK_LINK == 'True' && !$surfcms->isRestricted($admin['id'], 'new_blocks')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_BLOCK,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=2&wysiwyg=true'.$results));
if (SURFCMS_ENABLE_BLOCK_GROUP_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_block_group')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_BOX,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=2&subtype=sidebox&wysiwyg=true'.$results));
if (SURFCMS_ENABLE_BLOCK_GROUP_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_block_group')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_NAV,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=2&subtype=navbox&wysiwyg=true'.$results));
if (SURFCMS_ENABLE_BLOCK_GROUP_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_block_group')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_HEAD_TAG,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=2&subtype=headtag'.$results));
						  
if (SURFCMS_ENABLE_CONTENT_GROUP_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_page_content')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_CONTENT,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=1&wysiwyg=true'.$results));
						  
if (SURFCMS_ENABLE_MENU_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_menuing')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_SUB_MENU,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=3'.$results));
if (SURFCMS_ENABLE_MENU_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_menuing')) 
	$surfcms_menu[] = array('code' => 'surfcms.php',
			              'title' => MODULES_ADMIN_MENU_SURFCMS_NEW_MENU_LINK,
			              'link' => tep_href_link('surfcms.php', 'action=new_content&type=4'.$results));

    return $surfcms_menu;
  }
?>
