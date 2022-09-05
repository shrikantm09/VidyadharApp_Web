<?php


/**
 * Description of Edit Profile Extended Controller
 *
 * @module Extended Edit Profile
 *
 * @class Cit_Edit_profile.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Cit_Edit_profile.php
 *
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Edit_profile extends Edit_profile
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
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return unique user status & message.
     */
    public function checkUniqueUser($input_params = array())
    {
        $return_arr['message'] = '';
        $return_arr['status'] = '1';
        try {
            if (!empty($input_params['mobile_number'])) {
                $this->db->select('vMobileNo');
                $this->db->from('users');
                $this->db->where('vMobileNo', $input_params['mobile_number']);
                $this->db->where_not_in('iUserId', $input_params['user_id']);
                $mobile_number_data = $this->db->get();
                $mobile_number_data = is_object($mobile_number_data) ? $mobile_number_data->result_array() : array();

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }

                if ($mobile_number_data[0]['vMobileNo'] == $input_params['mobile_number']) {
                    $return_arr['message'] = "Account with this mobile number already exists.";
                    $return_arr['status'] = "0";

                    return  $return_arr;
                }
            }
            if (!empty($input_params['user_name'])) {
                $this->db->select('vUserName');
                $this->db->from('users');
                $this->db->where('vUserName', $input_params['user_name']);
                $this->db->where_not_in('iUserId', $input_params['user_id']);
                $user_name_data = $this->db->get();
                $user_name_data = is_object($user_name_data) ? $user_name_data->result_array() : array();

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }

                if ($user_name_data[0]['vUserName'] == $input_params['user_name']) {
                    $return_arr['message'] = "Account with this username already exists.";
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
}
