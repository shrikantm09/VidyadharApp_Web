<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Model
 *
 * @category webservice
 *
 * @package event
 *
 * @subpackage models
 *
 * @module Notification
 *
 * @class Notification_model.php
 *
 * @path application\webservice\event\models\Notification_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Notification_model extends CI_Model
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
     * Used to execute database queries for Send Message API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_notification($params_arr = array())
    {
        try {
            $result_arr = array();
            
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("sender_id", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"])) {
                $this->db->set("receiver_id", $params_arr["receiver_id"]);
            }
            if (isset($params_arr["entity_type"])) {
                $this->db->set("entity_type", $params_arr["entity_type"]);
            }
            if (isset($params_arr["entity_id"])) {
                $this->db->set("entity_id", $params_arr["entity_id"]);
            }
          
            if (isset($params_arr["notification_message"])) {
                $this->db->set("notification_message", $params_arr["notification_message"]);
            }
            if (isset($params_arr["redirection_type"])) {
                $this->db->set("redirection_type", $params_arr["redirection_type"]);
            }
            $this->db->set("notification_type", $params_arr["_enotificationtype"]);
            $this->db->set($this->db->protect("created_at"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set($this->db->protect("updated_at"), $params_arr["_dtupdatedat"], FALSE);

            $this->db->insert("notifications");

            $insert_id = $this->db->insert_id();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch(Exception $e) {
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

    /**
     * Used to execute database queries for _notification_read Update API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_notification_read_data($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["notification_id"]) && $params_arr["notification_id"] != "") {
                $this->db->where("notification_id=", $params_arr["notification_id"]);
            }
            $this->db->stop_cache();

            $this->db->set("status", $params_arr["status"]);

            $res = $this->db->update("notifications");
            $affected_rows = $this->db->affected_rows();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
    
    /**
     * Used to execute database queries for notification list API.
     * 
     * @param string $params_arr params_arr is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_all_notification($params_arr = array(), &$settings_params)
    {
        try {
            
            $date = new DateTime("now");
            $curr_date = $date->format('Y-m-d ');
            $result_arr = array();

            $this->db->start_cache();

            $this->db->from("notifications AS n");

            $this->db->where("n.receiver_id=", $params_arr["user_id"]);

            $this->db->stop_cache();
            $total_records = $this->db->count_all_results();
            $this->db->join("users AS u", "u.iUserId = n.sender_id", "LEFT");
            $this->db->select("n.notification_id");
            $this->db->select("n.entity_id");
            $this->db->select("n.entity_type");
            $this->db->select("n.notification_message");
            $this->db->select("n.status");
            $this->db->select("n.receiver_id");
            $this->db->select("n.sender_id");
            $this->db->select("u.vFirstName as sender_first_name");
            $this->db->select("u.vLastName as sender_last_name");
            $this->db->select("u.vProfileImage as sender_profile_image");
            $this->db->select("n.redirection_type");
            $this->db->select("n.created_at");

            $settings_params['count'] = $total_records;
            
            if (isset($params_arr["per_page_record"]) && isset($params_arr["page_index"])) {
                $record_limit = intval("".$params_arr["per_page_record"]."");
                $current_page = intval($params_arr["page_index"]) > 0 ? intval($params_arr["page_index"]) : 1;
                $total_pages = getTotalPages($total_records, $record_limit);
                $start_index = getStartIndex($total_records, $current_page, $record_limit);
                $settings_params['per_page'] = $record_limit;
                $settings_params['curr_page'] = $current_page;
                $settings_params['prev_page'] = ($current_page > 1) ? 1 : 0;
                $settings_params['next_page'] = ($current_page+1 > $total_pages) ? 0 : 1;
            }

            $this->db->order_by("n.notification_id", "DESC");

            if (isset($record_limit) && isset($start_index)){
                $this->db->limit($record_limit, $start_index);
            }

            $result_obj = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        
        $this->db->_reset_all();
        //echo $this->db->last_query();
        
        $return_arr["count"] = $total_records;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * Used to execute database queries for notification_count API.
     * 
     * @param string $user_id user_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_count($user_id = '')
    {
        try {
            $result_arr = array();
            $date = new DateTime("now");
            $curr_date = $date->format('Y-m-d ');
            $this->db->from("notifications AS n");

            $this->db->select("(count(n.notification_id)) AS notification_count", FALSE);
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("n.receiver_id =", $user_id);
            }
            $this->db->where("n.status=", 'Unread');
            $this->db->where("DATE(n.created_at) =", $curr_date);
            $this->db->limit(1);

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
        } catch(Exception $e) {
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

    /**
     * Used to execute database queries for notification_count API.
     * 
     * @param string $user_id user_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function delete_notification($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["notification_id"]) && $params_arr["notification_id"] != "") {
                $this->db->where_in("notification_id", $params_arr["notification_id"]);
            } else {
                $this->db->where("sender_id=", $params_arr["user_id"]);
            }
            $this->db->stop_cache();

            $res = $this->db->delete("notifications");

            $affected_rows = $this->db->affected_rows();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }


            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch(Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
