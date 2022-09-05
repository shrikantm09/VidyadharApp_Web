<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Sms Template Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Sms Template
 *
 * @class Sms_template_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Sms_template_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Sms_template_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
        $this->load->library('lib_log');
    }

    /**
     * Used to execute database queries for Get Template Message API.
     * 
     * @param string $template_code template_code is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_template($template_code = '')
    {
        try {
            $result_arr = array();

            $this->db->from("sms_template AS st");

            $this->db->select("st.tSmsText AS sms_text");
            if (isset($template_code) && $template_code != "") {
                $this->db->where("st.vTemplateCode =", $template_code);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['template_code'] = $template_code;
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
