<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  class cm_header_store_message {
    var $code;
    var $group;
    var $title;
    var $description;
    var $sort_order;
    var $enabled = false;
    var $languages_array = array ();

    function cm_header_store_message() {
      $this->code = get_class($this);
      $this->group = basename(dirname(__FILE__));
      $this->title = MODULE_CONTENT_STORE_MESSAGE_TITLE;
      $this->description = MODULE_CONTENT_STORE_MESSAGE_DESCRIPTION;

      if ( defined('MODULE_CONTENT_STORE_MESSAGE_STATUS') ) {
        $this->sort_order = MODULE_CONTENT_STORE_MESSAGE_SORT_ORDER;
        $this->enabled = (MODULE_CONTENT_STORE_MESSAGE_STATUS == 'True');
      }
    }

    function execute() {
      global $language, $oscTemplate;

			$store_msg = constant("MODULE_CONTENT_STORE_MESSAGE_" . strtoupper($language));

			ob_start();
			include(DIR_WS_MODULES . 'content/' . $this->group . '/templates/storemessage.php');
			$template = ob_get_clean();

			$oscTemplate->addContent($template, $this->group);

    }

    function isEnabled() {
      return $this->enabled;
    }

    function check() {
      return defined('MODULE_CONTENT_STORE_MESSAGE_STATUS');
    }

    function install() {
      include_once( DIR_WS_CLASSES . 'language.php' );
      $language_class = new language;
      $languages = $language_class->catalog_languages;

      foreach( $languages as $this_language ) {
        $this->languages_array[$this_language['id']] = $this_language['directory'];
      }

      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Store Message Module', 'MODULE_CONTENT_STORE_MESSAGE_STATUS', 'True', 'Do you want to enable the Store Message module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_CONTENT_STORE_MESSAGE_SORT_ORDER', '1', 'Sort order of Display.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ( 'Message type', 'MODULE_CONTENT_STORE_MESSAGE_TYPE', 'warning', 'Choose the type of message', '6', '2', 'tep_cfg_select_option(array(\'error\', \'warning\', \'info\'), ', now())");
      foreach ($this->languages_array as $language_id => $language_name) {
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ( '" . ucwords($language_name) . " Text', 'MODULE_CONTENT_STORE_MESSAGE_" . strtoupper($language_name) . "', 'This is a development store. Orders will not be fulfilled but you might still be charged.', 'Enter the text that you want to show in the message in " . $language_name . "', '6', '2', '', now())");
      }
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      include_once( DIR_WS_CLASSES . 'language.php' );
      $language_class = new language;
      $languages = $language_class->catalog_languages;

      foreach( $languages as $this_language ) {
        $this->languages_array[$this_language['id']] = $this_language['directory'];
      }
			
      $return_keys = array('MODULE_CONTENT_STORE_MESSAGE_STATUS', 'MODULE_CONTENT_STORE_MESSAGE_SORT_ORDER', 'MODULE_CONTENT_STORE_MESSAGE_TYPE');
			foreach ($this->languages_array as $language_name) {
        $return_keys[] = 'MODULE_CONTENT_STORE_MESSAGE_' . strtoupper($language_name);
      }

			return $return_keys;
    }
  }
?>
