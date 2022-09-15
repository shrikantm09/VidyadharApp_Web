<?php  

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Abusive Reports User Model
 * 
 * @category webservice
 *            
 * @package misc
 *
 * @subpackage models
 *
 * @module Abusive Reports
 * 
 * @class Report_abusive_business_model.php
 * 
 * @path application\webservice\misc\models\Report_abusive_user_model.php
 * 
 * @version 4.4
 *
 * @author CIT Dev Team
 * 
 * @since 03.05.2019
 */
 
class Report_abusive_user_model extends CI_Model
{
    public $default_lang = 'EN';
    
    /**
     * __construct method is used initialize the objects
     */
    public function __construct() {
        parent::__construct();
        $this->load->helper('listing');
        $this->load->library('lib_log');
        $this->default_lang = $this->general->getLangRequestValue();
    }
    
    /**
     * insert_report method is used to execute database queries for Report Abusive User API.
     
     * @param array $params_arr params_arr array to process query block.
     
     * @return array $return_arr returns response of query block.
     */
    public function insert_report($params_arr = array())
    {
        try {
            $result_arr = array();
                        
            if(!is_array($params_arr) || count($params_arr) == 0){
                throw new Exception("Insert data not found.");
            }

            
            if(isset($params_arr["user_id"])){
                $this->db->set("iReportedBy", $params_arr["user_id"]);
            }
            if(isset($params_arr["message"])){
                $this->db->set("vMessage", $params_arr["message"]);
            }
            if(isset($params_arr["report_on"])){
                $this->db->set("iReportedOn", $params_arr["report_on"]);
            }
            if(isset($params_arr["reason_id"])){
                $this->db->set("iReasonId", $params_arr["reason_id"]);
            }
            if(isset($params_arr["reason_description"])){
                $this->db->set("tReasonDescription", $params_arr["reason_description"]);
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->insert("abusive_reports");
            $insert_id = $this->db->insert_id();

            //echo $insert_id. "---"; 
            //echo $this->db->last_query();exit();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if(!$insert_id){
                 throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
            
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
			$this->general->apiLogger($params_arr, $e);
        }
        
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;


        return $return_arr;
    }
    
    
}