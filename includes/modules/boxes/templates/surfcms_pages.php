<?php 
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/
?>
<div class="panel panel-default">
  <div class="panel-heading"><?php echo SURFCMS_BOX_TITLE; ?></div>
<?php if (SURFCMS_BOX_STYLE == 'list') { ?>
  <div class="panel-body">
    <ul class="list-unstyled">
<?php } else { ?>
    <ul class="nav nav-pills nav-stacked">
<?php } ?>
<?php 
    foreach ($nav_array as $node) {
		echo '      <li><a href="' . $node['link'] . '" title="' . $node['meta_title'] . '">' . $node['menu_text'] . '</a></li>'."\n";
	} 
?>
    </ul>
<?php if (SURFCMS_BOX_STYLE == 'list') { ?>
  </div>
<?php } ?>
</div>
