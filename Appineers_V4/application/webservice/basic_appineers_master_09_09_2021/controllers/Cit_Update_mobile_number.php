<?php


/**
 * Description of Check Unique User Extended Controller
 * 
 * @module Extended Check Unique User
 * 
 * @class Cit_Update_mobile_number.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Update_mobile_number.php
 * 
 * @author CIT Dev Team
 * 
 * @date 06.02.2020
 */

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class Cit_Update_mobile_number extends Update_mobile_number
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
      if ($input_params['type'] == 'email') {
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
        
      } else if ($input_params['type'] == 'phone') {
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
