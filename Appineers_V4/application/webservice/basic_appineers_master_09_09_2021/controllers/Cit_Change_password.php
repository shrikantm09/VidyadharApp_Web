<?php


/**
 * Description of Change Password Extended Controller
 * 
 * @module Extended Change Password
 * 
 * @class Cit_Change_password.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Change_password.php
 * 
 * @author CIT Dev Team
 * 
 * @date 08.10.2019
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Change_password extends Change_password
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
     * Used to check password match.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $return_arr return password status & message.
     */
    public function checkPasswordMatch($input_params = array())
    {

        try {
            $this->db->select('iUserId,vPassword');
            $this->db->from('users');
            $this->db->where('iUserId', $input_params['user_id']);
            $data = $this->db->get()->result_array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $params['old_password'] = $input_params['old_password'];
            $params['mc_password'] = $data[0]['vPassword'];
            $result = $this->general->verifyCustomerPassword($params);

            if ($result[0]['is_matched'] == 1) {
                $return_array['matched'] = 1;
            } else {
                $return_array['matched'] = 0;
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }


        return $return_array;
    }
}
