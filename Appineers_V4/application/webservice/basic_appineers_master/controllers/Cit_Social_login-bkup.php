<?php


/**
 * Description of Social Login Extended Controller
 * 
 * @module Extended Social Login
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
	 * Used to prepare where condition.
	 *
	 * @param array $input_params input_params array to process loop flow.
	 *
	 * @return array $return_arr return where condition.
	 */
	public function helperPrepareWhere(&$input_params = array())
	{

		$allowed_sn = array('facebook', 'google', 'apple');
		$return = array();
		$return[0]['status'] = 1;
		$return[0]['message'] = "";
		$return[0]['where_clause'] = '0=1';
		$where = array();
		if ($input_params['social_login_type'] != '' && $input_params['social_login_id'] != '') {
			if (in_array($input_params['social_login_type'], $allowed_sn)) {
				$where[] = "u.vSocialLoginId='" . trim($input_params['social_login_id']) . "' ";
				$where[] = "u.eSocialLoginType IN('" . trim($input_params['social_login_type']) . "') ";
			} else {
				$return[0]['status'] = 0;
				$return[0]['message'] = "we are supporting facebook,google and apple account for login.";
			}
		} else {
			$return[0]['status'] = 0;
			$return[0]['message'] = "Please provide login detail.";
		}

		$return[0]['where_clause'] = implode("AND ", $where);

		return $return;
	}

	/**
     * Used to check device token exist.
     * 
     * @param array $input_params  array to process loop flow.
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
