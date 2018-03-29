<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

	$results = (isset($_GET['results']) ? (int)$_GET['results'] : MAX_DISPLAY_SEARCH_RESULTS);

    $surfcms_content_count = 0;

    // setup types dropdown
    $types_array = array( array('id' => '', 'text' => $surfcms->getDef('text_list_all_types')),
                          array('id' => '0', 'text' => $surfcms->typeName('0')),
                          array('id' => '1', 'text' => $surfcms->typeName('1')),
                          array('id' => '2', 'text' => $surfcms->typeName('2')),
                          array('id' => '3', 'text' => $surfcms->typeName('3')),
                          array('id' => '4', 'text' => $surfcms->typeName('4')) );

    $type_where = '';
    if ( tep_not_null($_GET['list_type']) && $_GET['list_type']==='00' ) {
      $type_where = " and oc.surfcms_content_type in ('0','1')";
    } elseif ( tep_not_null($_GET['list_type']) ) {
      $type_where = " and oc.surfcms_content_type = '".(int)$_GET['list_type']."'";
    }

    $group_where = '';
    if ( tep_not_null($_GET['list_group']) )
      $group_where = " and oc.surfcms_content_group like '" . tep_db_prepare_input($_GET['list_group']) . "%'";

    $search_where = '';
    if ( tep_not_null($_GET['search']) )
      $search_where = " and (oc.surfcms_content_group like '%" . tep_db_prepare_input($_GET['search']) . "%' or ocd.surfcms_content_menu_text like '%" . tep_db_prepare_input($_GET['search']) . "%')";

    // setup groups dropdown
    $groups_array = array(array('id' => '', 'text' => $surfcms->getDef('text_list_all_groups')));
    $surfcms_groups_query = tep_db_query("SELECT oc.surfcms_content_group, oc.surfcms_content_type FROM surfcms_content oc WHERE oc.surfcms_content_group != ''" . $type_where . $group_where . " GROUP BY oc.surfcms_content_group");
    while ($surfcms_groups_row = tep_db_fetch_array($surfcms_groups_query))
      $groups_array[] = array('id' => $surfcms_groups_row['surfcms_content_group'], 'text' => $surfcms_groups_row['surfcms_content_group']);

    // get list data
    $surfcms_content_query_raw =  "select oc.surfcms_content_id, 
                                          oc.surfcms_content_group, 
                                          oc.surfcms_content_status, 
                                          oc.surfcms_content_width, 
                                          oc.surfcms_content_sort_order, 
                                          oc.surfcms_content_type, 
                                          oc.surfcms_content_menu_parent, 
                                          oc.surfcms_content_show_in_nav, 
                                          oc.surfcms_content_show_in_box,
                                          oc.surfcms_content_administrators, 
                                          ocd.surfcms_content_title, 
                                          ocd.surfcms_content_menu_text, 
                                          ocd.surfcms_content_navbar_title, 
                                          ocd.surfcms_content_meta_title, 
                                          ocd.surfcms_content_meta_description, 
                                          ocd.surfcms_content_meta_keywords 
                                   from surfcms_content oc, 
                                        surfcms_content_description ocd 
                                   where oc.surfcms_content_id = ocd.surfcms_content_id 
                                         " . $type_where . "
                                         " . $group_where . "
                                         " . $search_where . "
                                         and ocd.language_id = '" . (int)$languages_id . "' 
                                   order by oc.surfcms_content_group, 
                                            oc.surfcms_content_type, 
                                            oc.surfcms_content_sort_order,
                                            ocd.surfcms_content_menu_text";

    $surfcms_content_split = new splitPageResults($_GET['page'], $results, $surfcms_content_query_raw, $surfcms_content_query_numrows);
    $surfcms_content_query = tep_db_query($surfcms_content_query_raw);

    while ($surfcms_content_row = tep_db_fetch_array($surfcms_content_query))
        if ( !in_array($admin['id'], explode(',',$surfcms_content_row['surfcms_content_administrators'])) ) 
          $content_array[] = $surfcms_content_row;
    
    if ( is_array($groups_array) && count($groups_array) ) {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo tep_draw_form('search', 'surfcms.php', '', 'get') . tep_draw_input_field('search', '', 'style="width:100px;height:1em;" placeholder="'.$surfcms->getDef('text_content_search').'"') . tep_hide_session_id() . '</form>' . '&nbsp;' . 
                       tep_draw_pull_down_menu('content_types', $types_array, ((isset($_GET['list_type'])) ? $_GET['list_type'] : ''), 'id="typePulldown"') . ' ' . 
                       tep_draw_pull_down_menu('group_names', $groups_array, ((isset($_GET['list_group'])) ? $_GET['list_group'] : ''), 'id="groupPulldown"') . ' ' . 
					   tep_draw_form('results', 'surfcms.php', '', 'get') . tep_draw_pull_down_menu('results', array(array('id'=>'20','text'=>'20 '.$surfcms->getDef('text_content_list_results_label')),array('id'=>'30','text'=>'30 '.$surfcms->getDef('text_content_list_results_label')),array('id'=>'50','text'=>'50 '.$surfcms->getDef('text_content_list_results_label')),array('id'=>'100','text'=>'100 '.$surfcms->getDef('text_content_list_results_label')),array('id'=>'9000','text'=>$surfcms->getDef('text_content_list_max_all'))), (isset($results) ? $results : ''), 'id="resultsPulldown"') . tep_hide_session_id() . '</form>'; ?></td>
      </tr>
    </table>
<?php } ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left"><?php echo $surfcms->getDef('table_heading_content_title'); ?></td>
                <td class="dataTableHeadingContent"><?php echo $surfcms->getDef('table_heading_content_name'); ?></td>
                <td class="dataTableHeadingContent"><?php echo $surfcms->getDef('table_heading_content_type'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo $surfcms->getDef('table_heading_content_sort_order'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo $surfcms->getDef('table_heading_content_status'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo $surfcms->getDef('table_heading_content_innav'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo $surfcms->getDef('table_heading_content_inbox'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo $surfcms->getDef('table_heading_content_action'); ?>&nbsp;</td>
              </tr>
<?php
  if ( is_array($content_array) && count($content_array) ) {
    foreach ($content_array as $surfcms_content) {

        $surfcms_content_count++;
        $rows++;
  
        if ( (!isset($_GET['ocID']) || (isset($_GET['ocID']) && ($_GET['ocID'] == $surfcms_content['surfcms_content_id']))) && !isset($ocInfo) && (substr($action, 0, 3) != 'new')) {
          $ocInfo = new objectInfo($surfcms_content);
        }
  
        if (isset($ocInfo) && is_object($ocInfo) && ($surfcms_content['surfcms_content_id'] == $ocInfo->surfcms_content_id) ) {
          echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $surfcms_content['surfcms_content_id'] . '&action=new_content') . '\'">' . "\n";
        } else {
          echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $surfcms_content['surfcms_content_id']) . '\'">' . "\n";
        }
?>
                  <td class="dataTableContent"><?php echo '&nbsp;' . $surfcms_content['surfcms_content_menu_text']; ?></td>
                  <td class="dataTableContent"><?php echo $surfcms_content['surfcms_content_group']; ?></td>
                  <td class="dataTableContent"><?php echo ucfirst($surfcms->typeName($surfcms_content['surfcms_content_type'])); ?></td>
                  <td class="dataTableContent" align="center"><?php echo $surfcms_content['surfcms_content_sort_order']; ?></td>
                  <td class="dataTableContent" align="center">
 <?php
        if ($surfcms_content['surfcms_content_status'] == '1') {
          echo tep_image('../includes/apps/surfcms/admin/images/list_green.gif', IMAGE_ICON_STATUS_GREEN, 14, 14) . '&nbsp;&nbsp;<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&flag=0&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 14, 14) . '</a>';
        } else {
          echo '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&flag=1&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 14, 14) . '</a>&nbsp;&nbsp;' . tep_image('../includes/apps/surfcms/admin/images/list_red.gif', IMAGE_ICON_STATUS_RED, 14, 14);
        }
?>
                  </td>
<?php
$nav_green_action_icons = array();
$nav_green_action_icons['0'] = '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&navflag=2&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 14, 14) . '</a>';
$nav_green_action_icons['1'] = '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&navflag=2&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_home.gif', $surfcms->getDef('text_list_home'), 14, 14) . '</a>';
$nav_green_action_icons['2'] = '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&navflag=3&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_left_arrow.gif', $surfcms->getDef('text_list_left'), 14, 14) . '</a>';
$nav_green_action_icons['3'] = '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&navflag=1&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_right_arrow.gif', $surfcms->getDef('text_list_right'), 14, 14) . '</a>';
?>
<?php // nav status for menu item settings that allow both navbar and infobox to be enabled and disabled separately
      if ((int)$surfcms_content['surfcms_content_type']==0 || ((int)$surfcms_content['surfcms_content_type']==4 && empty($surfcms_content['surfcms_content_group']))) { ?>

                  <td class="dataTableContent" align="center">
<?php   
        if ($surfcms_content['surfcms_content_show_in_nav'] == '0') {
          echo $nav_green_action_icons[$surfcms_content['surfcms_content_show_in_nav']] . '&nbsp;&nbsp;' . tep_image('../includes/apps/surfcms/admin/images/list_red.gif', IMAGE_ICON_STATUS_RED, 14, 14);
        } else {
          echo $nav_green_action_icons[$surfcms_content['surfcms_content_show_in_nav']] . '&nbsp;&nbsp;<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&navflag=0&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 14, 14) . '</a>';
        }
?>
                  </td>
                  <td class="dataTableContent" align="center">
<?php
        if ($surfcms_content['surfcms_content_show_in_box'] == '1') {
          echo tep_image('../includes/apps/surfcms/admin/images/list_green.gif', IMAGE_ICON_STATUS_GREEN, 14, 14) . '&nbsp;&nbsp;<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&boxflag=0&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 14, 14) . '</a>';
        } else {
          echo '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=setflag&boxflag=1&ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('../includes/apps/surfcms/admin/images/list_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 14, 14) . '</a>&nbsp;&nbsp;' . tep_image('../includes/apps/surfcms/admin/images/list_red.gif', IMAGE_ICON_STATUS_RED, 14, 14);
        }
?>
                  </td>

<?php // nav status for menu item settings that prohibit the nav to disable since it can't appear in the info box, with these settings disabling is via status button only
      } elseif ((int)$surfcms_content['surfcms_content_type']==3 || ((int)$surfcms_content['surfcms_content_type']==2 && strpos($surfcms_content['surfcms_content_group'],'navbar_')===0) || ((int)$surfcms_content['surfcms_content_type']==4 && !empty($surfcms_content['surfcms_content_group']))) { ?>

                  <td class="dataTableContent" align="center">
<?php   
        if ($surfcms_content['surfcms_content_status'] == '1') {
          echo $nav_green_action_icons[$surfcms_content['surfcms_content_show_in_nav']] . '&nbsp;&nbsp;' . tep_image('images/pixel_trans.gif', '', 14, 14);
        } else {
          echo tep_image('images/pixel_trans.gif', '', 14, 14) . '&nbsp;&nbsp;' . tep_image('../includes/apps/surfcms/admin/images/list_red.gif', IMAGE_ICON_STATUS_RED, 14, 14);
        }
?>
                  </td>
                  <td class="dataTableContent" align="center">&nbsp;</td>
                  
<?php } else { ?>

                  <td class="dataTableContent" align="center">&nbsp;</td>
                  <td class="dataTableContent" align="center">&nbsp;</td>
                  
<?php } ?>
                  <td class="dataTableContent" align="right"><?php if (isset($ocInfo) && is_object($ocInfo) && ($surfcms_content['surfcms_content_id'] == $ocInfo->surfcms_content_id)) { echo tep_image('images/icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $surfcms_content['surfcms_content_id'] . '&action=new_content') . '">' . tep_image('../includes/apps/surfcms/admin/images/icon_edit.gif', $surfcms->getDef('text_list_edit')) . '</a>' . ' ' . '<a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $surfcms_content['surfcms_content_id']) . '">' . tep_image('images/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                </tr>
 <?php 

    } // foreach ($content_array as $surfcms_content)
  } // if ( is_array($content_array) && count($content_array) )
?>

              <tr>
                <td colspan="8"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $surfcms_content_split->display_count($surfcms_content_query_numrows, $results, $_GET['page'], $surfcms->getDef('table_heading_content_pages')); ?></td>
                    <td class="smallText" align="right"><?php echo str_replace(array('?page','"get">'),array('?results='.$results.'&page','"get"><input type="hidden" name="results" value="'.$results.'">'),$surfcms_content_split->display_links($surfcms_content_query_numrows, $results, MAX_DISPLAY_PAGE_LINKS, $_GET['page'])); ?></td>
                  </tr>
                </table><br /></td>
              </tr>
                
              <tr>
                <td colspan="8">
                <?php echo tep_draw_form('new_content', 'surfcms.php', null, 'get') . tep_draw_hidden_field('action', 'new_content') . tep_surfcms_get_all_get_params_as_hidden(array('ocID','action','subaction','delete_content','wysiwyg','type')); ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td align="right" class="smallText"><?php if (SURFCMS_ENABLE_WYSIWYG == 'True') echo '<div style="position:relative; display: inline-block; top:5px;">' . tep_draw_checkbox_field('wysiwyg', 'true', true) . ' <div style="position:relative; display: inline-block; top:-3px">' . $surfcms->getDef('text_use_wysiwyg') . '</div></div>'; ?>
                                                        <?php if (!$surfcms->isRestricted($admin['id'], 'new_page')) echo tep_draw_button($surfcms->getDef('text_new_type').' '.ucfirst($surfcms->typeName('0')), 'plus', null, null, array('params' => 'name="type" value="0"')); ?> 
                                                        <?php if (SURFCMS_ENABLE_CONTENT_GROUP_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_page_content')) echo tep_draw_button($surfcms->getDef('text_new_type').' '.ucwords($surfcms->typeName('1')), 'plus', null, null, array('params' => 'name="type" value="1"')); ?> 
                                                        <?php if (SURFCMS_ENABLE_MENU_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_menuing')) echo tep_draw_button($surfcms->getDef('text_new_type').' '.ucwords($surfcms->typeName('4')), 'plus', null, null, array('params' => 'name="type" value="4"')); ?> 
                                                        <?php if (SURFCMS_ENABLE_MENU_LINKS == 'True' && !$surfcms->isRestricted($admin['id'], 'new_menuing')) echo tep_draw_button($surfcms->getDef('text_new_type').' '.ucwords($surfcms->typeName('3')), 'plus', null, null, array('params' => 'name="type" value="3"')); ?> 
                                                        <?php if (SURFCMS_ENABLE_BLOCK_LINK == 'True' && !$surfcms->isRestricted($admin['id'], 'new_blocks')) echo tep_draw_button($surfcms->getDef('text_new_type').' '.ucwords($surfcms->typeName('2')), 'plus', null, null, array('params' => 'name="type" value="2"')); ?>&nbsp;</td>
                  </tr>
                </table>
                <?php echo '</form>'; ?>
                </td>
              </tr>
            </table>
            
<div id="help-switch">Help ?</div>
<div id="help-detail" style="width: 100%;display:none;"></div>
<?php
if ( SURFCMS_DISPLAY_MENU_SAMPLE_BOX == "True" && !$surfcms->isRestricted($admin['id'], 'menuing_boxes')) {
    // generate sample box and nav 
    $box_array = $surfcms->getNavigation('box');
    $nav_array = $surfcms->getNavigation('navbar');

    if ((is_array($box_array) && count($box_array)) || (is_array($nav_array) && count($nav_array))) {
?>
<div style="width: 100%;">
  <div style="float: left; width: 50%;">
    <div style="padding: 2px;">
      <h3 class="surfcms-panel-header-basic"><?php echo $surfcms->getDef('text_nav_sample'); ?></h3>
      <div class="surfcms-panel surfcms-panel-basic">
        <ul>
        <?php
          if (is_array($nav_array) && count($nav_array))
            foreach ($nav_array as $pos => $nav) { 
              switch ($pos) {
                  case '1': echo $surfcms->getDef('text_content_sample_home');  break;  
                  case '2': echo $surfcms->getDef('text_content_sample_left');  break;  
                  case '3': echo $surfcms->getDef('text_content_sample_right');  break;  
              }
              foreach ($nav as $node) { 
                  if ( !is_array($node['sub']) ) { 
                      if ( !empty($admin['id']) && in_array($admin['id'], explode(',',$node['admins']) ) ) {
                        $admin_links = '';
                      } else {
                        $admin_links = ' &nbsp; <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content') . '" title="' . $surfcms->getDef('text_list_edit_raw') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_raw_letter') . '</a>
                                                <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content&wysiwyg=true') . '" title="' . $surfcms->getDef('text_list_edit') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_letter') . '</a> 
                                                <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id']) . '" title="' . $surfcms->getDef('text_list_info') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_info_letter') . '</a>';
                      }
                      if ( tep_not_null($node['link']) ) { 
                          $this_link = '<a href="' . $node['link'] . '" title="' . $node['meta_title'] . '" target="_blank">'; 
                          $this_link_end = '</a>'; 
                      } else {
                          $this_link = '<span style="color:#000;">'; 
                          $this_link_end = '</span>'; 
                      } ?>
                      <li><?php echo $this_link . $node['menu_text'] . $this_link_end; ?> (<?php echo $node['sort_order']; ?>)<?php echo $admin_links; ?></li><?php
                  } else { ?>
                      <li class="dropdown"> 
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $node['menu_text']; ?></a> (<?php echo $node['sort_order']; ?>)<?php echo $admin_links; ?>
                        <ul class="dropdown-menu"><?php
                        foreach ($node['sub'] as $subnode) { 
                          if ( !empty($admin['id']) && in_array($admin['id'], explode(',',$node['admins']) ) ) {
                            $admin_links = '';
                          } else {
                            $admin_links = ' &nbsp; <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $subnode['id'] . '&action=new_content') . '" title="' . $surfcms->getDef('text_list_edit_raw') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_raw_letter') . '</a>
                                                    <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $subnode['id'] . '&action=new_content&wysiwyg=true') . '" title="' . $surfcms->getDef('text_list_edit') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_letter') . '</a> 
                                                    <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $subnode['id']) . '" title="' . $surfcms->getDef('text_list_info') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_info_letter') . '</a>';
                          } ?>
                            <li><a href="<?php echo $subnode['link']; ?>" title="<?php echo $subnode['meta_title']; ?>" target="_blank"><?php echo $subnode['menu_text']; ?></a> (<?php echo $subnode['sort_order']; ?>)<?php echo $admin_links; ?></li><?php
                        } ?>
                        </ul>
                      </li><?php
                  }
              }
              echo '<br />';
            }
        ?>
        </ul>
    </div>
    </div>
  </div>
  <div style="float: left; width: 50%;">
    <div style="padding: 2px;">
      <h3 class="surfcms-panel-header-basic"><?php echo $surfcms->getDef('text_box_sample'); ?></h3>
      <div class="surfcms-panel surfcms-panel-basic">
        <ul>
          <?php 
            if (is_array($box_array) && count($box_array)) 
              foreach ($box_array as $node) {
                if ( !empty($admin['id']) && in_array($admin['id'], explode(',',$node['admins']) ) ) {
                  $admin_links = '';
                } else {
                  $admin_links = ' &nbsp; <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content&wysiwyg=true') . '" title="' . $surfcms->getDef('text_list_edit_raw') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_raw_letter') . '</a>
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content') . '" title="' . $surfcms->getDef('text_list_edit') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_letter') . '</a> 
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id']) . '" title="' . $surfcms->getDef('text_list_info') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_info_letter') . '</a>';
                }
                echo '      <li><a href="' . $node['link'] . '" title="' . $node['meta_title'] . '" target="_blank">' . $node['menu_text'] . '</a> ('. $node['sort_order'] .') &nbsp; ' . $admin_links . '</li>'."\n";
              }
              echo '<br />';
           ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php } ?>
<div style="clear: both;"></div>
<?php
if ( SURFCMS_DISPLAY_CONTENT_GROUP_BOX == "True" ||  SURFCMS_DISPLAY_BLOCK_GROUP_BOX == "True" ) {
?>
<div style="width: 100%;">
<?php if ( SURFCMS_DISPLAY_CONTENT_GROUP_BOX == "True" && !$surfcms->isRestricted($admin['id'], 'content_box') ) { 
         $content = $surfcms->getContentItems();
      if (is_array($content) && count($content)) {     
?>
  <div style="float: left; width: 50%;">
    <div style="padding: 2px;">
      <h3 class="surfcms-panel-header-basic"><?php echo $surfcms->getDef('text_content_list'); ?></h3>
      <div class="surfcms-panel surfcms-panel-basic">
        <ul>
          <?php 
            if (is_array($content) && count($content)) 
              foreach ($content as $node) {
                if ( !( !empty($admin['id']) && in_array($admin['id'], explode(',',$node['admins']) ) ) ) {
                  $admin_links = ' &nbsp; <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content&wysiwyg=true') . '" title="' . $surfcms->getDef('text_list_edit_raw') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_raw_letter') . '</a>
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content') . '" title="' . $surfcms->getDef('text_list_edit') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_letter') . '</a> 
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id']) . '" title="' . $surfcms->getDef('text_list_info') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_info_letter') . '</a>';
                  echo '      <li>' . (tep_not_null($node['menu_text']) ? $node['menu_text'] : $node['group'])  . ' &nbsp; ' . $admin_links . '</li>'."\n";
                } 
              } 
              echo '<br />';
          ?>
        </ul>
    </div>
    </div>
  </div>
<?php } ?>
<?php } ?>
<?php if ( SURFCMS_DISPLAY_BLOCK_GROUP_BOX == "True" && !$surfcms->isRestricted($admin['id'], 'blocks_box') ) { 
         $blocks = $surfcms->getBlockItems(); 
      if (is_array($blocks) && count($blocks)) {     
?>
  <div style="float: left; width: 50%;">
    <div style="padding: 2px;">
      <h3 class="surfcms-panel-header-basic"><?php echo $surfcms->getDef('text_blocks_list'); ?></h3>
      <div class="surfcms-panel surfcms-panel-basic">
        <ul>
            <?php 
            if (is_array($blocks) && count($blocks)) 
              foreach ($blocks as $node) {
                if ( !( !empty($admin['id']) && in_array($admin['id'], explode(',',$node['admins']) ) ) ) {
                  $admin_links = ' &nbsp; <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content&wysiwyg=true') . '" title="' . $surfcms->getDef('text_list_edit_raw') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_raw_letter') . '</a>
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id'] . '&action=new_content') . '" title="' . $surfcms->getDef('text_list_edit') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_edit_letter') . '</a> 
                                          <a href="' . tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $node['id']) . '" title="' . $surfcms->getDef('text_list_info') . '" class="surfcms-edit-links-basic">' . $surfcms->getDef('text_list_info_letter') . '</a>';
                  echo '      <li>' . (tep_not_null($node['menu_text']) ? $node['menu_text'] : $node['group']) . ' &nbsp; ' . $admin_links . '</li>'."\n";
                }
              } 
              echo '<br />';
            ?>
        </ul>
      </div>
    </div>
  </div>
<?php } ?>
<?php } ?>
</div>
<?php } ?>
            </td>
            
<?php
  $heading = array();
  $contents = array();

  switch ($subaction) {
    case 'delete_content':
      $heading[] = array('text' => '<b>' . $surfcms->getDef('text_info_heading_delete_content') . '</b>');

      $contents = array('form' => tep_draw_form('surfcms_content', 'surfcms.php', $sticky_get_params . 'action=list&subaction=delete_confirm') . tep_draw_hidden_field('surfcms_content_id', $ocInfo->surfcms_content_id));
      $contents[] = array('text' => $surfcms->getDef('text_delete_content_intro'));
      $contents[] = array('text' => '<br><b>' . $ocInfo->surfcms_content_group . ' (' . $ocInfo->surfcms_content_id . ')' . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary') . ' ' . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link('surfcms.php', $sticky_get_params . 'action=list&ocID=' . $ocInfo->surfcms_content_id)));
      break;
    default:
      if (isset($ocInfo) && is_object($ocInfo)) { // content info box contents
        $heading[] = array('text' => '<b>' . ucfirst($surfcms->typeName($ocInfo->surfcms_content_type)) . ": " . $ocInfo->surfcms_content_menu_text . '</b>');

        $contents[] = array('align' => 'center', 'text' => (SURFCMS_ENABLE_WYSIWYG == 'True' ? tep_draw_button($surfcms->getDef('text_use_wysiwyg'), 'document', tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $ocInfo->surfcms_content_id . '&action=new_content&wysiwyg=true')) . ' ' : '') . 
                                                           tep_draw_button(IMAGE_EDIT, 'document', tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $ocInfo->surfcms_content_id . '&action=new_content')) . '<br /><br />' . 
                                                           ($ocInfo->surfcms_content_type!='0' ? tep_draw_button(IMAGE_COPY, 'copy', tep_href_link('surfcms.php', $sticky_get_params . 'ocID=' . $ocInfo->surfcms_content_id . '&action=new_content&docopy')) . ' ' : '') . 
                                                           tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link('surfcms.php', $sticky_get_params . 'action=list&subaction=delete_content&ocID=' . $ocInfo->surfcms_content_id)) );
        $contents[] = array('text' => '<br>' . $surfcms->getDef('text_content_type') . ' ' . ucfirst($surfcms->typeName($ocInfo->surfcms_content_type)));
        $contents[] = array('text' => $surfcms->getDef('text_content_name') . ' ' . $ocInfo->surfcms_content_group);
        if ($ocInfo->surfcms_content_type == 0) {
          $contents[] = array('text' => '<br>' . $surfcms->getDef('text_content_title') . ' ' . $ocInfo->surfcms_content_title);
        }
        if ($ocInfo->surfcms_content_type == 0 || $ocInfo->surfcms_content_type == 3 || $ocInfo->surfcms_content_type == 4) {
          $contents[] = array('text' => $surfcms->getDef('text_content_link') . ' ' . str_replace((ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER),'',$surfcms->getLink($ocInfo->surfcms_content_id)));
        }
        $contents[] = array('text' => '<br>');
        $contents[] = array('text' => ($ocInfo->surfcms_content_menu_parent!=0 ? $surfcms->getDef('text_content_menu_parent') . ' ' . $ocInfo->surfcms_content_menu_parent : ''));
        $contents[] = array('text' => $surfcms->getDef('text_sort_order') . ' ' . $ocInfo->surfcms_content_sort_order);
        $contents[] = array('text' => $surfcms->getDef('text_content_width') . ' ' . $ocInfo->surfcms_content_width);
        $contents[] = array('text' => $surfcms->getDef('text_content_status') . ' ' . ($ocInfo->surfcms_content_status==1 ? $surfcms->getDef('text_content_status_active') : $surfcms->getDef('text_content_status_disabled')) );
        if ($ocInfo->surfcms_content_type == 0) {
          $contents[] = array('text' => '<br>' . $surfcms->getDef('text_content_meta_navbar_title') . ' ' . substr($ocInfo->surfcms_content_navbar_title,0,40));
          $contents[] = array('text' => $surfcms->getDef('text_content_meta_title') . ' ' . substr($ocInfo->surfcms_content_meta_title,0,40));
          $contents[] = array('text' => $surfcms->getDef('text_content_meta_description') . ' ' . substr($ocInfo->surfcms_content_meta_description,0,40));
          $contents[] = array('text' => $surfcms->getDef('text_content_meta_keywords') . ' ' . substr($ocInfo->surfcms_content_meta_keywords,0,40));
        }
        if (!empty($ocInfo->surfcms_content_administrators) && count($surfcms->admins())) {
          $contents[] = array('text' => '<br>' . $surfcms->getDef('text_content_restricted_users') . ' ' . implode(', ',$surfcms->admins(array_flip(explode(',',$ocInfo->surfcms_content_administrators)))) );
        }
        $contents[] = array('text' => '<br>');
      } else {
        $heading[] = array('text' => '<b>' . $surfcms->getDef('text_no_items_sidebar_title') . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<p>&nbsp;</p>');
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="30%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>

<script>
$(document).ready(function(){
    
  // Quick Help Button
  if ($('#help-switch').length) {
    $("#help-switch").click(function() {
        $("#help-detail").html('<?php echo tep_image((ENABLE_SSL_CATALOG=='false' ? HTTP_CATALOG_SERVER : HTTPS_CATALOG_SERVER) . '/includes/apps/surfcms/docs/images/doc_demo_help_area.jpg', null, null, null, 'width="100%"'); ?>');
        $("#help-detail").toggle();
    });
  }
    
<?php if (is_array($groups_array)) { ?>
    $(function(){
      // bind change event to group pulldown select
      $('#groupPulldown').bind('change', function () {
          var group = $(this).val(); // get selected value
          window.location = '<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>?<?php echo '&' . tep_get_all_get_params(array('ocID','subtype','list_group','page')) ?>' + ((group)? 'list_group=' + group : ''); // redirect
          return false;
      });
      // bind change event to type pulldown select
      $('#typePulldown').bind('change', function () {
          var type = $(this).val(); // get selected value
          window.location = '<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>?<?php echo '&' . tep_get_all_get_params(array('ocID','subtype','list_type','page')) ?>' + ((type)? 'list_type=' + type : ''); // redirect
          return false;
      });
      // bind change event to results select
      $('#resultsPulldown').bind('change', function () {
          var results = $(this).val(); // get selected value
          window.location = '<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>?<?php echo '&' . tep_get_all_get_params(array('results','page')) ?>' + ((results)? 'results=' + results : ''); // redirect
          return false;
      });
    });
<?php } ?>
});
</script>
