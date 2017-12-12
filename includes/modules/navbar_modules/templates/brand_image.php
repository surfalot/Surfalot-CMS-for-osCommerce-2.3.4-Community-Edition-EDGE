<?php
// in a template so that shopowners 
// don't have to change the main file! 

?>
<a class="navbar-brand" href="<?php echo tep_href_link('index.php'); ?>" style="padding-top:0;padding-bottom:0;"><?php echo tep_image('images/' . (defined('MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME') && tep_not_null(MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME)? MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME : STORE_LOGO), STORE_NAME/*, $width = '', $height = '26px', $parameters = '', $responsive = true, $bootstrap_css = ''*/); ?></a>
