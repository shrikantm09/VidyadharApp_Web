<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Messages Model
 *
 * @category webservice
 *
 * @package comments
 *
 * @subpackage models
 *
 * @module Messages
 *
 * @class Messages_model.php
 *
 * @path application\webservice\comments\models\Messages_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */

class Messages_model extends CI_Model
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
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function check_chat_intiated_or_not($user_id = '', $receiver_id = '', $firebase_id = '')
    {
        try {
            $result_arr = array();

            $this->db->from("message AS m");
            $this->db->select("m.message_id AS m_message_id");
            $this->db->select("m.firebase_id AS m_firebase_id");
            $this->db->where("m.firebase_id =", $firebase_id);
            $this->db->or_where("(m.sender_id IS NOT NULL AND m.sender_id <> '')", false, false);
            $this->db->where("(m.sender_id = ".$user_id." AND m.receiver_id = ".$receiver_id.") OR (m.sender_id = ".$receiver_id." AND m.receiver_id = ".$user_id.")", false, false);

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

    /**
     * Used to execute database queries for Send Message API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_message($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            if (isset($where_arr["m_firebase_id"]) && $where_arr["m_firebase_id"] != "") {
                $this->db->where("firebase_id =", $where_arr["m_firebase_id"]);
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("sender_id", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"])) {
                $this->db->set("receiver_id", $params_arr["receiver_id"]);
            }
            if (isset($params_arr["message"])) {
                $this->db->set("message", $params_arr["message"]);
            }
            if (isset($params_arr["reserved_item_id"])) {
                $this->db->set("reserved_item_id", $params_arr["reserved_item_id"]);
            }
            if (isset($params_arr["upload_doc"]) && !empty($params_arr["upload_doc"])) {
                $this->db->set("upload_doc", $params_arr["upload_doc"]);
            }
            
            $this->db->set($this->db->protect("created_at"), $params_arr["_dtmodifieddate"], false);
            $this->db->set($this->db->protect("updated_at"), $params_arr["_dtmodifieddate"], false);
            $res = $this->db->update("message");

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            
            $affected_rows = $this->db->affected_rows();
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * Used to execute database queries for Send Message API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function add_message($params_arr = array())
    {
        try {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["firebase_id"]) && $params_arr["firebase_id"] != "") {
                $this->db->set("firebase_id", $params_arr["firebase_id"]);
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("sender_id", $params_arr["user_id"]);
            }
            if (isset($params_arr["receiver_id"])) {
                $this->db->set("receiver_id", $params_arr["receiver_id"]);
            }
            if (isset($params_arr["reserved_item_id"])) {
                $this->db->set("reserved_item_id", $params_arr["reserved_item_id"]);
            }
            if (isset($params_arr["message"])) {
                $this->db->set("message", $params_arr["message"]);
            }
            if (isset($params_arr["upload_doc"]) && !empty($params_arr["upload_doc"])) {
                $this->db->set("upload_doc", $params_arr["upload_doc"]);
            }

            $this->db->set($this->db->protect("created_at"), $params_arr["_dtaddeddate"], false);
            $this->db->set($this->db->protect("updated_at"), $params_arr["_dtmodifieddate"], false);
            
            $this->db->insert("message");
            $insert_id = $this->db->insert_id();
            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "m_message_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
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

    /**
     * Method is used to execute database queries for Send Message API.
     * 
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $receiver_id = '')
    {
        try {
            $result_arr = array();

            $this->db->from("message AS m");
            $this->db->join("users AS s", "m.sender_id = s.iUserId", "left");
            $this->db->join("users AS r", "m.receiver_id = r.iUserId", "left");


            $this->db->select("s.iUserId AS s_users_id");
            $this->db->select("r.iUserId AS r_users_id");
            $this->db->select("r.vDeviceToken AS r_device_token");
            $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
            $this->db->select("s.vProfileImage AS sender_profile_image");
            $this->db->where("(m.sender_id = ".$user_id." AND m.receiver_id = ".$receiver_id.")", false, false);
            $this->db->where("r.eStatus", "Active");

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

    /**
     * Used to execute database queries for Get Message List API.
     * 
     * @param string $where_clause where_clause is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_message($where_clause = '', $status='')
    {
        
        try {
            $result_arr = array();

            $this->db->from("message AS m");
            $this->db->join("users AS u", "m.sender_id = u.iUserId", "left");
            $this->db->join("users AS u1", "m.receiver_id = u1.iUserId", "left");
          
            $this->db->select("m.iMessageId AS message_id");
            $this->db->select("m.dtAddedDate AS message_date");
            $this->db->select("m.sender_id AS sender_id");
            $this->db->select("m.receiver_id AS receiver_id");
            $this->db->select("m.vMessage AS message");
            $this->db->select("m.tMessageUpload AS message_upload");
            $this->db->select("concat(u.vFirstName,\" \",u.vLastName) AS sender_name");
            $this->db->select("concat(u1.vFirstName,\" \",u1.vLastName) AS receiver_name");
            $this->db->select("m.dtModifiedDate AS updated_at");
            $this->db->select("(".$this->db->escape("").") AS sender_image", false);
            $this->db->select("(".$this->db->escape("").") AS receiver_image", false);
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_logged_user", false);
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_receiver_user", false);
           
            $this->db->where("".$where_clause." ", false, false);

            if (isset($status) && $status != "") {
                $this->db->where("m.eStatus", $status);
            }
            $this->db->order_by("m.dtAddedDate", "desc");

            // if(false == empty($receiver_id)){
            //  $this->db->where("m.sender_id", $user_id)->where("m.receiver_id", $receiver_id)->or_where("m.sender_id", $receiver_id)->where("m.receiver_id", $user_id);
            //   $this->db->order_by("m.dtModifiedDate", "desc");
           
            //  }else{
            //  /*$this->db->where("m.receiver_id", $user_id);
            //  $this->db->order_by("m.dtAddedDate", "desc");*/
            //  $strWhere ="m.iMessageId in (select max(m.iMessageId ) as max_id
            //          from message m
            //           group by least(m.sender_id , m.receiver_id ), greatest(m.sender_id , m.receiver_id )
            //          )
            //           AND (m.sender_id = '".$user_id."' OR m.receiver_id = '".$user_id."')";
            //          $this->db->where($strWhere);
            //         // $this->db->where("m.sender_id", $user_id)->or_where("m.receiver_id", $user_id);
            //  }
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
