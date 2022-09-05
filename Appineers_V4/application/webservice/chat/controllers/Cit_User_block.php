<?php

/**
 * Description of blocked user Extended Controller
 *
 * @module Extended blocked user
 *
 * @class Cit_User_block.php
 *
 * @path application\webservice\business\controllers\Cit_User_block.php
 *
 * @author CIT Dev Team
 *
 * @date 10.02.2020
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_User_block extends User_block
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Used to check user blocked OR unblocked.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return unique user status & message.
     */
    public function userBlockedOrNot($input_params = array())
    {
        $return_arr['user_block_status'] = '';
        $return_arr['status'] = '1';
        try {
            if (!empty($input_params['block_user_id'])) {
                $this->db->select('status');
                $this->db->from('user_block');
                $this->db->where('user_id', $input_params['user_id']);
                $this->db->where('block_user_id', $input_params['block_user_id']);
                $result_obj = $this->db->get();

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

                if (count($result_arr) > 0) {
                    $return_arr['user_block_status'] = $result_arr[0]['status'];
                    $return_arr['status'] = "0";
                    return  $return_arr;
                }
            }
        } catch (Exception $e) {
            $input_params['db_query'] = $this->db->last_query();
            $this->general->apiLogger($input_params, $e);
        }

        return  $return_arr;
    }
}
