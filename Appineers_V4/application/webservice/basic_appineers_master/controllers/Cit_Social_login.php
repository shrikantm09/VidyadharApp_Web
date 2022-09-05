<?php


/**
 * Description of Social Sign Up Extended Controller
 * 
 * @module Extended Social Sign Up
 * 
 * @class Cit_Social_login.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Social_login.php
 * 
 * @author Suresh Nakate
 *
 * @date 06.09.2021
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Social_login extends Social_login
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Used to check unique user.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $return_arr return unique user status & message.
     */
    public function checkUniqueUser($input_params = array())
    {
        $return_arr['message'] = '';
        $return_arr['status'] = '1';
        try {

            try {
                $this->db->select('eSocialLoginType,vSocialLoginId');
                $this->db->from('users');
                $this->db->where('eSocialLoginType', $input_params['social_login_type']);
                $this->db->where('vSocialLoginId', $input_params['social_login_id']);
                $this->db->or_where('vEmail', $input_params['email']);
                $social_data = $this->db->get()->result_array();
                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }

                if (!empty($social_data)) {
                    $return_arr['message'] = "This social account already registered, please try using different account.";
                    $return_arr['status'] = "0";

                    return  $return_arr;
                }
                //check unique email
                if (!empty($input_params['email'])) {


                    $this->db->select('vEmail');
                    $this->db->from('users');
                    $this->db->where('vEmail', $input_params['email']);
                    $email_data = $this->db->get()->result_array();
                    $db_error = $this->db->error();
                    if ($db_error['code']) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    }
                    if ($email_data[0]['vEmail'] == $input_params['email']) {
                        $return_arr['message'] = "This email is already registered, please try using different email.";
                        $return_arr['status'] = "0";
                        return  $return_arr;
                    }
                }
                //check unique mobile number
                if (!empty($input_params['mobile_number'])) {
                    $this->db->select('vMobileNo');
                    $this->db->from('users');
                    $this->db->where('vMobileNo', $input_params['mobile_number']);
                    $mobile_number_data = $this->db->get()->result_array();
                    $db_error = $this->db->error();
                    if ($db_error['code']) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    }
                    if ($mobile_number_data[0]['vMobileNo'] == $input_params['mobile_number']) {
                        $return_arr['message'] = "This mobile number already registered, please try using different mobile number.";
                        $return_arr['status'] = "0";
                        return  $return_arr;
                    }
                }

                //check unique username
                if (!empty($input_params['user_name'])) {
                    $this->db->select('vUserName');
                    $this->db->from('users');
                    $this->db->where('vUserName', $input_params['user_name']);
                    $user_name_data = $this->db->get()->result_array();
                    $db_error = $this->db->error();
                    if ($db_error['code']) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    }
                    if ($user_name_data[0]['vUserName'] == $input_params['user_name']) {
                        $return_arr['message'] = "This username already registered, please try using different username.";
                        $return_arr['status'] = "0";
                        return  $return_arr;
                    }
                }
            } catch (Exception $e) {
                $input_params['db_query'] = $this->db->last_query();
                $this->general->apiLogger($input_params, $e);
                $success = 0;
                $message = $e->getMessage();
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return  $return_arr;
    }

     /**
     * Used to check device token exist.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $return_arr return unique user status & message.
     */
    public function checkDeviceTokenExist($input_params = array())
    {


        $return_arr['message'] = '';
        $return_arr['status'] = '0';
        try {
            if (!empty($input_params['device_token'])) {
                $this->db->select('vDeviceToken');
                $this->db->from('users');
                $this->db->where('vDeviceToken', $input_params['device_token']);
                $device_token_data = $this->db->get();

                $device_token_data = is_object($device_token_data) ? $device_token_data->result_array() : array();

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }

                if ($device_token_data[0]['vDeviceToken'] == $input_params['device_token']) {
                    $return_arr['status'] = "1";

                    return  $return_arr;
                }
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return  $return_arr;
    }

}
