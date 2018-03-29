<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $languages = tep_get_languages();

  if (isset($_GET['ocID']) && empty($_POST)) {
      
    $surfcms_content_query = tep_db_query("select oc.surfcms_content_id, oc.surfcms_content_group, oc.surfcms_content_type, oc.surfcms_content_url, oc.surfcms_content_menu_parent, oc.surfcms_content_menu_icon, oc.surfcms_content_status, oc.surfcms_content_sort_order, oc.surfcms_content_width, oc.surfcms_content_show_in_nav, oc.surfcms_content_show_in_box, oc.surfcms_content_administrators from surfcms_content oc where oc.surfcms_content_id = '" . (int)$_GET['ocID'] . "'");
    if ( $surfcms_content = tep_db_fetch_array($surfcms_content_query) ) {
      $surfcms_content_description_query = tep_db_query("select ocd.language_id, ocd.surfcms_content_menu_text, ocd.surfcms_content_title, ocd.surfcms_content_body, ocd.surfcms_content_navbar_title, ocd.surfcms_content_meta_title, ocd.surfcms_content_meta_description, ocd.surfcms_content_meta_keywords from surfcms_content_description ocd where ocd.surfcms_content_id = '" . (int)$_GET['ocID'] . "'");
      while ( $surfcms_content_description = tep_db_fetch_array($surfcms_content_description_query) ) {
        $surfcms_content['surfcms_content_menu_text'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_menu_text'];
        $surfcms_content['surfcms_content_title'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_title'];
        $surfcms_content['surfcms_content_body'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_body'];
        $surfcms_content['surfcms_content_navbar_title'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_navbar_title'];
        $surfcms_content['surfcms_content_meta_title'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_meta_title'];
        $surfcms_content['surfcms_content_meta_description'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_meta_description'];
        $surfcms_content['surfcms_content_meta_keywords'][$surfcms_content_description['language_id']] = $surfcms_content_description['surfcms_content_meta_keywords'];
      }
    }
    $ocInfo = new objectInfo($surfcms_content);
    
  } elseif (tep_not_null($_POST)) {
      
    $ocInfo = new objectInfo($_POST); // objectInfo already uses tep_db_prepare_input() on each var and iterates arrays also
  
  } else {
      
      $parameters = array('surfcms_content_id' => '',
                          'surfcms_content_group' => '',
                          
                          'surfcms_content_type' => $_GET['type'],
                          'surfcms_content_menu_url' => '',
                          'surfcms_content_menu_parent' => '',
                          'surfcms_content_menu_icon' => '',
                          'surfcms_content_width' => '',
                          'surfcms_content_sort_order' => '100',
                          'surfcms_content_status' => '',
                          'surfcms_content_show_in_nav' => '',
                          'surfcms_content_show_in_box' => '',
                          'surfcms_content_administrators' => '',
                          
                          'surfcms_content_menu_text' => '',
                          'surfcms_content_title' => '',
                          'surfcms_content_body' => '',
                          'surfcms_content_navbar_title' => '',
                          'surfcms_content_meta_title' => '',
                          'surfcms_content_meta_description' => '',
                          'surfcms_content_meta_keywords' => ''
                          );
  
      $ocInfo = new objectInfo($parameters);

  }


  if ( !in_array($admin['id'], explode(',',$ocInfo->surfcms_content_administrators)) )  {
    
    // collect potential parent pages
    if ((isset($_GET['type']) && ($_GET['type']=='0' || $_GET['type']=='4')) || (!isset($_GET['type']) && ($ocInfo->surfcms_content_type == '0' || $ocInfo->surfcms_content_type == '4') )) {
      $parents = array();
      $parents = array(array('id' => '', 'text' => $surfcms->getDef('text_content_root_label')));
      $tmp_query = tep_db_query("select oc.surfcms_content_id, ocd.surfcms_content_menu_text from surfcms_content oc, surfcms_content_description ocd where oc.surfcms_content_id = ocd.surfcms_content_id and oc.surfcms_content_type = '3' and ocd.language_id = ".$languages_id." order by ocd.surfcms_content_menu_text");
      while ( $row = tep_db_fetch_array($tmp_query) ) {
        $parents[] = array('id' => $row['surfcms_content_id'], 'text' => $row['surfcms_content_menu_text']);
      }
    }
    
    // collect all the files in the shop root for edit page selector
    if ((isset($_GET['type']) && $_GET['type']=='0') || (!isset($_GET['type']) && $ocInfo->surfcms_content_type == '0' )) {
      $file_extension = substr($_SERVER['SCRIPT_FILENAME'], strrpos($_SERVER['SCRIPT_FILENAME'], '.'));
      $tmp = array();
      if ($dir = @dir(DIR_FS_CATALOG)) {
        while ($file = $dir->read()) {
          if (($file != '.') && ($file != '..') && !is_dir(DIR_FS_CATALOG . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $tmp[basename($file,$file_extension)] = '';
            }
          }
        }
      }
      ksort($tmp);
      
      $files_array = array(array('id' => '', 'text' => $surfcms->getDef('text_content_custom_label')));
      foreach ($tmp as $file => $v) {
        $files_array[] = array('id' => $file, 'text' => $file);
      }
    }

    // collect all the preset groups and add custom groups and pages from database for edit page selector
    if ((isset($_GET['type']) && $_GET['type']=='1') || (!isset($_GET['type']) && $ocInfo->surfcms_content_type == '1' )) {
      $tmp = array();
      $tmp_query = tep_db_query("select distinct surfcms_content_group as name from surfcms_content where surfcms_content_type in ('0','1')");
      while ( $row = tep_db_fetch_array($tmp_query) ) {
        $tmp[$row['name']] = '';
      }
    
      if ($maindir = @dir(DIR_FS_CATALOG_MODULES . 'content/')) {
        while ($group = $maindir->read()) {
          if ( ($group != '.') && ($group != '..') && is_dir(DIR_FS_CATALOG_MODULES . 'content/' . $group)) {
            $tmp[$group] = '';
          }
        }
      }
      ksort($tmp);
      
      $groups_array = array(array('id' => '', 'text' => $surfcms->getDef('text_content_custom_label')));
      foreach ($tmp as $group => $v) {
        $groups_array[] = array('id' => $group, 'text' => $group);
      }
    }
    
    // setup block groups for edit page selector
    if ((isset($_GET['type']) && $_GET['type']=='2') || (!isset($_GET['type']) && $ocInfo->surfcms_content_type == '2' )) {
      switch ($_GET['subtype']) {
              case 'sidebox': $rest_blocks_array = array( array('id' => '', 'text' => $surfcms->getDef('text_content_select_label')), 
                                                          array('id' => 'boxes_column_left', 'text' => 'Box: Left Column'), 
                                                          array('id' => 'boxes_column_right', 'text' => 'Box: Right Column') );
                   break; 
              case 'navbox':  $rest_blocks_array = array( array('id' => '', 'text' => $surfcms->getDef('text_content_select_label')), 
                                                          array('id' => 'navbar_modules_home', 'text' => 'Navbar: Home'), 
                                                          array('id' => 'navbar_modules_left', 'text' => 'Navbar: Left'), 
                                                          array('id' => 'navbar_modules_right', 'text' => 'Navbar: Right') );
                   break; 
              case 'headtag': $rest_blocks_array = array( array('id' => '', 'text' => $surfcms->getDef('text_content_select_label')), 
                                                          array('id' => 'footer_scripts', 'text' => 'Footer Script'), 
                                                          array('id' => 'header_tags', 'text' => 'Header Tag') );
                   break; 
              default: $blocks_array = array( array('id' => '', 'text' => $surfcms->getDef('text_content_custom_label')), 
                                              array('id' => 'boxes_column_left', 'text' => 'Box: Left Column'), 
                                              array('id' => 'boxes_column_right', 'text' => 'Box: Right Column'), 
                                              array('id' => 'footer_scripts', 'text' => 'Footer Script'),
                                              array('id' => 'header_tags', 'text' => 'Header Tag'), 
                                              array('id' => 'navbar_modules_home', 'text' => 'Navbar: Home'), 
                                              array('id' => 'navbar_modules_left', 'text' => 'Navbar: Left'), 
                                              array('id' => 'navbar_modules_right', 'text' => 'Navbar: Right') );
      }
    }
    
    
    // collect all the files in the includes/modules/navbar_modules folder for edit page selector
    if ((isset($_GET['type']) && $_GET['type']=='4') || (!isset($_GET['type']) && $ocInfo->surfcms_content_type == '4' )) {
      $file_extension = substr($_SERVER['SCRIPT_FILENAME'], strrpos($_SERVER['SCRIPT_FILENAME'], '.'));
      $tmp = array();
      if ($dir = @dir(DIR_FS_CATALOG . 'includes/modules/navbar_modules/')) {
        while ($file = $dir->read()) {
          if (($file != '.') && ($file != '..') && !is_dir(DIR_FS_CATALOG . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $tmp[basename($file)] = '';
            }
          }
        }
      }
      ksort($tmp);
      
      $nav_array = array(array('id' => '', 'text' => $surfcms->getDef('text_content_nav_modules')));
      foreach ($tmp as $file => $v) {
        $nav_array[] = array('id' => $file, 'text' => ucwords(str_replace(array('nb_','_','.php'),array('',' ',''),$file)));
      }
    }


    if (!isset($ocInfo->surfcms_content_status) || $ocInfo->surfcms_content_status == '') $ocInfo->surfcms_content_status = '1';
    switch ($ocInfo->surfcms_content_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
    
    // build width input
    $checked = ( $ocInfo->surfcms_content_width==='' ? 12 : (int)$ocInfo->surfcms_content_width);
    $width_fields = tep_draw_radio_field('surfcms_content_width', '0', (0 == $checked ? true : false)) . '&nbsp;<span style="font-size: 1.3em;">' . $surfcms->getDef('text_content_width_none') . '</span>&nbsp;&nbsp;&nbsp;';
    for ($i=1; $i<=12; $i++) {
      $width_fields .= tep_draw_radio_field('surfcms_content_width', (string)$i, ($i == $checked ? true : false)) . '&nbsp;<span style="font-size: 1.3em;">' . (string)$i . '</span>&nbsp;&nbsp;&nbsp;';
    }

    if ($ocInfo->surfcms_content_type=='3' && $ocInfo->surfcms_content_show_in_nav=='') $ocInfo->surfcms_content_show_in_nav = '2'; // default for sub-menu can't be disabled
    if (!isset($ocInfo->surfcms_content_show_in_nav) || $ocInfo->surfcms_content_show_in_nav == '') $ocInfo->surfcms_content_show_in_nav = '0';  // everything else defaults to disabled
    switch ($ocInfo->surfcms_content_show_in_nav) {
      case '3': $in_nav_home = false; $in_nav_left = false; $in_nav_right = true; $out_nav = false; break;
      case '2': $in_nav_home = false; $in_nav_left = true; $in_nav_right = false; $out_nav = false; break;
      case '1': $in_nav_home = true; $in_nav_left = false; $in_nav_right = false; $out_nav = false; break;
      case '0': 
      default: $in_nav_home = false; $in_nav_left = false; $in_nav_right = false; $out_nav = true;
    }

    if (!isset($ocInfo->surfcms_content_show_in_box) || $ocInfo->surfcms_content_show_in_box == '') $ocInfo->surfcms_content_show_in_box = '0';
    switch ($ocInfo->surfcms_content_show_in_box) {
      case '0': $in_box = false; $out_box = true; break;
      case '1':
      default: $in_box = true; $out_box = false;
    }


    if ( SURFCMS_ENABLE_WYSIWYG == 'True' ) {
      include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/functions/tinymce_init.php');
    }

    if (isset($_GET['docopy'])) { unset($_GET['ocID']); }
    $form_action = (isset($_GET['ocID']) ? 'update_content' : 'insert_content');
    
    echo tep_draw_form($form_action, 'surfcms.php', $sticky_get_params . 'action=new_content&subaction=commit&commit_action=' . $form_action . (isset($_GET['ocID']) ? '&ocID=' . $_GET['ocID'] : ''), 'post'); 

?>
    <table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_type'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; 
                                   echo '<span style="font-size: 1.2em; font-weight:bold;">' . ucfirst($surfcms->typeName($ocInfo->surfcms_content_type)) . '</span>';
                                   echo tep_draw_hidden_field('surfcms_content_type', $ocInfo->surfcms_content_type); ?></td>
          </tr>
<?php /* surfcms_content_group */?>
<?php if ($ocInfo->surfcms_content_type!='3') { 

        if ( is_array($rest_blocks_array) && count($rest_blocks_array) ) {
            $group_input = tep_draw_pull_down_menu('surfcms_content_group', $rest_blocks_array, (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : ''), 'id="blockPulldown"');
        } else {
            $group_input = tep_draw_input_field('surfcms_content_group', (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : (isset($_GET['group']) ? tep_db_prepare_input($_GET['group']) : '' ) ), 'id="groupField" size="35"');
        } 
        ?>
          <tr>
            <td class="main">&nbsp;</td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'); ?>
                             <?php echo ($ocInfo->surfcms_content_type=='0' ? $surfcms->getDef('text_content_name') : ($ocInfo->surfcms_content_type!='4' ? $surfcms->getDef('text_content_ref_id') : $surfcms->getDef('text_content_menu_item'))); ?> &nbsp;
							 <?php if (!is_array($rest_blocks_array)) { echo ($ocInfo->surfcms_content_type=='0' ? $surfcms->getDef('text_content_name_ex') : ($ocInfo->surfcms_content_type!='4' ? $surfcms->getDef('text_content_ref_id_ex') : $surfcms->getDef('text_content_nav_module'))); } ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo ($ocInfo->surfcms_content_type=='0' ? $surfcms->getDef('text_content_name') : ($ocInfo->surfcms_content_type!='4' ? $surfcms->getDef('text_content_ref_id') : $surfcms->getDef('text_content_menu_item'))); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $group_input . 
                                                                                                       (is_array($groups_array) && count($groups_array) ? ' ' . tep_draw_pull_down_menu('group_names', $groups_array, (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : ''), 'id="groupPulldown"') : '') . 
                                                                                                       (is_array($files_array) && count($files_array) ? ' ' . tep_draw_pull_down_menu('file_names', $files_array, (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : ''), 'id="filePulldown"') : '') . 
                                                                                                       (is_array($blocks_array) && count($blocks_array) ? ' ' . tep_draw_pull_down_menu('block_names', $blocks_array, (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : ''), 'id="blockPulldown"') : '') . 
                                                                                                       (is_array($nav_array) && count($nav_array) ? ' ' . tep_draw_pull_down_menu('nav_names', $nav_array, (tep_not_null($ocInfo->surfcms_content_group) ? $ocInfo->surfcms_content_group : ''), 'id="navPulldown"') : ''); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_group', ''); ?>
<?php } ?>
<?php /* surfcms_content_url */?>
<?php if ($ocInfo->surfcms_content_type=='0') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_parameters'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('surfcms_content_url', (isset($ocInfo->surfcms_content_url) ? $ocInfo->surfcms_content_url : '' ), 'size="25" maxlength="192"') . ' &nbsp; ' . $surfcms->getDef('text_content_parameters_ex'); ?></td>
          </tr>
<?php } elseif ($ocInfo->surfcms_content_type=='4') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_url'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('surfcms_content_url', (isset($ocInfo->surfcms_content_url) ? $ocInfo->surfcms_content_url : '' ), 'size="64" maxlength="192" id="urlField"'); ?></td>
          </tr>
<?php } elseif ($ocInfo->surfcms_content_type!='0') { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_url', ''); ?>
<?php } ?>

<?php /* surfcms_content_sort_order */?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_sort_order'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('surfcms_content_sort_order', $ocInfo->surfcms_content_sort_order, 'size="8"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_status */?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_status'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('surfcms_content_status', '1', $in_status) . '&nbsp;' . $surfcms->getDef('text_content_active') . '&nbsp;' . tep_draw_radio_field('surfcms_content_status', '0', $out_status) . '&nbsp;' . $surfcms->getDef('text_content_not_active'); ?></td>
          </tr>
<?php /* surfcms_content_show_in_nav */?>
<?php if ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='3' || $ocInfo->surfcms_content_type=='4') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_heading_in_nav'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . '<input type="radio" name="surfcms_content_show_in_nav" value="1"' . ($in_nav_home==true ? ' checked="checked"' : '') . ($ocInfo->surfcms_content_type=='3' ? ' disabled="disabled"' : '') . '>&nbsp;' . $surfcms->getDef('text_content_in_nav_home') . '&nbsp;' .
                                                                                                       tep_draw_radio_field('surfcms_content_show_in_nav', '2', $in_nav_left) . '&nbsp;' . $surfcms->getDef('text_content_in_nav_left') . '&nbsp;' . 
                                                                                                       tep_draw_radio_field('surfcms_content_show_in_nav', '3', $in_nav_right) . '&nbsp;' . $surfcms->getDef('text_content_in_nav_right') . '&nbsp;
                                                                                                       <input type="radio" name="surfcms_content_show_in_nav" value="0"' . ($out_nav==true ? ' checked="checked"' : '') . ($ocInfo->surfcms_content_type=='3' ? ' disabled="disabled"' : '') . '>&nbsp;' . $surfcms->getDef('text_content_not_in_nav'); ?></td>
          </tr>
<?php } elseif ($ocInfo->surfcms_content_type==2 && (!isset($_GET['subtype']) || (isset($_GET['subtype']) && $_GET['subtype']=='navbox'))) { ?>
<?php 
  $show_in_nav = 0;
  switch ($ocInfo->surfcms_content_group) {
    case 'navbar_modules_home': $show_in_nav = '1'; break;    
    case 'navbar_modules_left': $show_in_nav = '2'; break;    
    case 'navbar_modules_right': $show_in_nav = '3'; break;    
  }
 ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_heading_in_nav'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<input type="radio" name="surfcms_content_show_in_nav" value="1"' . ($show_in_nav=='1' ? ' checked="checked"' : '') . ' disabled="disabled">&nbsp;' . $surfcms->getDef('text_content_in_nav_home') . '&nbsp;
                                                                                                   <input type="radio" name="surfcms_content_show_in_nav" value="2"' . ($show_in_nav=='2' ? ' checked="checked"' : '') . ' disabled="disabled">&nbsp;' . $surfcms->getDef('text_content_in_nav_left') . '&nbsp;
                                                                                                   <input type="radio" name="surfcms_content_show_in_nav" value="3"' . ($show_in_nav=='3' ? ' checked="checked"' : '') . ' disabled="disabled">&nbsp;' . $surfcms->getDef('text_content_in_nav_right') . '&nbsp;
                                                                                                   <input type="radio" name="surfcms_content_show_in_nav" value="0"' . ($show_in_nav=='0' ? ' checked="checked"' : '') . ' disabled="disabled">&nbsp;' . $surfcms->getDef('text_content_not_in_nav'); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_show_in_nav', '0'); ?>
<?php } ?>
<?php /* surfcms_content_show_in_box */?>
<?php if ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='4') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_heading_in_box'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('surfcms_content_show_in_box', '1', $in_box) . '&nbsp;' . $surfcms->getDef('text_content_in_box') . '&nbsp;' . tep_draw_radio_field('surfcms_content_show_in_box', '0', $out_box) . '&nbsp;' . $surfcms->getDef('text_content_not_in_box'); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_show_in_box', '0'); ?>
<?php } ?>
<?php /* surfcms_content_administrators */?>
<?php if ( count($surfcms->restricted_admins()) && !$surfcms->isRestricted($admin['id']) ) {
        // build array of checkboxes
        $current_admin_restrictions = explode(',',$ocInfo->surfcms_content_administrators);
        $admin_select = '';
        foreach ( $surfcms->restricted_admins() as $admin_id ) {
          $admin_select .= '<input id="admin_' . $admin_id . '" type="checkbox" name="surfcms_content_administrators[]" value="'.$admin_id.'"' . (in_array($admin_id,$current_admin_restrictions) ? ' checked="checked"' : '') . ' style="display:inline-block; position:relative; top:2px;">' . ' <label for="admin_' . $admin_id . '">' . $surfcms->admins($admin_id) . '</label>&nbsp;&nbsp;';
        }
?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_heading_administrators'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $admin_select; ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_administrators', ''); ?>
<?php } ?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_menu_parent */?>
<?php if ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='4') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_menu_parent'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('surfcms_content_menu_parent', $parents, (tep_not_null($ocInfo->surfcms_content_menu_parent) ? $ocInfo->surfcms_content_menu_parent : '' ), 'id="parentPulldown"' ) . '&nbsp; ' . $surfcms->getDef('text_content_menu_parent_help'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_menu_parent', '0'); ?>
<?php } ?>
<?php /* surfcms_content_menu_icon */?>
<?php if ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='3' || $ocInfo->surfcms_content_type=='4') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_menu_icon'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('surfcms_content_menu_icon', (tep_not_null($ocInfo->surfcms_content_menu_icon) ? $ocInfo->surfcms_content_menu_icon : '' ), 'id="iconField" size="25" maxlength="40"') . '&nbsp; ' . $surfcms->getDef('text_content_menu_icon_help'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_menu_icon', ''); ?>
<?php } ?>
<?php /* surfcms_content_menu_text */?>
<?php
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo ( ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='3' || $ocInfo->surfcms_content_type=='4') ? $surfcms->getDef('text_content_menu_text') : $surfcms->getDef('text_content_admin_text')); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_menu_text[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_menu_text[$languages[$i]['id']]) ? $ocInfo->surfcms_content_menu_text[$languages[$i]['id']] : '' ), 'size="45" maxlength="64"'); ?></td>
          </tr>
<?php
        }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_title */?>
<?php if ($ocInfo->surfcms_content_type=='0') { ?>
<?php
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo $surfcms->getDef('text_content_title'); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_title[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_title[$languages[$i]['id']]) ? $ocInfo->surfcms_content_title[$languages[$i]['id']] : '' ), 'size="45" maxlength="128"'); ?></td>
          </tr>
<?php
        }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php } else { 
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          echo tep_draw_hidden_field('surfcms_content_title[' . $languages[$i]['id'] . ']', ''); 
        } ?>
<?php } ?>
<?php /* surfcms_content_width */?>
<?php if ($ocInfo->surfcms_content_type=='0' || $ocInfo->surfcms_content_type=='1') { ?>
          <tr>
            <td class="main"><?php echo $surfcms->getDef('text_content_width'); ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $width_fields; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php } else { ?>
<?php     echo tep_draw_hidden_field('surfcms_content_width', '0'); ?>
<?php } ?>
<?php /* surfcms_content_body */?>
<?php if ($ocInfo->surfcms_content_type!='3' && $ocInfo->surfcms_content_type!='4') { ?>
<?php
		for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $rows = ((isset($ocInfo->surfcms_content_body[$languages[$i]['id']]) && substr_count($ocInfo->surfcms_content_body[$languages[$i]['id']],"\n") > 14) ? (string)(substr_count($ocInfo->surfcms_content_body[$languages[$i]['id']],"\n")+3) : '15');
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo $surfcms->getDef('text_content_body'); ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php echo tep_draw_textarea_field('surfcms_content_body[' . $languages[$i]['id'] . ']', 'soft', '90', $rows, (isset($ocInfo->surfcms_content_body[$languages[$i]['id']]) ? $ocInfo->surfcms_content_body[$languages[$i]['id']] : '' ), /*'style="white-space: pre; overflow-wrap: normal; overflow-x: scroll;"'.*/(SURFCMS_ENABLE_WYSIWYG == 'True' && $_GET['wysiwyg']=='true'?' class="htmleditor"':'')); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
        }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php } else { ?>
<?php    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          echo tep_draw_hidden_field('surfcms_content_body[' . $languages[$i]['id'] . ']', '');
        } ?>
<?php } ?>
<?php /* surfcms_content_navbar_title */?>
<?php if ($ocInfo->surfcms_content_type=='0') { ?>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo $surfcms->getDef('text_content_navbar_title'); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_navbar_title[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_navbar_title[$languages[$i]['id']]) ? $ocInfo->surfcms_content_navbar_title[$languages[$i]['id']] : '' ), 'size="45" maxlength="64"'); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_meta_title */?>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo $surfcms->getDef('text_content_meta_title'); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_meta_title[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_meta_title[$languages[$i]['id']]) ? $ocInfo->surfcms_content_meta_title[$languages[$i]['id']] : '' ), 'size="45" maxlength="128"'); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_meta_description */?>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo $surfcms->getDef('text_content_meta_description'); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_meta_description[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_meta_description[$languages[$i]['id']]) ? $ocInfo->surfcms_content_meta_description[$languages[$i]['id']] : '' ), 'size="80" maxlength="255"'); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php /* surfcms_content_meta_keywords */?>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo $surfcms->getDef('text_content_meta_keywords'); ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('surfcms_content_meta_keywords[' . $languages[$i]['id'] . ']', (isset($ocInfo->surfcms_content_meta_keywords[$languages[$i]['id']]) ? $ocInfo->surfcms_content_meta_keywords[$languages[$i]['id']] : '' ), 'size="80" maxlength="255"'); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php 
} else { 
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      echo tep_draw_hidden_field('surfcms_content_navbar_title[' . $languages[$i]['id'] . ']', '');
      echo tep_draw_hidden_field('surfcms_content_meta_title[' . $languages[$i]['id'] . ']', '');
      echo tep_draw_hidden_field('surfcms_content_meta_description[' . $languages[$i]['id'] . ']', '');
      echo tep_draw_hidden_field('surfcms_content_meta_keywords[' . $languages[$i]['id'] . ']', '');
    }
} 
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><p><div id="help-switch">Help ?</div></p></td>
            <td class="main" align="right"><?php echo ( isset($_GET['ocID']) ? tep_draw_button(IMAGE_UPDATE, 'disk', null, 'secondary', array('params' => 'name="edit" value="update"')) . '&nbsp;&nbsp;' : '' ) . 
                                                  ( isset($_GET['ocID']) ? tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') : tep_draw_button(IMAGE_INSERT, 'plus', null, 'primary') ) . '&nbsp;&nbsp;' . 
                                                                           tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('surfcms.php', $sticky_get_params . 'action=list' . (isset($_GET['ocID']) ? '&ocID=' . $_GET['ocID'] : ''))); ?></td>
          </tr>
        </table>        
        </td>
      </tr>
    </table></form>
    

<div id="help-detail" style="width: 100%;max-width:900px;display:none;"></div>

<script>
$(document).ready(function(){
    
  // Quick Help Button
  if ($('#help-switch').length) {
    $("#help-switch").click(function() {
        $("#help-detail").html('<?php echo tep_image((ENABLE_SSL_CATALOG=='false' ? HTTP_CATALOG_SERVER : HTTPS_CATALOG_SERVER) . '/includes/apps/surfcms/docs/images/doc_demo_help_area.jpg', null, null, null, 'width="100%"'); ?>');
        $("#help-detail").toggle();
    });
  }
    
<?php if (is_array($files_array)) { ?>
  var saveval;
  $('#filePulldown').change(function(){
    if ($(this).val()=='') {
      $('#groupField').val(saveval);
    } else {
      $('#groupField').val($(this).val());
    }
  });

  $('#filePulldown').click(function(){
    if ($(this).val()=='') {
      saveval = $('#groupField').val();
    }
  });
<?php } ?>

<?php if (is_array($groups_array)) { ?>
  var saveval;
  $('#groupPulldown').change(function(){
    if ($(this).val()=='') {
      $('#groupField').val(saveval);
    } else {
      $('#groupField').val($(this).val());
    }
  });

  $('#groupPulldown').click(function(){
    if ($(this).val()=='') {
      saveval = $('#groupField').val();
    }
  });
<?php } ?>

<?php if (is_array($blocks_array) || is_array($rest_blocks_array)) { ?>
  var saveval;
  $('#blockPulldown').change(function(){
    if ($(this).val()=='') {
      $('#groupField').val(saveval);
    } else {
      $('#groupField').val($(this).val());
      if ($('#groupField').val()=='navbar_modules_home') {
        $('input[name=surfcms_content_show_in_nav][value=1]').prop('checked', true);
      } else if ($('#groupField').val()=='navbar_modules_left') {
        $('input[name=surfcms_content_show_in_nav][value=2]').prop('checked', true);
      } else if ($('#groupField').val()=='navbar_modules_right') {
        $('input[name=surfcms_content_show_in_nav][value=3]').prop('checked', true);
      } else {
        $('input[name=surfcms_content_show_in_nav][value=0]').prop('checked', true);
      }
    }
  });

  $('#blockPulldown').click(function(){
    if ($(this).val()=='') {
      saveval = $('#groupField').val();
    }
  });
<?php } ?>

<?php if (is_array($rest_blocks_array)) { ?>
  var saveval;
  $('#blockPulldown').change(function(){
      if ($(this).val()=='navbar_modules_home') {
        $('input[name=surfcms_content_show_in_nav][value=1]').prop('checked', true);
      } else if ($(this).val()=='navbar_modules_left') {
        $('input[name=surfcms_content_show_in_nav][value=2]').prop('checked', true);
      } else if ($(this).val()=='navbar_modules_right') {
        $('input[name=surfcms_content_show_in_nav][value=3]').prop('checked', true);
      } else {
        $('input[name=surfcms_content_show_in_nav][value=0]').prop('checked', true);
      }
  });

  $('#blockPulldown').click(function(){
    if ($(this).val()=='') {
      saveval = $('#groupField').val();
    }
  });
<?php } ?>

<?php if (is_array($nav_array)) { ?>
  var saveval;
  var saveiconval;
  
  $('#groupField').focus(function(e) {
    $(this).blur();
  });

  if ($('#groupField').val()!='') {
    $('#urlField').prop('disabled', true);
    $('#parentPulldown').val("").change();
    $('#parentPulldown').prop('disabled', true);
      
    //$('input[name=surfcms_content_show_in_nav][value=1]').prop('checked', true);
    $('input[name=surfcms_content_show_in_box][value=0]').prop('checked', true);
    //$('input[name=surfcms_content_show_in_nav]').prop('disabled', true);
    $('input[name=surfcms_content_show_in_box]').prop('disabled', true);
    $('input[name=surfcms_content_show_in_nav][value=0]').prop('disabled', true);
    $('#iconField').val("");
    $('#iconField').prop('disabled', true);
    
  } else if ($('#urlField').val()!='') {
    $('#groupField').prop('disabled', true);
    $('#navPulldown').prop('disabled', true);
  }

  $('#navPulldown').on('change', function() {
    if ($(this).val()=='') {
      $('#groupField').val(saveval);
      $('#urlField').prop('disabled', false);
      $('#parentPulldown').prop('disabled', false);
      
      //$('input[name=surfcms_content_show_in_nav][value=1]').prop('checked', true);
      //$('input[name=surfcms_content_show_in_box][value=1]').prop('checked', true);
      //$('input[name=surfcms_content_show_in_nav]').prop('disabled', false);
      $('input[name=surfcms_content_show_in_box]').prop('disabled', false);
      $('input[name=surfcms_content_show_in_nav][value=0]').prop('disabled', false);
	  
	  $('#iconField').val(saveiconval);
      $('#iconField').prop('disabled', false);
      
    } else {
      $('#groupField').val($(this).val());
      $('#urlField').prop('disabled', true);
      $('#parentPulldown').val("").change();
      $('#parentPulldown').prop('disabled', true);
      
      //$('input[name=surfcms_content_show_in_nav][value=1]').prop('checked', true);
      $('input[name=surfcms_content_show_in_box][value=0]').prop('checked', true);
      //$('input[name=surfcms_content_show_in_nav]').prop('disabled', true);
      $('input[name=surfcms_content_show_in_box]').prop('disabled', true);
      if ($('input[name=surfcms_content_show_in_nav][value=0]').prop('checked')==true) { 
          $('input[name=surfcms_content_show_in_nav][value=2]').prop('checked',true) 
      }
      $('input[name=surfcms_content_show_in_nav][value=0]').prop('disabled', true);
	  
      saveiconval = $('#iconField').val();
	  $('#iconField').val("");
      $('#iconField').prop('disabled', true);

    }
  });

  $('#navPulldown').on('click', function() {
    if ($(this).val()=='') {
      saveval = $('#groupField').val();
    }
  });
  
  $('#groupField').on('keyup', function(ev){
    if ($(this).val()=='') {
      $('#urlField').prop('disabled', false);
    } else {
      $('#urlField').prop('disabled', true);
    }
   });

  $('#urlField').on('keyup', function(ev){
    if ($(this).val()=='') {
      $('#groupField').prop('disabled', false);
      $('#navPulldown').prop('disabled', false);
    } else {
      $('#groupField').prop('disabled', true);
      $('#navPulldown').prop('disabled', true);
    }
   });
   
  $('form[name=<?=$form_action?>]').submit(function() {
      $('#urlField').prop('disabled', false);
      $('#parentPulldown').prop('disabled', false);
      $('input[name=surfcms_content_show_in_nav]').prop('disabled', false);
      $('input[name=surfcms_content_show_in_box]').prop('disabled', false);
  });

<?php } ?>

});
</script>

<?php 
  } else {

	echo $surfcms->getDef('text_content_no_access');

  } // if ( !$surfcms->isRestricted($admin['id'], 'new_content') )
?>
