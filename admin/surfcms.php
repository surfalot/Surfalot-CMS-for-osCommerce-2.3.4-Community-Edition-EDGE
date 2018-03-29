<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!class_exists('surfcms')) 
     require(DIR_FS_CATALOG . 'includes/apps/surfcms/surfcms.class.php');
  
  if ( !isset($surfcms) || !is_object($surfcms) || (get_class($surfcms) != 'surfcms') )
    $surfcms = new surfcms();

  $page = 'surfcms.php';
  $action =  (isset($_GET['action']) ? basename($_GET['action']) : 'list');
  $subaction = (isset($_GET['subaction']) ? basename($_GET['subaction']) : '');
  $sticky_get_params = tep_get_all_get_params(array('ocID','type','subtype','action','subaction','commit_action','docopy','flag','navflag','boxflag','wysiwyg','origin'));

  $surfcms->loadLanguageFile('surfcms.admin.php');

  if ( !empty($action) && file_exists(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/actions/' . $action . '.php') ) {
	$surfcms->loadLanguageFile('admin/' . $action . '.php');
	include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/actions/' . $action . '.php');
  }

  if ( !empty($subaction) && file_exists(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/actions/' . $action . '/' . $subaction . '.php') ) {
	include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/actions/' . $action . '/' . $subaction . '.php');
  }
  
  if (empty($content)) $content = $action . '.php';
  
  include('includes/template_top.php');
  echo '<link href="../includes/apps/surfcms/admin/css/styles.css" rel="stylesheet">' . "\n";

  // collect docs
  $docs = array();
  foreach ( glob ('../includes/apps/surfcms/docs/*.html') as $file ) {
    $docs[] = basename($file, '.html');
  }
  natsort($docs);

?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading surfcms-logo-td"><div class="surfcms-logo">&nbsp;</div></td>
            <td class="pageHeading surfcms-logo-tx"><?php echo $page_title; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right" style="padding-right:40px; line-height: 1.6em;"><?php 
				   echo $surfcms->getTitleAndVersion() . '<br />';
				   
				   for ($c=count($docs)-1; $c>=0; $c--) {
					 echo '<a href="' . tep_href_link($page, $sticky_get_params . 'action=docs&docs=' . $docs[$c]) . '" class="info-link">' . $docs[$c] . '</a> &nbsp; ';
				   }

				   echo '<a href="' . tep_href_link('configuration.php', 'gID=' . $surfcms->getConfigID()) . '" class="info-link">' . $surfcms->getDef('app_link_config') . '</a>';
            ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if ( $surfcms->hasAlert() ) {
?>
      <tr>
        <td><?php echo $surfcms->getAlerts(); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php 	  
  }
?>
    </table>

    <?php include(DIR_FS_CATALOG . 'includes/apps/surfcms/admin/content/' . basename($content)); ?>

<?php
  include('includes/template_bottom.php');
  require('includes/application_bottom.php');
?>
