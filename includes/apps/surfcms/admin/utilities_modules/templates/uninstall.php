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
        <p><?php echo $this->surfcms->getDef('text_utilities_uninstall_body'); ?></p>
        <?php 
          if ( !isset($_GET['confirm']) ) {
            echo '<p>' . tep_draw_button($this->surfcms->getDef('text_utilities_uninstall_button'), 'trash', tep_href_link('surfcms.php', 'action=utilities&utility=uninstall&confirm')) . '</p>'; 
          } else {
            echo '<p><strong>' . $this->surfcms->getDef('text_utilities_uninstall_confirm') . '</strong></p>';
            echo '<p>' . tep_draw_button($this->surfcms->getDef('text_utilities_uninstall_button_confirm'), 'trash', tep_href_link('surfcms.php', 'action=utilities&utility=uninstall&uninstall=commit')) . '</p>';
            echo '<p>' . tep_draw_button(IMAGE_CANCEL, 'arrow-1-w', tep_href_link('surfcms.php', 'action=utilities&utility=uninstall')) . '</p>';
          }
        ?>
