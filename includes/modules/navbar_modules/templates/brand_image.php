<?php
// in a template so that shopowners 
// don't have to change the main file! 

$seo = (defined('META_SEO_TITLE') ? tep_output_string(META_SEO_TITLE) . MODULE_HEADER_TAGS_PAGES_SEO_SEPARATOR . STORE_NAME : STORE_NAME);

?><a class="navbar-brand" href="<?php echo tep_href_link('index.php'); ?>" style="padding-top:0;padding-bottom:0;"><?php echo tep_image('images/' . (defined('MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME') && tep_not_null(MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME)? MODULE_NAVBAR_BRAND_IMAGE_FILE_NAME : STORE_LOGO), $seo); ?></a>
