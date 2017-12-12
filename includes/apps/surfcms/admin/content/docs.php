<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  $page = basename($_GET['docs']);
  
?>
<script type="application/javascript">
//
// fix links and anchors created for package folders
//
$().ready(function() {
  // fix using a <base href="" /> and hash tag page anchors
  $("a[href^='\#']").each(function(){ 
    this.href=location.href.split("#")[0]+'#'+this.href.substr(this.href.indexOf('#')+1);
  });
  // make links that work in add-on package, work in live project
  if ($('#doc-img-src').length) $('#doc-img-src').prop('src', $('#doc-img-src').prop('src').replace('images/', '../includes/apps/surfcms/docs/images/'));
  $('.installed_hidden').hide();
<?php if (file_exists('../includes/apps/surfcms/languages/english/admin/support.php')) { 
      $surfcms->loadLanguageFile('../english/admin/support.php'); ?>
      $('.donate-block').html('<?=$surfcms->getDef('text_content_message')?>');
<?php } ?>
});
</script>

<link href="../includes/apps/surfcms/docs/css/styles.css" rel="stylesheet">
<div class="container" style="width: 100%;">
  <div style="float: left;">
    <div style="padding: 2px;">
      <?php include_once(DIR_FS_CATALOG . 'includes/apps/surfcms/docs/' . $page . '.html'); ?>
    </div>
  </div>
</div>

<div class="container" style="width: 100%;">
  <div style="margin-left: 10px; padding: 4px; float: left;">
      <?php echo tep_draw_button(IMAGE_BACK, 'arrow-1-w', tep_href_link('surfcms.php', $sticky_get_params . 'action=' . (isset($_GET['origin']) ? tep_db_prepare_input($_GET['origin']) : 'list'))); ?>
  </div>
</div>
<p>&nbsp;</p>

