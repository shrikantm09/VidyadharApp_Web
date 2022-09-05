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

class Users_model extends CI_Model
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
     * This method is used to execute database queries for Logout API.
     *
     * @param array $params_arr params_arr array to process query block.
     * 
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function logout($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            $this->db->where_in("eStatus", array('Active'));

            $this->db->set($this->db->protect("vAccessToken"), $params_arr["_vaccesstoken"], FALSE);
            $res = $this->db->update("users");
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
            $message = "";
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }

        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }



    public function user_images($data_arr)
    {
        try {
            $result_arr = array();
            if (false == empty($data_arr['u_user_id'])) {
                $this->db->where("iUserId  =", $data_arr['u_user_id']);
            }
            $this->db->from("user_images");        
            $this->db->select("vImage AS user_image");
            $this->db->select("iImageId AS image_id");
            $this->db->select("vLocalImageId AS local_image_id");
            $result_obj = $this->db->get();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

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
            $arrResult['db_query'] = $this->db->last_query();
            $this->general->apiLogger($arrResult, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();

        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        //echo __LINE__;exit;
      //  echo '<pre>';print_r($result_arr);exit;
        return $return_arr;
    }

    /**
     * update_device_token method is used to execute database queries for Update Device Token API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_device_token($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * update_notification method is used to execute database queries for Update Push Notification Settings API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_notification($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->where_in("eStatus", array('Active'));
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["notification"])) {
                $this->db->set("ePushNotify", $params_arr["notification"]);
            }
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * update_new_password method is used to execute database queries for Change Password API.
     *
     * @param array $params_arr params_arr array to process query block.
     
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_new_password($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            $this->db->where("eStatus", "Active");
            if (isset($params_arr["new_password"])) {
                $this->db->set("vPassword", $params_arr["new_password"]);
            }
            $res = $this->db->update("users");


            //echo $this->db->last_query();
           // exit;

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            $affected_rows = $this->db->affected_rows();

            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $db_error = $this->db->error();
            if (!$db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * check_unique_mobile_number method is used to execute database queries for Change Mobile Number API.
     *
     * @param string $new_mobile_number new_mobile_number is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function check_unique_mobile_number($new_mobile_number = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.iUserId AS u_user_id");
            if (isset($new_mobile_number) && $new_mobile_number != "") {
                $this->db->where("u.vMobileNo =", $new_mobile_number);
            }

            $this->db->limit(1);
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
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

    /**
     * update_mobile_number method is used to execute database queries for Change Mobile Number API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_mobile_number($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["new_mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["new_mobile_number"]);
            }
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * get_user method is used to execute database queries for User Email Confirmation API.
     *
     * @param string $email email is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user($email = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->select("u.vFirstName AS u_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.iUserId AS u_user_id");
            if (isset($email) && $email != "") {
                $this->db->where("u.vEmail =", $email);
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

    /**
     * update_confirmation method is used to execute database queries for User Email Confirmation API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_confirmation($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }
            if (isset($where_arr["confirmation_code"]) && $where_arr["confirmation_code"] != "") {
                $this->db->where("vEmailVerificationCode =", $where_arr["confirmation_code"]);
            }

            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * Used to execute database queries for create User using email.
     *
     * @param array $params_arr params_arr array to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function create_user($params_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["first_name"])) {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (isset($params_arr["last_name"])) {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (isset($params_arr["user_name"])) {
                $this->db->set("vUserName", $params_arr["user_name"]);
            }
            if (isset($params_arr["email"])) {
                $this->db->set("vEmail", $params_arr["email"]);
            }
            if (isset($params_arr["mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            if (isset($params_arr["user_profile"]) && !empty($params_arr["user_profile"])) {
                $this->db->set("vProfileImage", $params_arr["user_profile"]);
            }
            if (isset($params_arr["dob"])) {
                $this->db->set("dDob", $params_arr["dob"]);
            }
            if (isset($params_arr["password"])) {
                $this->db->set("vPassword", $params_arr["password"]);
            }
            if (isset($params_arr["address"])) {
                $this->db->set("tAddress", $params_arr["address"]);
            }
            if (isset($params_arr["city"])) {
                $this->db->set("vCity", $params_arr["city"]);
            }
            if (isset($params_arr["latitude"])) {
                $this->db->set("dLatitude", $params_arr["latitude"]);
            }
            if (isset($params_arr["longitude"])) {
                $this->db->set("dLongitude", $params_arr["longitude"]);
            }
            
            if (isset($params_arr["state_name"])) {
                $this->db->set("vStateName", $params_arr["state_name"]);
            }
            if (isset($params_arr["zipcode"])) {
                $this->db->set("vZipCode", $params_arr["zipcode"]);
            }
            $this->db->set("eStatus", $params_arr["status"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);
            if (isset($params_arr["email_confirmation_code"])) {
                $this->db->set("vEmailVerificationCode", $params_arr["email_confirmation_code"]);
            }
            $this->db->set("vTermsConditionsVersion", $params_arr["_vtermsconditionsversion"]);
            $this->db->set("vPrivacyPolicyVersion", $params_arr["_vprivacypolicyversion"]);
            $this->db->insert("users");
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
     * Get user details for Sign Up Email API.
     *
     * @param string $insert_id used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();
            $params_arr = array();
            $params_arr['db_query'] = $insert_id;

            $this->db->from("users AS u");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("'' AS u_state_id");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS email_user_name", FALSE);
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.vTermsConditionsVersion AS terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("u.iUserId =", $insert_id);
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
     * Used to execute database queries for User Sign Up Phone API.
     *
     * @param array $params_arr params_arr array to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function create_user_v1($params_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (!empty($params_arr["first_name"])) {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (!empty($params_arr["last_name"])) {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (!empty($params_arr["user_name"])) {
                $this->db->set("vUserName", $params_arr["user_name"]);
            }
            if (!empty($params_arr["email"])) {
                $this->db->set("vEmail", $params_arr["email"]);
            }
            if (isset($params_arr["mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            if (!empty($params_arr["user_profile"]) && !empty($params_arr["user_profile"])) {
                $this->db->set("vProfileImage", $params_arr["user_profile"]);
            }
            if (!empty($params_arr["dob"])) {
                $this->db->set("dDob", $params_arr["dob"]);
            }
            if (!empty($params_arr["password"])) {
                $this->db->set("vPassword", $params_arr["password"]);
            }
            if (!empty($params_arr["address"])) {
                $this->db->set("tAddress", $params_arr["address"]);
            }
            if (isset($params_arr["city"])) {
                $this->db->set("vCity", $params_arr["city"]);
            }
            if (!empty($params_arr["latitude"])) {
                $this->db->set("dLatitude", $params_arr["latitude"]);
            }
            if (!empty($params_arr["longitude"])) {
                $this->db->set("dLongitude", $params_arr["longitude"]);
            }
           
            if (!empty($params_arr["state_name"])) {
                $this->db->set("vStateName", $params_arr["state_name"]);
            }
            if (!empty($params_arr["zipcode"])) {
                $this->db->set("vZipCode", $params_arr["zipcode"]);
            }
            $this->db->set("eStatus", $params_arr["status"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            if (!empty($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (!empty($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (!empty($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (!empty($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set($this->db->protect("vEmailVerificationCode"), $params_arr["_vemailverificationcode"], FALSE);
            if (!empty($params_arr["auth_token"])) {
                $this->db->set("vAccessToken", $params_arr["auth_token"]);
            }
            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);
            $this->db->set("vTermsConditionsVersion", $params_arr["_vtermsconditionsversion"]);
            $this->db->set("vPrivacyPolicyVersion", $params_arr["_vprivacypolicyversion"]);
            $this->db->insert("users");
            $user_id = $this->db->insert_id();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$user_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "user_id";
            $result_arr[0][$result_param] = $user_id;
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
     * Used to execute database queries for User Sign Up Phone API.
     *
     * @param string $insert_id  is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_v1($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS email_user_name", FALSE);
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("u.iUserId =", $insert_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

            $success = 1;
        } catch (Exception $e) {
            
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
     * This method is used to execute database queries for Social Sign Up API.
     * 
     * @param array $params_arr params_arr array to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function create_user_social($params_arr = array())
    {
       
        try {
            $message = "";
            $result_arr = array();
            
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            $this->db->trans_begin();

            if (isset($params_arr["first_name"])) {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (isset($params_arr["last_name"])) {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (isset($params_arr["user_name"])) {
                $this->db->set("vUserName", $params_arr["user_name"]);
            }
            if (isset($params_arr["email"])) {
                $this->db->set("vEmail", $params_arr["email"]);
            }
            if (isset($params_arr["mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            if (isset($params_arr["user_profile"]) && !empty($params_arr["user_profile"])) {
                $this->db->set("vProfileImage", $params_arr["user_profile"]);
            }
            if (isset($params_arr["dob"])) {
                $this->db->set("dDob", $params_arr["dob"]);
            }
            if (isset($params_arr["address"])) {
                $this->db->set("tAddress", $params_arr["address"]);
            }
            if (isset($params_arr["city"])) {
                $this->db->set("vCity", $params_arr["city"]);
            }
            if (isset($params_arr["latitude"])) {
                $this->db->set("dLatitude", $params_arr["latitude"]);
            }
            if (isset($params_arr["longitude"])) {
                $this->db->set("dLongitude", $params_arr["longitude"]);
            }
           
            if (isset($params_arr["state_name"])) {
                $this->db->set("vStateName", $params_arr["state_name"]);
            }
            if (isset($params_arr["zipcode"])) {
                $this->db->set("vZipCode", $params_arr["zipcode"]);
            }
            $this->db->set("eStatus", $params_arr["status"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set($this->db->protect("vEmailVerificationCode"), $params_arr["_vemailverificationcode"], FALSE);
            if (isset($params_arr["auth_token"])) {
                $this->db->set("vAccessToken", $params_arr["auth_token"]);
            }
            if (isset($params_arr["social_login_type"])) {
                $this->db->set("eSocialLoginType", $params_arr["social_login_type"]);
            }
            if (isset($params_arr["social_login_id"])) {
                $this->db->set("vSocialLoginId", $params_arr["social_login_id"]);
            }
            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);
            if (isset($params_arr["_vtermsconditionsversion"])) {
                $this->db->set("vTermsConditionsVersion", $params_arr["_vtermsconditionsversion"]);
            }
            if (isset($params_arr["_vprivacypolicyversion"])) {
                $this->db->set("vPrivacyPolicyVersion", $params_arr["_vprivacypolicyversion"]);
            }
            $this->db->insert("users");

            $user_id = $this->db->insert_id();

            
            if (!$user_id) {
                throw new Exception("Failure in insertion.");
            }

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            
            $result_param = "user_id";
            $result_arr[0][$result_param] = $user_id;
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

    
    /**
     * get_customer_by_email method is used to execute database queries for Forgot Password API.
     * @created  | 29.01.2016
     * @modified ---
     * @param string $email email is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_customer_by_email($email = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("users AS mc");

            $this->db->select("mc.iUserId AS mc_user_id");
            // $this->db->select("mc.vFirstName AS mc_first_name");
            // $this->db->select("mc.vLastName AS mc_last_name");
            // $this->db->select("mc.vEmail AS mc_email");
            // $this->db->select("mc.vUserName AS mc_user_name");
            if (isset($email) && $email['email'] != "")
            {
                $this->db->where("mc.vEmail =", $email['email']);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            #echo $this->db->last_query(); die;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
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
     * get_user_user _images method is used to execute database queries get user images.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */


     public function get_user_personal_images($where_arr = array())
    {
        try {

            if ($where_arr['user_id'] > 0 && $where_arr['image_id'] > 0) {

                $this->db->from("user_images");
                $this->db->select("Distinct(iImageId) AS image_id");
                $this->db->select("vImage as image_url");
                $this->db->where("iUserId =", $where_arr['user_id']);
                $this->db->where_in("iImageId", $where_arr['image_id']);

                $result_objjj = $this->db->get();

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
    
                $imgArr = [];
                $imgArrid = [];
                foreach ($result_objjj->result_array() as $rowB) {
                    $imgArr[] = $rowB['image_url'];
                    $imgArrid[] = $rowB['image_id'];
                }

                $result_arr["image_url"] = $imgArr;
                $result_arr["image_id"] = $imgArrid;
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }



    /**
     * delete_user_media_image method is used to execute database queries delete user images.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */


    public function delete_user_media_image($where_arr = array())
    {
        try {
            $result_arr = array();
            $affected_rows = 0;

            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);

                if (isset($where_arr["image_id"])) {
                    $this->db->where_in("iImageId", $where_arr["image_id"]);

                    $res = $this->db->delete("user_images");

                    //echo $this->db->last_query();
                    //exit;
                    $db_error = $this->db->error();
                    if ($db_error['code']) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    }
                    $affected_rows = $this->db->affected_rows();
                    if (!$res || $affected_rows == -1) {
                        throw new Exception("Failure in updation.");
                    }
                }
            }


            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            $message = "";
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
        //print_r($return_arr);exit;
        return $return_arr;
    }


    /**
     * This method is used to execute database queries for Social Sign Up API.
     *
     * @param string $insert_id insert_id is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_v1_v1($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS email_user_name", FALSE);
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.vTermsConditionsVersion AS terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("u.iUserId =", $insert_id);
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

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

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
     * check_user_exists_or_not method is used to execute database queries for Send Verification Link API.
     * @param string $email email is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_user_exists_or_not($email = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("(concat(u.vFirstName,\" \",u.vLastName)) AS email_user_name", FALSE);
            if (isset($email) && $email != "") {
                $this->db->where("u.vEmail =", $email);
            }
            $this->db->where_in("u.eEmailVerified", array('No'));
            $this->db->where_in("u.eStatus", array('Inactive'));

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();


            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * update_email_verification_code method is used to execute database queries for Send Verification Link API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_email_verification_code($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["email"]) && $where_arr["email"] != "") {
                $this->db->where("vEmail =", $where_arr["email"]);
            }
            if (isset($params_arr["email_confirmation_code"])) {
                $this->db->set("vEmailVerificationCode", $params_arr["email_confirmation_code"]);
            }
            $res = $this->db->update("users");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * get_user_login_details method is used to execute database queries for User Login Email API.
     *
     * @param string $auth_token auth_token is used to process query block.
     * @param string $where_clause where_clause is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_login_details($auth_token = '', $where_clause = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("(" . $this->db->escape("" . $auth_token . "") . ") AS auth_token1", FALSE);
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.iIsMobileNoVerified AS u_is_mobile_number_verified");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.iFirstLogin AS u_first_login");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->select("u.eModeratorFlag AS eModeratorFlag");
            $this->db->where("" . $where_clause . "", FALSE, FALSE);
            ///$this->db->where("uiUserId =", 6);

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

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        //$this->db->_reset_all();
        //echo $this->db->last_query();
        //exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * get_subscription_details method is used to execute database queries for User Login Email API.
     *
     * @param string $auth_token auth_token is used to process query block.
     * @param string $where_clause where_clause is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */

    public function get_subscription_details($user_id)
    {
        try
        {

          $this->db->select('vProductId as product_id,dLatestExpiryDate,"" as subscription_status, lReceiptData as purchase_token'); //vOrginalTransactionId
            $this->db->from('user_subscription');
            $this->db->where('iUserId',$user_id);
            $this->db->order_by('dLatestExpiryDate','DESC');
             $result_obj = $this->db->get();
             //echo $this->db->last_query();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
            }
        catch(Exception $e)
        {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


    /**
     * update_device_details method is used to execute database queries for User Login Email API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_device_details($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }
            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set($this->db->protect("vAccessToken"), $params_arr["_vaccesstoken"], FALSE);
            $this->db->set("iFirstLogin", 1);
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
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
     * remove_device_token method is used to execute database queries for User Login Email API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function remove_device_token($params_arr = array(), $where_arr = array())
    {
        try {

            $message = "";
            $result_arr = array();
            if (isset($where_arr["device_token"]) && $where_arr["device_token"] != "") {
                $this->db->where("vDeviceToken =", $where_arr["device_token"]);
            }
            
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", "");
            }
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
       // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }



     /**
     * update_mobile_number_deatils method is used to execute database queries for User Login Email API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_mobile_number_deatils($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
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
     * update_mobile_number_verified method is used to execute database queries for User Login Email API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_mobile_number_verified($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["is_mobile_number_verified"])) {
                $this->db->set("iIsMobileNoVerified", $params_arr["is_mobile_number_verified"]);
            }
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
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
     * get_user_login_details_v1 method is used to execute database queries for User Login Phone API.
     *
     * @param string $auth_token auth_token is used to process query block.
     * @param string $where_clause where_clause is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_login_details_v1($auth_token = '', $where_clause = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
           

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
         
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("u.iFirstLogin AS u_first_login");
            $this->db->select("(" . $this->db->escape("" . $auth_token . "") . ") AS auth_token1", FALSE);
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.iIsMobileNoVerified AS u_is_mobile_number_verified");
            //$this->db->select("ms.vState AS ms_state");
            $this->db->select("u.eOneTimeTransaction AS e_one_time_transaction");
            $this->db->select("u.tOneTimeTransaction AS t_one_time_transaction");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->where("" . $where_clause . "", FALSE, FALSE);

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

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

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

    /**
     * update_device_details_v1 method is used to execute database queries for User Login Phone API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_device_details_v1($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }
            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            $this->db->set($this->db->protect("vAccessToken"), $params_arr["_vaccesstoken"], FALSE);
            $this->db->set("iFirstLogin", 1);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * get_user_login_details_v1_v1 method is used to execute database queries for Social Login API.
     *
     * @param string $auth_token auth_token is used to process query block.
     * @param string $where_clause where_clause is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_social_login_details($input_params = array())
    {   
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
          
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
          
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("u.iFirstLogin AS u_first_login");
            $this->db->select("(" . $this->db->escape("" . $input_params['auth_token'] . "") . ") AS auth_token1", FALSE);
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.iIsMobileNoVerified AS u_is_mobile_number_verified");
            //$this->db->select("ms.vState AS ms_state");
            $this->db->select("u.eOneTimeTransaction AS e_one_time_transaction");
            $this->db->select("u.tOneTimeTransaction AS t_one_time_transaction");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->select("u.eModeratorFlag AS eModeratorFlag");
            //$this->db->where("" . $where_clause . "", FALSE, FALSE);
            $this->db->where("vEmail", $input_params['email']);

            $this->db->limit(1);

            $result_obj = $this->db->get();
            #echo $this->db->last_query();die;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

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
     * get_user_login_details_v1_v1 method is used to execute database queries for Social Login API.
     *
     * @param string $auth_token auth_token is used to process query block.
     * @param string $where_clause where_clause is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_login_details_v1_v1($auth_token = '', $where_clause = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
          

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
          
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("u.iFirstLogin AS u_first_login");
            $this->db->select("(" . $this->db->escape("" . $auth_token . "") . ") AS auth_token1", FALSE);
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.iIsMobileNoVerified AS u_is_mobile_number_verified");
            //$this->db->select("ms.vState AS ms_state");
            $this->db->select("u.eOneTimeTransaction AS e_one_time_transaction");
            $this->db->select("u.tOneTimeTransaction AS t_one_time_transaction");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->select("u.eModeratorFlag AS eModeratorFlag");
            $this->db->where("" . $where_clause . "", FALSE, FALSE);

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

            $result_arr[0]['change_password'] = empty($result_arr[0]['password']) ? "0" : "1";

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
     * update_device_details_v1_v1 method is used to execute database queries for Social Login API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_device_details_v1_v1($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }
            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);
            }
            if (isset($params_arr["device_token"])) {
                $this->db->set("vDeviceToken", $params_arr["device_token"]);
            }
            if (isset($params_arr["social_login_type"])) {
                $this->db->set("eSocialLoginType", $params_arr["social_login_type"]);
            }
            if (isset($params_arr["social_login_id"])) {
                $this->db->set("vSocialLoginId", $params_arr["social_login_id"]);
            }
            $this->db->set($this->db->protect("vAccessToken"), $params_arr["_vaccesstoken"], FALSE);
            $this->db->set("iFirstLogin", 1);
            $res = $this->db->update("users");

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
     * This method is used to execute database queries for Forgot Password API.
     * 
     * @param string $email email is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function check_email_exists($email = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.eStatus AS u_status");
            $this->db->select("(concat(u.vFirstName,\" \",u.vLastName)) AS email_username", FALSE);
            $this->db->select("u.vEmail AS u_email");
            if (isset($email) && $email != "") {
                $this->db->where("u.vEmail =", $email);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
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
     * This method is used to execute database queries for Forgot Password API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_reset_key($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["email"]) && $where_arr["email"] != "") {
                $this->db->where("vEmail =", $where_arr["email"]);
            }
            if (isset($params_arr["reset_key"])) {
                $this->db->set("vResetPasswordCode", $params_arr["reset_key"]);
            }
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");

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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * reset_password method is used to execute database queries for Reset Password API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function reset_password($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["reset_key"]) && $where_arr["reset_key"] != "") {
                $this->db->where("vResetPasswordCode =", $where_arr["reset_key"]);
            }
            if (isset($params_arr["new_password"])) {
                $this->db->set("vPassword", $params_arr["new_password"]);
            }
            $this->db->set($this->db->protect("vResetPasswordCode"), $params_arr["_vresetpasswordcode"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * This method is used to execute database queries for Forgot Password Phone API.
     * 
     * @param string $mobile_number mobile_number is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_by_mobile_number($mobile_number = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.eModeratorFlag AS eModeratorFlag");
            $this->db->select("(concat(u.vFirstName,\"\",u.vLastName)) AS msg_user_name", FALSE);
            if (isset($mobile_number) && $mobile_number != "") {
                $this->db->where("u.vMobileNo =", $mobile_number);
            }

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

    /**
     * This method is used to execute database queries for Forgot Password Phone API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_reset_key_phone($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["mobile_number"]) && $where_arr["mobile_number"] != "") {
                $this->db->where("vMobileNo =", $where_arr["mobile_number"]);
            }
            if (isset($params_arr["reset_key"])) {
                $this->db->set("vResetPasswordCode", $params_arr["reset_key"]);
            }
            $res = $this->db->update("users");
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
            $message = "";
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
     * Used to execute database queries for Reset Password Phone API.
     *
     * @param string $mobile_number mobile_number is used to process query block.
     * @param string $reset_key reset_key is used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function check_mobile_number($mobile_number = '', $reset_key = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");

            $this->db->select("u.iUserId AS u_user_id");
            if (isset($mobile_number) && $mobile_number != "") {
                $this->db->where("u.vMobileNo =", $mobile_number);
            }
            if (isset($reset_key) && $reset_key != "") {
                $this->db->where("u.vResetPasswordCode =", $reset_key);
            }

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
     * Used to execute database queries for Reset Password Phone API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_user_password($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }
            if (isset($where_arr["mobile_number"]) && $where_arr["mobile_number"] != "") {
                $this->db->where("vMobileNo =", $where_arr["mobile_number"]);
            }
            if (isset($where_arr["reset_key"]) && $where_arr["reset_key"] != "") {
                $this->db->where("vResetPasswordCode =", $where_arr["reset_key"]);
            }
            if (isset($params_arr["new_password"])) {
                $this->db->set("vPassword", $params_arr["new_password"]);
            }
            $this->db->set($this->db->protect("vResetPasswordCode"), $params_arr["_vresetpasswordcode"], FALSE);
            $res = $this->db->update("users");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * This method is used to execute database queries for Edit Profile API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_profile($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->start_cache();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            $this->db->where_in("eStatus", array('Active'));
            $this->db->stop_cache();
            if (isset($params_arr["first_name"])) {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (isset($params_arr["last_name"])) {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (isset($params_arr["user_profile"]) && !empty($params_arr["user_profile"])) {
                $this->db->set("vProfileImage", $params_arr["user_profile"]);
            }

            if (isset($params_arr["delete_user_profile"]) && !empty($params_arr["delete_user_profile"])) {
                $this->db->set("vProfileImage", "");
            }


            if (isset($params_arr["dob"])) {
                $this->db->set("dDob", $params_arr["dob"]);
            }
            if (isset($params_arr["address"])) {
                $this->db->set("tAddress", $params_arr["address"]);
            }
            if (isset($params_arr["city"])) {
                $this->db->set("vCity", $params_arr["city"]);
            }
            if (isset($params_arr["latitude"])) {
                $this->db->set("dLatitude", $params_arr["latitude"]);
            }
            if (isset($params_arr["longitude"])) {
                $this->db->set("dLongitude", $params_arr["longitude"]);
            }
           
            if (isset($params_arr["state_name"])) {
                $this->db->set("vStateName", $params_arr["state_name"]);
            }
            if (isset($params_arr["zipcode"])) {
                $this->db->set("vZipCode", $params_arr["zipcode"]);
            }
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            if (isset($params_arr["user_name"])) {
                $this->db->set("vUserName", $params_arr["user_name"]);
            }
            if (isset($params_arr["mobile_number"])) {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            $res = $this->db->update("users");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * This method is used to execute database queries for Edit Profile API.
     *
     * @param string $user_id user_id is used to process query block.

     * @return array $return_arr returns response of query block.
     */
    public function get_updated_details($user_id = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
          

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            //$this->db->select("u.vUserName AS u_user_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vPassword AS password");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
           
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.ePushNotify AS u_push_notify");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            $this->db->select("u.eModeratorFlag AS eModeratorFlag");
            if (isset($user_id) && $user_id != "") {
                $this->db->where("u.iUserId =", $user_id);
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

            $subscription = array();
            $subscription_plans = array();

            if (isset($result_arr[0]["eModeratorFlag"]) && $result_arr[0]["eModeratorFlag"] == '1') {
                $subscription[0]["subscription_status"] = 1;
            } else if (isset($result_arr[0]["u_user_id"]) && $result_arr[0]["u_user_id"] > 0) {
               

                $status_data = $this->get_subscription_details($result_arr[0]["u_user_id"]);
              
                if(count($status_data['data']) > 0){
                    foreach ($status_data['data'] as $key => $value) {
                        if (in_array($value['product_id'], $subscription_plans)) {
                            continue;
                        }
    
                        $expire_date = $value['dLatestExpiryDate'];
    
                        unset($value['dLatestExpiryDate']);
                        $value['subscription_status'] = $this->get_subscription_status($expire_date);
    
                        // print_r($value);
    
                        $subscription[] = $value;
                        $subscription_plans[] = $value['product_id'];
                    }
                }
               
            }

            $result_arr[0]["subscription"] = $subscription;
            $result_arr[0]['change_password'] = $result_arr[0]['password'] == "" ? "0" : "1";
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }

        $this->db->_reset_all();
        
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }


       /**
     * This method is used to execute database queries for get user details API.
     *
     * @param string $user_id user_id is used to process query block.

     * @return array $return_arr returns response of query block.
     */
    public function get_user_profile_details($user_id = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("users AS u");
          

            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.eSocialLoginType AS u_social_login_type");
            $this->db->select("u.vSocialLoginId AS u_social_login_id");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
           
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.vAccessToken AS u_access_token");
            $this->db->select("u.eDeviceType AS u_device_type");
            $this->db->select("u.vDeviceModel AS u_device_model");
            $this->db->select("u.vDeviceOS AS u_device_os");
            $this->db->select("u.vDeviceToken AS u_device_token");
            $this->db->select("u.eStatus AS u_status");
            $this->db->select("u.dtAddedAt AS u_added_at");
            $this->db->select("u.dtUpdatedAt AS u_updated_at");
            $this->db->select("u.eEmailVerified AS u_email_verified");
            $this->db->select("u.vTermsConditionsVersion AS u_terms_conditions_version");
            $this->db->select("u.vPrivacyPolicyVersion AS u_privacy_policy_version");
            $this->db->select("u.eLogStatus AS u_log_status_updated");
            if (isset($user_id) && $user_id != "") {
                $this->db->where("u.iUserId =", $user_id);
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

    /**
     * update_email_verfied_status method is used to execute database queries for Admin Update User status In Listing API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_email_verfied_status($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }

            $this->db->set("eEmailVerified", $params_arr["_eemailverified"]);
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $this->db->set($this->db->protect("dtDeletedAt"), $params_arr["_dtdeletedat"], FALSE);
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * update_transaction_data method is used to execute database queries for Go Ad Free API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_transaction_data($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            $this->db->where_in("eStatus", array('Active'));

            $this->db->set("eOneTimeTransaction", $params_arr["_eonetimetransaction"]);
            if (isset($params_arr["one_time_transaction_data"])) {
                $this->db->set("tOneTimeTransaction", $params_arr["one_time_transaction_data"]);
            }
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * This method is used to execute database queries for Delete Account API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function delete_user_account($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }

            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set("vAccessToken", NULL);
            $this->db->set("vDeviceToken", NULL);
            $this->db->set($this->db->protect("dtDeletedAt"), $params_arr["_dtdeletedat"], FALSE);
            $res = $this->db->update("users");
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    public function get_subscription_status($expire_date)
    {
        $current_timezone = date_default_timezone_get();
        // convert the current timezone to UTC
        date_default_timezone_set('UTC');
        $current_date = date("Y-m-d H:i:s");
        // Again coverting into local timezone
        date_default_timezone_set($current_timezone);

        if (strtotime($expire_date) > strtotime($current_date) || $expire_date == "0000-00-00 00:00:00") {
            $subscription_status = 1;
        } else {
             $subscription_status = 0; 
            //$subscription_status = 1; // in production set it to 0
        }

        return $subscription_status;
    }

    /**
     * login user as social method is used to execute database queries for Send Verification Link API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_existing_account($input_params = array())
    {
        try {
            $message = "";
            $result_arr = array();


            $this->db->where("vEmail", $input_params['email']);
            
            $this->db->set("eSocialLoginType", $input_params["social_login_type"]);
            $this->db->set("vSocialLoginId", $input_params["social_login_id"]);
            $this->db->set("vAccessToken", $input_params["auth_token"]);
            $this->db->set("eDeviceType", $input_params["device_type"]);
            $this->db->set("vDeviceModel", $input_params["device_model"]);
            $this->db->set("vDeviceOS", $input_params["device_os"]);
            $this->db->set("vDeviceToken", $input_params["device_token"]);

            $this->db->set("eEmailVerified", 'Yes');
            $this->db->set("eStatus", 'Active');
            $this->db->set("iFirstLogin", 1);

            

            $res = $this->db->update("users");
            //echo $this->db->last_query(); die;
            $affected_rows = $this->db->affected_rows();

            if($affected_rows > 0){
                $this->db->from("users AS u");
                $this->db->select("u.iUserId AS u_user_id");
        
                if (isset($input_params['email']) && $input_params['email'] != "") {
                    $this->db->where("u.vEmail =", $input_params['email']);
                }

                if (isset($input_params['social_login_id']) && $input_params['social_login_id'] != "") {
                    $this->db->where("u.vSocialLoginId =", $input_params['social_login_id']);
                }
    
                $this->db->limit(1);
    
                $result_obj2 = $this->db->get();
                $result_arr2 = is_object($result_obj2) ? $result_obj2->result_array() : array();
                $result_arr[0]["user_id"] = $result_arr2[0]["u_user_id"];
            }

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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * update_image method is used to execute database queries for User Login Email API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_image($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["u_user_id"]) && $where_arr["u_user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["u_user_id"]);
            }

            if (isset($params_arr["user_profile"])) {
                $this->db->set("vProfileImage", $params_arr["user_profile"]);
            }
           
            $res = $this->db->update("users");
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
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $params_arr['user_id'] = $where_arr["u_user_id"];
            $this->general->apiLogger($params_arr, $e);
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

}
