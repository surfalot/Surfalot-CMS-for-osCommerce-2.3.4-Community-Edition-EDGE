<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  /* surfcms_app abstract class */
  require_once DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_app.abstract.php';

  /* shortcode_modules singleton class */
  if ( file_exists(DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_shortcode_modules.class.php') )
    require_once DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_shortcode_modules.class.php';


  // ////////////////////////////////////////////////////////////////////////////////
  // Page Abstraction
  //
  abstract class surfcms_page extends surfcms_app {
    
    private $id;
    private $group;
    private $type;
    
    private $url;
    private $menu_parent;
    private $menu_icon;
    private $width;
    private $sort_order;
    private $show_in_nav;
    private $show_in_box;
    private $status;
    
    private $menu_text;
    private $title;
    private $body;
    private $navbar_title;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;
    
    protected $page = array();
    protected $group_content = array();
    

    // ////////////////////////////////////////////////////////////////////////////////
    // Data Collection Methods
    //
    public function getPageGroup($group_name = null, $languages_id = null, $defs = false) {
      global $SID;

        if ( $this->id() ) $this->cleanup();
        if ( !isset($languages_id) ) $languages_id = $this->languages_id;

        if ( tep_not_null($group_name) ) {

		    if ((USE_CACHE == 'true') && empty($SID)) {
			  $this->cachePageData($group_name, $languages_id);
		    } else {
			  $this->queryPageData($group_name, $languages_id);
		    }

            foreach ( $this->group_content as $k => $content ) {

              // do shortcodes
              if ( tep_not_null($content['body']) ) {
                $content['body'] = $this->replaceContentShortcodes($content['body']);
                $this->group_content[$k]['body'] = $content['body'];
			  }

              // add all content bodies that exacly match the $group_name to body var to set early on pages that use TEXT_INFORMATION
              if ( tep_not_null($content['body']) && $group_name == $content['group'] )
                $this->body .= ($content['width']!='0' ? $this->gridWrap($content['body'], $content['width']) : $content['body']);

			}

            if ( $defs )
              $this->definePageConstants();

        }
        return $this->getPageGroupContent();
    }
    
    private function queryPageData($group_name, $languages_id) {

      $groups = array_merge( array($group_name.'%'),                                                          // the page
	                         array('boxes_column_left','boxes_column_right','header_tags','footer_scripts'),  // block template areas NOTE: navbar_modules_home, navbar_modules_left, navbar_modules_right are being handled by SurfCMS Navigation Module
							 array('header','navigation','footer','footer_suffix') );                         // content template areas
	  
	  $where_group = " and (surfcms_content_group like '" . implode("' or surfcms_content_group like '", tep_db_prepare_input($groups)) . "')";
        
      // This will seek all content group names that start with current page. 
      // i.e. "index" will find "index_nested" also and process that.
      $item_query = tep_db_query("SELECT oc.surfcms_content_id as `id`, 
                                         oc.surfcms_content_group as `group`, 
                                         oc.surfcms_content_type as `type`, 
                                         oc.surfcms_content_menu_parent as `parent`, 
                                         oc.surfcms_content_menu_icon as `menu_icon`, 
                                         oc.surfcms_content_width as `width`, 
                                         oc.surfcms_content_sort_order as `sort_order`, 
                                         oc.surfcms_content_status as `status`, 
                                         ocd.surfcms_content_menu_text as `menu_text`, 
                                         ocd.surfcms_content_title as `title`, 
                                         ocd.surfcms_content_body as `body`, 
                                         ocd.surfcms_content_navbar_title as `navbar_title`, 
                                         ocd.surfcms_content_meta_title as `meta_title`, 
                                         ocd.surfcms_content_meta_description as `meta_description`, 
                                         ocd.surfcms_content_meta_keywords as `meta_keywords`
                                  FROM surfcms_content oc, 
                                       surfcms_content_description ocd 
                                  WHERE oc.surfcms_content_id = ocd.surfcms_content_id 
                                        " . $where_group . " 
                                        and ocd.language_id = '" . (int)$languages_id . "' 
                                        and oc.surfcms_content_type in ('0','1','2') 
                                        and oc.surfcms_content_status='1' 
                                  ORDER BY oc.surfcms_content_sort_order" );
  
      while ($item = tep_db_fetch_array($item_query)) {
  
        if ($item['type'] == '0') 
          $this->setPageDetail($item);
  
        $this->addGroupContent($item);
  
      }
      ksort($this->group_content);

      return true;
    }
    
    private function cachePageData($group_name, $languages_id, $auto_expire = false, $refresh = false) {
      $language = $this->getLanguageFromID($languages_id);
	  $cache_retrieve = array();
	  
	  if (tep_not_null($group_name)) {
        if (($refresh == true) || !read_cache($cache_retrieve, 'surfcms_content-' . $language . '.cache.' . $group_name, $auto_expire)) {

		  $this->queryPageData($group_name, $languages_id);
          write_cache( $this->group_content, 'surfcms_content-' . $language . '.cache.' . $group_name);
        
		} else {

		  $this->group_content = $cache_retrieve;
		  foreach ($this->group_content as $item)  {
		    if ($item['type'] == '0') {
			  $this->setPageDetail($item);
			  break;
			}
		  }
		  
		}
      }

	}
	
    private function setPageDetail($item) {

      $this->id($item['id']);
      $this->group($item['group']);
      $this->type((int)$item['type']);

      $this->url($item['url']);
      $this->menu_parent((int)$item['menu_parent']);
      $this->menu_icon($item['menu_icon']);
      $this->width((int)$item['width']);
      $this->sort_order((int)$item['sort_order']);
      $this->show_in_nav((int)$item['show_in_nav']);
      $this->show_in_box((int)$item['show_in_box']);
      $this->status((int)$item['status']);
      
      $this->menu_text($item['menu_text']);
      $this->title($item['title']);
      $this->body(''); // body collected in $this->getPageGroup()
      $this->navbar_title($item['navbar_title']);
      $this->meta_title($item['meta_title']);
      $this->meta_description($item['meta_description']);
      $this->meta_keywords($item['meta_keywords']);

      return true;
    }

    private function addGroupContent($content, $sort = null) {

      if (!is_numeric($sort)) $sort = (is_numeric($content['sort_order']) ? (int)$content['sort_order'] : 0);
      for ($i=0; isset($this->group_content[((int)$sort+$i)]); $i++) {}
	  
      if ( $content['type'] == '0' ) {
		$this->group_content[((int)$sort+$i)] = $content;
	  } else {
        $this->group_content[((int)$sort+$i)] = array( 'id' => $content['id'], 
                                                       'type' => $content['type'], 
                                                       'group' => $content['group'], 
                                                       'sort_order' => $content['sort_order'], 
                                                       'width' => $content['width'], 
                                                       'body' => $content['body'] );
	  }

      return true;
    }

    public function getPageGroupContent() {

      $return = false;
      if (count($this->group_content)) {
        $return = $this->group_content;
      }

      return $return;
    }

    // ////////////////////////////////////////////////////////////////////////////////
    // Replace shortcodes in content wrapper method
    //
    public function replaceContentShortcodes($body) {

      if ( class_exists( 'surfcms_shortcode_modules' ) ) 
	    return surfcms_shortcode_modules::i()->replaceShortcodes($body);
	  else
	    return $body;

    }

    // ////////////////////////////////////////////////////////////////////////////////
    // Define page constants
    //
    public function definePageConstants() {

	  // This App utilizes the ht_pages_seo.php module to display META tag info for description and keywords. 
	  // It has a conflict with other modules.
	  // So, avoid displaying meta twice when these modules: ht_category_seo.php or ht_manufacturer_seo.php or ht_product_meta.php 
	  // are installed at the same time. Give the modules precedence.
	  global $PHP_SELF;
	  $basename = basename($PHP_SELF);
	  
	  if ( !(defined('MODULE_HEADER_TAGS_CATEGORY_TITLE_STATUS') && MODULE_HEADER_TAGS_CATEGORY_TITLE_STATUS == 'True' && $basename=='index.php' && isset($_GET['cPath']))
		   && !(defined('MODULE_HEADER_TAGS_MANUFACTURER_TITLE_STATUS') && MODULE_HEADER_TAGS_MANUFACTURER_TITLE_STATUS  == 'True' && $basename == 'index.php' && isset($_GET['manufacturers_id']))
		   && !(defined('MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS') && MODULE_HEADER_TAGS_PRODUCT_TITLE_STATUS == 'True' && $basename == 'product_info.php') ) {
		      if ( !empty($this->title) )  define('HEADING_TITLE', $this->title() );
	  }
	  
	  if ( !(defined('MODULE_HEADER_TAGS_CATEGORY_SEO_STATUS') && MODULE_HEADER_TAGS_CATEGORY_SEO_STATUS == 'True' && $basename=='index.php' && isset($_GET['cPath']))
		   && !(defined('MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS') && MODULE_HEADER_TAGS_MANUFACTURERS_SEO_STATUS  == 'True' && $basename == 'index.php' && isset($_GET['manufacturers_id']))
		   && !(defined('MODULE_HEADER_TAGS_PRODUCT_META_STATUS') && MODULE_HEADER_TAGS_PRODUCT_META_STATUS  == 'True' && $basename == 'product_info.php') ) {
              if ( !empty($this->meta_title) )       define('META_SEO_TITLE', $this->meta_title() );
              if ( !empty($this->meta_description) ) define('META_SEO_DESCRIPTION', $this->meta_description() );
              if ( !empty($this->meta_keywords) )    define('META_SEO_KEYWORDS', $this->meta_keywords() );
	  }

      if ( $this->isActive() ) {
        if ( !empty($this->body) ) define('TEXT_INFORMATION', $this->body() );
      } else {
        define('TEXT_INFORMATION', $this->gridWrap($this->getDef('text_page_disabled'), '12'));
      }
      if ( !empty($this->navbar_title) )     define('NAVBAR_TITLE', $this->navbar_title() );

      return true;
    }

    // ////////////////////////////////////////////////////////////////////////////////
    // Wrap in bootstrap DIV if user specified (assuming quality opcode caching)
    //
    public function gridWrap($body, $width) {

      $return = $body;
      if ( $width != '0' ) {
        ob_start();
        include(DIR_FS_CATALOG . 'includes/apps/surfcms/core/templates/content_wrap.php');
        $return = ob_get_clean();
      }

      return $return;
    }

    // ////////////////////////////////////////////////////////////////////////////////
    // data methods
    //
    public function isActive() {
        if (tep_not_null($this->status) && $this->status) {
            return true;
        }
        return false;
    }

    public function id($inp = null) {
        if (tep_not_null($inp))
            $this->id = $inp;
        if (tep_not_null($this->id))
            return $this->id;
        return false;
    }

    public function group($inp = null) {
        if (tep_not_null($inp))
            $this->group = $inp;
        if (tep_not_null($this->group))
            return $this->group;
        return false;
    }

    public function type($inp = null) {
        if (tep_not_null($inp))
            $this->type = $inp;
        if (tep_not_null($this->type))
            return $this->type;
        return false;
    }

    public function url($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->url = $inp;
        if (tep_not_null($this->url))
            return $this->url;
        return false;
    }

    public function menu_parent($inp = null) {
        if (tep_not_null($inp))
            $this->menu_parent = $inp;
        if (tep_not_null($this->menu_parent))
            return $this->menu_parent;
        return false;
    }

    public function menu_icon($inp = null) {
        if (tep_not_null($inp))
            $this->menu_icon = $inp;
        if (tep_not_null($this->menu_icon))
            return $this->menu_icon;
        return false;
    }

    public function width($inp = null) {
        if (tep_not_null($inp))
            $this->width = $inp;
        if (tep_not_null($this->width))
            return $this->width;
        return false;
    }

    public function sort_order($inp = null) {
        if (tep_not_null($inp))
            $this->sort_order = $inp;
        if (tep_not_null($this->sort_order))
            return $this->sort_order;
        return false;
    }

    public function status($inp = null) {
        if (tep_not_null($inp))
            $this->status = $inp;
        if (tep_not_null($this->status))
            return $this->status;
        return false;
    }

    public function show_in_nav($inp = null) {
        if (tep_not_null($inp))
            $this->show_in_nav = $inp;
        if (tep_not_null($this->show_in_nav))
            return $this->show_in_nav;
        return false;
    }

    public function show_in_box($inp = null) {
        if (tep_not_null($inp))
            $this->show_in_box = $inp;
        if (tep_not_null($this->show_in_box))
            return $this->show_in_box;
        return false;
    }

    public function menu_text($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->menu_text = $inp;
        if (tep_not_null($this->menu_text))
            return $this->menu_text;
        return false;
    }

    public function title($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->title = $inp;
        if (tep_not_null($this->title))
            return $this->title;
        return false;
    }

    public function body($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->body = $inp;
        if (tep_not_null($this->body))
            return $this->body;
        return false;
    }

    public function navbar_title($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->navbar_title = $inp;
        if (tep_not_null($this->navbar_title))
            return $this->navbar_title;
        return false;
    }

    public function meta_title($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->meta_title = $inp;
        if (tep_not_null($this->meta_title))
            return $this->meta_title;
        return false;
    }

    public function meta_description($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->meta_description = $inp;
        if (tep_not_null($this->meta_description))
            return $this->meta_description;
        return false;
    }

    public function meta_keywords($inp = null) {
        if (tep_not_null($inp) || $inp === '')
            $this->meta_keywords = $inp;
        if (tep_not_null($this->meta_keywords))
            return $this->meta_keywords;
        return false;
    }

    // ////////////////////////////////////////////////////////////////////////////////
    // Cleanup old content for a refresh or reload
    //
    private function cleanup() {

        $this->id = NULL;
        $this->group = NULL;
        $this->type = NULL;
        
        $this->url = NULL;
        $this->menu_parent = NULL;
        $this->menu_icon = NULL;
        $this->width = NULL;
        $this->sort_order = NULL;
        $this->show_in_nav = NULL;
        $this->show_in_box = NULL;
        $this->status = NULL;
        
        $this->menu_text = NULL;
        $this->title = NULL;
        $this->body = NULL;
        $this->navbar_title = NULL;
        $this->meta_title = NULL;
        $this->meta_description = NULL;
        $this->meta_keywords = NULL;
        
        $this->group_content = NULL;
        
        return true;
    }
    
    
  } // abstract class surfcms_page
?>