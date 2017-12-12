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
        <p><?php echo $this->surfcms->getDef('text_utilities_insert_sample_body'); ?></p>
        <?php 
          if ( !isset($_GET['confirm']) ) {
            echo '<p>' . tep_draw_button($this->surfcms->getDef('text_utilities_insert_sample_button'), 'arrowthickstop-1-e', tep_href_link('surfcms.php', 'action=utilities&utility=insert_sample&confirm')) . '</p>'; 
          } else {
            echo '<p><strong>' . $this->surfcms->getDef('text_utilities_insert_sample_confirm') . '</strong></p>';
            echo '<p>' . tep_draw_button($this->surfcms->getDef('text_utilities_insert_sample_button_confirm'), 'arrowthickstop-1-e', tep_href_link('surfcms.php', 'action=utilities&utility=insert_sample&insert_sample=commit')) . '</p>';
            echo '<p>' . tep_draw_button(IMAGE_CANCEL, 'arrow-1-w', tep_href_link('surfcms.php', 'action=utilities&utility=insert_sample')) . '</p>';
          }
        ?>
