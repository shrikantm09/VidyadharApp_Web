<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


/**
 * Description of User Sign Up Phone Extended Controller
 *
 * @module Extended User Sign Up Phone
 *
 * @class Cit_User_sign_up_phone.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Cit_User_sign_up_phone.php
 *
 * @author Suresh Nakate
 *
 * @date 06.09.2021
 */
class Cit_User_sign_up_phone extends User_sign_up_phone
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
     * Used to check unique user.
     *
     * @param array $input_params  array to process loop flow.
     *
     * @return array $return_arr return unique user status & message.
     */
    public function checkUniqueUser($input_params = array())
    {
        $return_arr['message'] = '';
        $return_arr['status'] = '1';
        try {
            if (!empty($input_params['email'])) {
                $this->db->select('vEmail');
                $this->db->from('users');
                $this->db->where('vEmail', $input_params['email']);
                $email_data = $this->db->get();

                $email_data = is_object($email_data) ? $email_data->result_array() : array();

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

            if (!empty($input_params['mobile_number'])) {
                $this->db->select('vMobileNo');
                $this->db->from('users');
                $this->db->where('vMobileNo', $input_params['mobile_number']);
                $mobile_number_data = $this->db->get();

                $mobile_number_data = is_object($mobile_number_data) ? $mobile_number_data->result_array() : array();

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
            if (!empty($input_params['user_name'])) {
                $this->db->select('vUserName');
                $this->db->from('users');
                $this->db->where('vUserName', $input_params['user_name']);
                $user_name_data = $this->db->get();

                $user_name_data = is_object($user_name_data) ? $user_name_data->result_array() : array();

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
