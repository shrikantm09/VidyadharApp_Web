<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Message Model
 *
 * @category webservice
 *
 * @package CHAT
 *
 * @subpackage models
 *
 * @module Send Message
 *
 * @class Send_message_model.php
 *
 * @path application\webservice\CHAT\models\Send_message_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Send_message_model extends CI_Model
{
    
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_log');
    }
}
