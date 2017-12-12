<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $commit_action = (isset($_GET['commit_action']) ? tep_db_prepare_input($_GET['commit_action']) : (isset($_GET['ocID']) ? 'update_content' : 'insert_content' ) );

  if (count($_POST) > 0) {

    $show_in_box = (isset($_POST['surfcms_content_show_in_box']) ? tep_db_prepare_input($_POST['surfcms_content_show_in_box']) : '0');
    $show_in_box = ($_POST['surfcms_content_type']=='4' && tep_not_null($_POST['surfcms_content_group']) ? '0' : $show_in_box); // enforce no Navbar modules in box
    $show_in_nav = (isset($_POST['surfcms_content_show_in_nav']) ? tep_db_prepare_input($_POST['surfcms_content_show_in_nav']) : '0');
    switch ($_POST['surfcms_content_group']) {
      case 'navbar_modules_home': $show_in_nav = '1'; break;	
      case 'navbar_modules_left': $show_in_nav = '2'; break;	
      case 'navbar_modules_right': $show_in_nav = '3'; break;	
    }
	$administrators = '';
	if (is_array($_POST['surfcms_content_administrators'])) {
		$administrators = implode(',',$_POST['surfcms_content_administrators']);
	}

	$sql_data_array = array('surfcms_content_group' => tep_db_prepare_input($_POST['surfcms_content_group']),
							'surfcms_content_type' => tep_db_prepare_input($_POST['surfcms_content_type']),
							'surfcms_content_url' => tep_db_prepare_input($_POST['surfcms_content_url']),
							'surfcms_content_menu_parent' => tep_db_prepare_input($_POST['surfcms_content_menu_parent']),
							'surfcms_content_menu_icon' => tep_db_prepare_input($_POST['surfcms_content_menu_icon']),
							'surfcms_content_width' => tep_db_prepare_input($_POST['surfcms_content_width']),
							'surfcms_content_status' => tep_db_prepare_input($_POST['surfcms_content_status']),
							'surfcms_content_sort_order' => tep_db_prepare_input($_POST['surfcms_content_sort_order']),
							'surfcms_content_show_in_nav' => $show_in_nav,
							'surfcms_content_show_in_box' => $show_in_box,
							'surfcms_content_administrators' => tep_db_prepare_input($administrators)
							);

	if ($commit_action == 'insert_content') {
	  tep_db_perform('surfcms_content', $sql_data_array);
	  $surfcms_content_id = tep_db_insert_id();
	} elseif ($commit_action == 'update_content') {
	  if (isset($_GET['ocID'])) $surfcms_content_id = (int)$_GET['ocID'];
	  tep_db_perform('surfcms_content', $sql_data_array, 'update', "surfcms_content_id = '" . (int)$surfcms_content_id . "'");
	}

	$languages = tep_get_languages();
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		
	  $language_id = $languages[$i]['id'];

	  $sql_data_array = array('surfcms_content_menu_text' => tep_db_prepare_input($_POST['surfcms_content_menu_text'][$language_id]),
							  'surfcms_content_title' => tep_db_prepare_input($_POST['surfcms_content_title'][$language_id]),
							  'surfcms_content_body' => tep_db_prepare_input($_POST['surfcms_content_body'][$language_id]),
							  'surfcms_content_navbar_title' => tep_db_prepare_input($_POST['surfcms_content_navbar_title'][$language_id]),
							  'surfcms_content_meta_title' => tep_db_prepare_input($_POST['surfcms_content_meta_title'][$language_id]),
							  'surfcms_content_meta_description' => tep_db_prepare_input($_POST['surfcms_content_meta_description'][$language_id]),
							  'surfcms_content_meta_keywords' => tep_db_prepare_input($_POST['surfcms_content_meta_keywords'][$language_id])
							  );

	  $force_insert = false;
	  if ($commit_action == 'update_content') {
	    // check if it exists before deciding to insert or update (in case a language was added)
	    $item_query = tep_db_query("select count(surfcms_content_id) as total from surfcms_content_description where surfcms_content_id = '" . (int)$surfcms_content_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
	    $item_total = tep_db_fetch_array($item_query);
		if ($item_total['total'] == 0) {
		  $force_insert = true;
		}
	  }
	  
	  if ($commit_action == 'insert_content' || $force_insert) {
		$insert_sql_data = array('surfcms_content_id' => $surfcms_content_id,
								 'language_id' => $languages[$i]['id']);

		$sql_data_array = array_merge($sql_data_array, $insert_sql_data);

		tep_db_perform('surfcms_content_description', $sql_data_array);
		
	  } elseif ($commit_action == 'update_content') {

		tep_db_perform('surfcms_content_description', $sql_data_array, 'update', "surfcms_content_id = '" . (int)$surfcms_content_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
	  }
	}

	if (USE_CACHE == 'true') {
	  tep_reset_cache_block('surfcms_content');
	}
	
	if (isset($_POST['edit']) && $_POST['edit'] == 'update') { 
	  // update fields and return to editor
	  tep_redirect(tep_href_link('surfcms.php', $sticky_get_params . 'action=new_content&ocID=' . $surfcms_content_id));
	} else {
	  // update and go back to list page
	  tep_redirect(tep_href_link('surfcms.php', $sticky_get_params . 'action=list&ocID=' . $surfcms_content_id));
	}
	
  }

?>
