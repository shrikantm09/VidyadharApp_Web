<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Users Model
 *
 * @category webservice
 *
 * @package users
 *
 * @subpackage models
 *
 * @module Users
 *
 * @class Users_model.php
 *
 * @path application\webservice\users\models\Users_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Users_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * get_all_user_list method is used to execute database queries for User List API.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_all_user_list()
    {
        try {
            $result_arr = array();

            $this->db->from("users AS u");
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vProfileImage AS u_profile_image");
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = SUCCESS_CODE;
        } catch (Exception $e) {
            $success = FAILED_CODE;
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
    * This method is used to execute database queries for User profile details API.
    *
    * @return array $return_arr returns response of query block.
    */
    public function get_user_profile_details($params_arr = array())
    {
        try {
            $result_arr = array();

            $this->db->from("users AS u");
           
            $this->db->select("u.iUserId AS u_user_id");
            $this->db->select("u.vFirstName AS u_first_name");
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vProfileImage AS u_profile_image");
            $this->db->select("u.tAddress AS u_address");
            //$this->db->select("u.vAptSuite AS u_apt_suite");
            $this->db->select("u.vCity AS u_city");
            $this->db->select("u.vStateName AS u_state_name");
            $this->db->select("u.vZipCode AS u_zip_code");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            
           
            $block_status = "(select ub.status from user_block AS ub where ub.user_id =". $params_arr['user_id']." && ub.block_user_id =". $params_arr['other_user_id'].") as block_status";
            $this->db->select($block_status);

            if (isset($params_arr['other_user_id'])) {
                $this->db->where("u.iUserId", $params_arr['other_user_id']);
            }
            $result_obj = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = SUCCESS_CODE;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = FAILED_CODE;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        //print_r($result_arr);die;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
    * This method is used to execute database queries for check_user_status API.
    *
    * @param array $params_arr params_arr array to process query block.
    *
    * @return array $return_arr returns response of query block.
    */
    public function check_user_status($params_arr = array())
    {
        try {
            $result_arr = array();

            $this->db->from("users");
            $this->db->select("eStatus AS user_status");
            if (isset($params_arr['other_user_id'])) {
                $this->db->where("iUserId", $params_arr['other_user_id']);
            }
            $result_obj = $this->db->get();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = SUCCESS_CODE;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = FAILED_CODE;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        //print_r($result_arr);die;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
