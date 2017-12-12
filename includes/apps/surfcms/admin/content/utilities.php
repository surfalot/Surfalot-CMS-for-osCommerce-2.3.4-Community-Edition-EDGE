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
<div style="width: 100%;">
  <div style="margin-left:10px;width: 75%;">
    <p><?=$surfcms->getDef('text_utilities_description')?></p>
    <p>&nbsp;</p>
    <?php 
    if ( is_array($surfcms_modules) && count($surfcms_modules) ) { 
	  $output = '';
      foreach($surfcms_modules as $name => $module) {
		if ( !$surfcms->isRestricted($admin['id'], $name) ) {
          $output .= '      <h3>' . $module['class']::i()->content_title() . '</h3>' . PHP_EOL;
          $output .= '      <div>' . PHP_EOL . $module['class']::i()->content() . PHP_EOL . '      </div>' . PHP_EOL;
		}
      }
	  if ( !empty($output) )
	    echo '<div id="surfcmsAppUtilitiesMenu">' . PHP_EOL . $output . PHP_EOL . '</div>' . PHP_EOL;
	  else
	    echo $surfcms->getDef('text_utilities_none_available');
	}
    ?>
    <p>&nbsp;</p>
  </div>
</div>

<div style="width: 100%;">
  <div style="margin-left: 10px; padding: 4px; float: left;">
      <?php echo tep_draw_button(IMAGE_BACK, 'arrow-1-w', tep_href_link('surfcms.php', $sticky_get_params . 'action=' . (isset($_GET['origin']) ? tep_db_prepare_input($_GET['origin']) : 'list'))); ?>
  </div>
</div>
<?php if ( !empty($output) ) { ?>
<script type="text/javascript">
$(document).ready(function(){

  $('#surfcmsAppUtilitiesMenu').accordion({
    heightStyle: 'content',
    collapsible: true<?php
      $count = 0;
      if ( is_array($surfcms_modules) && count($surfcms_modules) ) 
        foreach($surfcms_modules as $name => $module) {
          if ($name == $_GET['utility']) {
            echo ',' . "\n" . '    active: ' . $count;
            break;
          }
          $count++;
      } ?>

  });
  
});
</script>
<?php } ?>