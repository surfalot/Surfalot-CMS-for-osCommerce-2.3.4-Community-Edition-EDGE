<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  
  Some surfcms_app methods shamelessly gancked from Harold Ponce de Leon's PayPal app
  Copyright (c) 2014 osCommerce

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

  /* osc_app interface */
  require_once DIR_FS_CATALOG . 'includes/apps/surfcms/core/osc_app.interface.php';

  // /////////////////////////////////////////////////////
  // App Abstraction
  //
  abstract class surfcms_app implements osc_app {
    
    protected $_code;
    protected $_title;
    protected $_version;
    protected $_version_name;
    protected $_definitions = array();

    protected $language;
    protected $languages_id;

    // /////////////////////////////////////////////////////
	// Base Methods
	//
    public function getAppCode() {
      return $this->_code;
    }

    public function getAppTitle() {
      return $this->_title;
    }
	
    public function getTitleAndVersion() {
      return $this->_version_name;
    }
	
    public function getVersion() {
		
      if ( isset($this->_version) ) {
		return $this->_version;
	  } else {
          
        // get App version
        $this->_version_name = tep_db_prepare_input(trim(file_get_contents(DIR_FS_CATALOG . 'includes/apps/surfcms/version.txt')));
		
		$match = '';
		preg_match( '/' . $this->_title . ' v([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})/', $this->_version_name, $match);
		$version = $match[1];
		
        if ( strlen($version) > 4 ) {
          $this->_version = $version;
		  return $this->_version;
        } else {
          trigger_error($this->getAppTitle() . $this->getDef('app_version_error'));
        }
            
      }
      return false;
    }

    public function hasAlert() {
      return tep_session_is_registered('surfcms_Alerts');
    }

    public function addAlert($message, $type) {
      global $surfcms_Alerts;

      if ( in_array($type, array('error', 'warning', 'success')) ) {
        if ( !tep_session_is_registered('surfcms_Alerts') ) {
          $surfcms_Alerts = array();
          tep_session_register('surfcms_Alerts');
        }

        $surfcms_Alerts[$type][] = $message;
      }
      return true;
    }

    public function getAlerts() {
      global $surfcms_Alerts;

      $output = '';

      if ( tep_session_is_registered('surfcms_Alerts') && tep_not_null($surfcms_Alerts) ) {
        $result = array();

        foreach ( $surfcms_Alerts as $type => $messages ) {
          if ( in_array($type, array('error', 'warning', 'success')) ) {
            $m = '<ul class="surfcms-alerts-' . $type . '">';

            foreach ( $messages as $message ) {
              $m .= '<li>' . tep_output_string_protected($message) . '</li>';
            }

            $m .= '</ul>';

            $result[] = $m;
          }
        }

        if ( tep_not_null($result) ) {
          $output .= '<div class="surfcms-alerts">' . implode("\n", $result) . '</div>';
        }
      }

      tep_session_unregister('surfcms_Alerts');

      return $output;
    }

    public function loadLanguageFile($filename, $lang = null) {
      $lang = isset($lang) ? basename($lang) : basename($this->language);

      if ( $lang != 'english' ) {
        $this->loadLanguageFile($filename, 'english');
      }

      $pathname = DIR_FS_CATALOG . 'includes/apps/surfcms/languages/' . $lang . '/' . $filename;

      if ( file_exists($pathname) ) {
        $contents = file($pathname);

        $ini_array = array();

        foreach ( $contents as $line ) {
          $line = trim($line);

          if ( tep_not_null($line) && (substr($line, 0, 1) != '#') ) {
            $delimiter = strpos($line, '=');

            if ( ($delimiter !== false) && (preg_match('/^[A-Za-z0-9_-]/', substr($line, 0, $delimiter)) === 1) && (substr_count(substr($line, 0, $delimiter), ' ') == 1) ) {
              $key = trim(substr($line, 0, $delimiter));
              $value = trim(substr($line, $delimiter + 1));

              $ini_array[$key] = $value;
            } elseif ( isset($key) ) {
              $ini_array[$key] .= "\n" . $line;
            }
          }
        }

        unset($contents);

        $this->_definitions = array_merge($this->_definitions, $ini_array);

        unset($ini_array);
      }
    }
    
    public function getDef($key, $values = null) {
      $def = isset($this->_definitions[$key]) ? $this->_definitions[$key] : $key;

      if ( is_array($values) ) {
        $keys = array_keys($values);

        foreach ( $keys as &$k ) {
          $k = ':' . $k;
        }

        $def = str_replace($keys, array_values($values), $def);
      }

      return $def;
    }


    protected function getLanguage() {
      
      if ( !isset($this->language) || empty($this->language)) {
        $this->setLanguage();
      }
	  
	  return $this->language;
    }

    protected function getLanguageID() {
      
      if ( !isset($this->languages_id) || empty($this->languages_id)) {
        $this->setLanguage();
      }
	  
	  return $this->languages_id;
    }

    protected function setLanguage() {
      global $language, $languages_id;

      // set the language
      if ( !isset($language) && !tep_not_null($language)) {

        include_once(DIR_FS_CATALOG . 'includes/apps/surfcms/core/surfcms_language.class.php');
        $lng = new surfcms_language();
    
        if (isset($_GET['language']) && tep_not_null($_GET['language'])) {
          $lng->set_language($_GET['language']);
        } else {
          $lng->get_browser_language();
        }
    
        $this->language = $lng->language['directory'];
        $this->languages_id = $lng->language['id'];
		
      } else {
		  
        $this->language = $language;
        $this->languages_id = $languages_id;
		
      }
	  
      if ( !isset($this->language) || empty($this->language)) {
        return false;
      }

	  return true;
    }
	
	protected function getLanguageFromID($id) {
	  
      $language_query = tep_db_query("select languages_id, directory from languages where languages_id = '" . (int)$id . "'");
      if (tep_db_num_rows($language_query)) {
        $language = tep_db_fetch_array($language_query);
        return $language['directory'];
      } else {
        return false;
      }
		
	}


  } // abstract class surfcms_app
?>