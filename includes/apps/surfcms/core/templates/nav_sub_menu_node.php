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
                <li class="dropdown"> 
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php if (!empty($node['icon'])) { ?><i class="fa <?=$node['icon']?>"></i>&nbsp;<?php } ?><span class="hidden-sm"> <?=$node['menu_text']?></span> <span class="caret"></span></a>
                  <ul class="dropdown-menu">
<?php foreach ($node['sub'] as $subnode) { ?>
                    <li><a href="<?=$subnode['link']?>" title="<?=$subnode['meta_title']?>"><?php if (!empty($subnode['icon'])) { ?><i class="fa <?=$subnode['icon']?>"></i>&nbsp;<?php } ?><span class="hidden-sm"> <?=$subnode['menu_text']?></span></a></li>
<?php } ?>
                  </ul>
                </li>
