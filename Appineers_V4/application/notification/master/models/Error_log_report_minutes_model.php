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
 * @class Error_log_report_minutes_model.php
 *
 * @path application\notifications\user\models\Error_log_report_minutes_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */
 
class Error_log_report_minutes_model extends CI_Model
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_log');
    }

    
    public function get_today_error_logs()
    {
        try {
            $result_arr = array();
            $fromDate = date("Y-m-d H:i:s"); //echo "<br>";
            $confTime=  "-".$this->config->item("CRONE_EXCECUTION_TIME")." minutes";
            $toDate = date("Y-m-d H:i:s", strtotime($confTime));

            $this->db->from("api_accesslogs AS t");
            $this->db->select_max("t.eReportFlag");
            $result_ob = $this->db->get()->result_array();
            $this->db->flush_cache();

            if (!empty($result_ob) && $result_ob[0]['eReportFlag'] >0) {
                $flag_condition= " iAccessLogId > ".$result_ob[0]['eReportFlag'];
                $this->db->where($flag_condition);
            }

            $record_limit = $this->config->item("LOG_REPORT_ERROR_RECOD_LIMIT") != ''? $this->config->item("LOG_REPORT_ERROR_RECOD_LIMIT") : 0;
            $this->db->from("api_accesslogs");
           
            $this->db->select("iAccessLogId AS access_log_id");
            $this->db->select("vAPIURL AS api_url");
            $this->db->select("vAPIName AS api_name");
            $this->db->select("vRequestMethod AS request_method");
            $this->db->select("dAccessDate AS access_date");
            $this->db->select("vErrorType AS error_type");
            
            $where_err ="(vErrorType IN('Exception','Error','Warning','Notice') OR iErrorCode > 0)";
            $this->db->where($where_err);
            $this->db->where("dAccessDate <=", $fromDate);

            $this->db->order_by("iAccessLogId", 'DESC');

            if ($record_limit > 0) {
                $this->db->limit("$record_limit");
            }
            
            $result_obj = $this->db->get();
            #echo  $this->db->last_query();die;
            $db_error = $this->db->error();

            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr['fromDate'] = $fromDate;
            $result_arr['toDate'] = $toDate;
            $result_arr['data'] = is_object($result_obj) ? $result_obj->result_array() : array();
            
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




    public function update_error_logs($records = array())
    {
        try {
            $result_arr = array();
           
            $this->db->where("t.iAccessLogId", max($records));
            $this->db->set("t.eReportFlag", max($records));
            $this->db->update("api_accesslogs AS t");
           
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $affected_rows = $this->db->affected_rows();
            
            // if ($affected_rows < 1)
            // {
            //     throw new Exception('No log records updated.');
            // }

            $result_arr[0]["affected_rows"] = $affected_rows;
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
}
