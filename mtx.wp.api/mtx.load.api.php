<?php
#########################################################################
# MTX WP API VERSION 2.0                                                #
#                                                                       #
#########################################################################

/**
 * mtx_load_api
 * 
 * @package mtx.wp.api  
 * @author Maurizio Tarchini
 * @copyright 2017
 * @version 2.0
 * @access public
 */
class mtx_load_api
{
    public $ajax;
    public $custombox;
    public $db;
    public $load;
    
        public function __construct()
        {
            $this->_load_api(); 
            $this->ajax = new mtx_ajax_api_support();
            $this->custombox = new metabox_support();
            $this->db = new db_tools_support();
            $this->load = new include_api(); 
        }
        
        private function _load_api()
        {
            require_once 'cleaner.api.inc.php';
            require_once 'ajax.api.inc.php';
            require_once 'custombox.api.inc.php';
            require_once 'db.tools.inc.php';
            require_once 'include.api.inc.php';
        }
}

$mtx = new mtx_load_api();
?>