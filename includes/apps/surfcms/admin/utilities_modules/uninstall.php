<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  // /////////////////////////////////////////////////////
  // SurfCMS Uninstall Database
  //
  final class surfcms_uninstall {
      
    private static $instance = null;
    private $surfcms;


    // /////////////////////////////////////////////////////
    // construct / destruct
    //
    protected function __construct() {
      global $surfcms;
	  
	  $this->surfcms = $surfcms;
      $this->surfcms->loadLanguageFile('admin/utilities_modules/'.basename(__FILE__));
    } 
        
    function __destruct() {} 


    // /////////////////////////////////////////////////////
    // Privatize Magic Methods
    //
    // no cloning
    private function __clone() {} 
    // no serialize
    private function __sleep() {}
    private function __wakeup() {} 


    // /////////////////////////////////////////////////////
    // Static singleton instance
    //
    public static function i() {
     if ( !self::$instance instanceof surfcms_uninstall ) {
       self::$instance = new self;
     }
     return self::$instance;
    }


    // /////////////////////////////////////////////////////
    // Module action: Uninstall Database
    //
    public function commit() {
      global $messageStack;  
	  
	  // invoke built in database uninstall
	  // class is loaded as a include with surfcms class
	  surfcms_app_setup::i()->uninstallDatabase();
	  
	  if (USE_CACHE == 'true') {
	    tep_reset_cache_block('surfcms_content');
	  }
	  
	  $messageStack->add_session($this->surfcms->getDef('text_utilities_uninstall_success'), 'success');

	  tep_redirect(tep_href_link('index.php'));

    }
    
    // /////////////////////////////////////////////////////
    // Return content title
    //
    public function content_title() {

      return $this->surfcms->getDef('text_utilities_uninstall_title');
    }
    
    // /////////////////////////////////////////////////////
    // Display default form or content
    //
    public function content() {

      ob_start();
      include(dirname(__FILE__).'/templates/'.basename(__FILE__));
      $data = ob_get_clean();

      return $data;
    }


    
  } // class surfcms_uninstall
?>