<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Subscription Purchase Model
 *
 * @category webservice
 *
 * @package master
 *
 * @subpackage models
 *
 * @module Subscription Purchase
 *
 * @class Subscription_purchase_model.php
 *
 * @path application\webservice\master\models\Subscription_purchase_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 29.05.2020
 */

class Subscription_purchase_model extends CI_Model
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

    /**
    * This method is used to execute database queries for Subscription Purchase API.
    *
    * @param array $params_arr params_arr array to process query block.
    * @param array $where_arr where_arr are used to process where condition(s).
    *
    * @return array $return_arr returns response of query block.
    */
    public function subscription_purchase($params_arr = array(), $where_arr = array())
    {
        try {
            $subscription = array();
            $result_arr = array();

            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }

            if (isset($params_arr["original_transaction_id"])) {
                $this->db->set("vOrginalTransactionId", $params_arr["original_transaction_id"]);
            }

            if (isset($params_arr["auto_renew_product_id"])) {
                $this->db->set("auto_renew_product_id", $params_arr["auto_renew_product_id"]);
            }

            if (isset($params_arr["expiry_date"])) {
                $this->db->set("dLatestExpiryDate", $params_arr["expiry_date"]);
            }
            $this->db->set("eDeviceType", $params_arr["_ereceipttype"]);
            if (isset($params_arr["receipt_data_v1"])) {
                $this->db->set("lReceiptData", $params_arr["receipt_data_v1"]);
            }
            if (isset($params_arr["product_id"])) {
                $this->db->set("vProductId", $params_arr["product_id"]);
                $subscription[0]["product_id"] = $params_arr["product_id"];
            }
            if (isset($params_arr["autorenewal"])) {
                $this->db->set("eAutoRenewal", $params_arr["autorenewal"]);
            }
            // $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->insert("user_subscription");

            $insert_id = $this->db->insert_id();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";

            $subscription[0]["subscription_status"] = 1;
            $result_arr[0]["subscription"] = $subscription;
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
     * This method is used to execute database queries for Go Ad Free API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_subscription_purchase($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $subscription = array();

            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($where_arr["original_transaction_id"]) && $where_arr["original_transaction_id"] != "") {
                $this->db->where("vOrginalTransactionId =", $where_arr["original_transaction_id"]);
            }

            $this->db->set("eDeviceType", $params_arr["_ereceipttype"]);
            if (isset($params_arr["receipt_data_v1"])) {
                $this->db->set("lReceiptData", $params_arr["receipt_data_v1"]);
            }

            if (isset($params_arr["expiry_date"])) {
                $this->db->set("dLatestExpiryDate", $params_arr["expiry_date"]);
            }
            if (isset($params_arr["product_id"])) {
                $this->db->set("vProductId", $params_arr["product_id"]);

                $subscription[0]["product_id"]  = $params_arr["product_id"];
            }

            if (isset($params_arr["autorenewal"])) {
                $this->db->set("eAutoRenewal", $params_arr["autorenewal"]);
            }
            $res = $this->db->update("user_subscription");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            /* $result_param = "affected_rows";
             $result_arr[0][$result_param] = $affected_rows;*/

            $subscription[0]["subscription_status"]  = 1;
            $result_arr[0]["subscription"] = $subscription;
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
     * This method is used to execute database queries for Subscription Purchase API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function subscription_purchase_android($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $subscription = array();

            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }

            if (isset($params_arr["expiry_date_v1"])) {
                $this->db->set("dLatestExpiryDate", $params_arr["expiry_date_v1"]);
            }

            if (isset($params_arr["autoRenewing"])) {
                if ($params_arr["autoRenewing"] > 0) {
                    $autorenewal = '1';
                } else {
                    $autorenewal = '0';
                }
                
                $this->db->set("eAutoRenewal", $autorenewal);
            }

            if (isset($params_arr["subscription_id"])) {
                $this->db->set("vProductId", $params_arr["subscription_id"]);
                $subscription[0]["product_id"]  = $params_arr["subscription_id"];
            }
            if (isset($params_arr["purchase_token"])) {
                $this->db->set("lReceiptData", $params_arr["purchase_token"]);
            }
            $this->db->set("eDeviceType", $params_arr["_ereceipttype"]);
            //$this->db->set("eAutoRenewal",'1');
            $this->db->insert("user_subscription");
            $insert_id = $this->db->insert_id();
            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            /*$result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            */

            $subscription[0]["subscription_status"]  = 1;
            $result_arr[0]["subscription"] = $subscription;
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
     * This method is used to execute database queries for Subscription Purchase API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_subscription_purchase_android($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $subscription = array();

            if (isset($params_arr["user_id"])) {
                $this->db->where("iUserId", $params_arr["user_id"]);
            }

            if (isset($params_arr["subscription_id"])) {
                $this->db->where("vProductId", $params_arr["subscription_id"]);

                $subscription[0]["product_id"]  = $params_arr["subscription_id"];
            }

            if (isset($params_arr["purchase_token"])) {
                $this->db->where("lReceiptData", $params_arr["purchase_token"]);
            }

            if (isset($params_arr["expiry_date_v1"])) {
                $this->db->set("dLatestExpiryDate", $params_arr["expiry_date_v1"]);
            }

            if (isset($params_arr["autoRenewing"])) {
                if ($params_arr["autoRenewing"] > 0) {
                    $autorenewal = '1';
                } else {
                    $autorenewal = '0';
                }
                
                $this->db->set("eAutoRenewal", $autorenewal);
            }

            $res = $this->db->update("user_subscription");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            /* $result_param = "affected_rows";
             $result_arr[0][$result_param] = $affected_rows;*/

            $subscription[0]["subscription_status"]  = 1;
            $result_arr[0]["subscription"] = $subscription;
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
     * This method is used to execute database queries for Subscription Purchase API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_subscription_details($user_id, $original_transaction_id)
    {
        try {
            $result_arr = array();
            $this->db->select('vProductId as product_id,"1" as subscription_status');
            $this->db->from('user_subscription');

            if (isset($user_id) && $user_id != "") {
                $this->db->where("iUserId =", $user_id);
                $this->db->where("vOrginalTransactionId =", $original_transaction_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();

            $subscription = is_object($result_obj) ? $result_obj->result_array() : array();
            $result_arr[0]["subscription"] = $subscription;

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            /*
                "subscription": [
                   {
                       "product_id": "com.appineers.WidsConnect.monthly",
                       "subscription_status": "1"
                   }
               ]

               [
                   [
                       {
                           "product_id": "com.appineers.WidsConnect.monthly",
                           "subscription_status": "1"
                       }
                   ]
               ]*/
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
     * This method is used to execute database queries for Subscription Purchase API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_android_subscription_details($user_id, $purchase_token)
    {
        try {
            $result_arr = array();

            $this->db->select('vProductId as product_id,"1" as subscription_status,lReceiptData as purchase_token');
            
            $this->db->from('user_subscription');

            if (isset($user_id) && $user_id != "") {
                $this->db->where("iUserId =", $user_id);
                $this->db->where("lReceiptData =", $purchase_token);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $subscription = is_object($result_obj) ? $result_obj->result_array() : array();
            $result_arr[0]["subscription"] = $subscription;
        
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
}
