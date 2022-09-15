<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Users Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Users
 *
 * @class Users_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Users_model.php
 *
 */

class Post_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->load->library('lib_log');
        $this->default_lang = $this->general->getLangRequestValue();
    }
    
}