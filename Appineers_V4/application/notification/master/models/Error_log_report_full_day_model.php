<?php
            
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send error logs Model
 *
 * @category notification
 *
 * @package user
 *
 * @subpackage models
 *
 * @module
 *
 * @class Error_log_report_full_day_model.php
 *
 * @path application\notifications\user\models\Error_log_report_full_day_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */
 
class Error_log_report_full_day_model extends CI_Model
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_log');
    }

    public function get_fullday_error_logs()
    {
        try {
            $result_arr = array();
            $start_date = new DateTime("now");

            $curr_date =$start_date->format('Y-m-d');
            $record_limit = $this->config->item("record_limit") != '' ?  $this->config->item("record_limit") : 0;


            $this->db->from("api_accesslogs AS t");
           
            $this->db->select("t.iAccessLogId AS access_log_id");
            $this->db->select("t.vAPIURL AS api_url");
            $this->db->select("t.vAPIName AS api_name");
            $this->db->select("t.vRequestMethod AS request_method");
            $this->db->select("t.dAccessDate AS access_date");
            $this->db->select("t.vErrorType AS error_type");
            
            $this->db->where("t.vErrorType IN('Exception','Error')");
            $this->db->where("DATE(t.dAccessDate)=", $curr_date);
            
            $this->db->ORDER_BY("access_log_id DESC");

            if ($record_limit > 0) {
                $this->db->limit("$record_limit");
            }
            $result_obj = $this->db->get();
            #echo $this->db->last_query(); exit;
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr['Date'] = $curr_date;
            $result_arr['errorCount'] = $result_obj->num_rows();
            $result_arr['data'] = is_object($result_obj) ? $result_obj->result_array() : array();
            // if (!is_array($result_arr) || $result_obj->num_rows() == 0)
            // {
            //     throw new Exception('No log records found in crone.');
            // }
            $success = 1;
            $message = "";
        } catch (Exception $e) {
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

    public function delete_error_logs()
    {
        try {
            $dt2 = new DateTime("-".$this->config->item('LOG_DELETE_DURATION')." day");
            $dateLessthen = $dt2->format("Y-m-d");

            $this->db->where("Date(dAccessDate) <= '$dateLessthen' ");
            $this->db->delete('api_accesslogs');

            $affected_rows = $this->db->affected_rows();

            $result_arr["affected_rows"] = $affected_rows;
            $success = 1;
            $message = "";
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
