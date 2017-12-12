<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  
  Some osc_app interface methods shamelessly gancked from Harold Ponce de Leon's PayPal app
  Copyright (c) 2014 osCommerce

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  // /////////////////////////////////////////////////////
  // App Interface
  //
  interface osc_app {
    
    // /////////////////////////
	// Methods
    // /////////////////////////
	
    public function getAppCode();
    public function getAppTitle();
	public function getVersion();
    public function hasAlert();
    public function addAlert($message, $type);
    public function getAlerts();
    public function loadLanguageFile($filename, $lang = null);
    public function getDef($key, $values = null);

  } // interface osc_app
?>