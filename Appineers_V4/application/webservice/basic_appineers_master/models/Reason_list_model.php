<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Reason List Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Reason List
 *
 * @class Reason_list_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Reason_list_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Reason_list_model extends CI_Model
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_log');
    }
    /**
     * This method is used to execute database queries for States List API.
     *
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * 
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function reasons_list($WhereArr = array())
    {
        // print_r("Text");exit;
        try {
            $result_arr = array();

            $this->db->from("reasons");
            $this->db->select('iReasonId AS reason_id');
            $this->db->select('vReason AS reason_name');
            $this->db->select('vEntityType AS reason_type');
            $this->db->where("eStatus", 'Active');

            if (false == empty($WhereArr['reason_type'])) {
                $this->db->where("vEntityType = '" . $WhereArr['reason_type'] . "' ");
            }

            $this->db->order_by("vReason", "asc");
        
            $result_obj = $this->db->get();
           
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
            $message = "";
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
