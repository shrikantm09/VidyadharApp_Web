<?php

/**
 * Description of Reset Password Extended Model
 *
 * @module Extended Reset Password
 *
 * @class Cit_Reset_password_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Cit_Reset_password_model.php
 *
 * @author CIT Dev Team
 *
 * @date 16.09.2019
 */

class Cit_Reset_password_model extends Reset_password_model
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
     * Get reset password key.
     *
     * @param string $reset_key to process loop flow.
     *
     * @return array $data.
     */
    public function getResetPasswordKey($reset_key)
    {
        try {
            $this->db->select('vResetPasswordCode,iUserId');
            $this->db->from('users');
            $this->db->where('vResetPasswordCode', $reset_key);
            $result_obj = $this->db->get();

            $data = is_object($result_obj) ? $result_obj->result_array() : array();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        } catch (Exception $e) {
            $params_arr['reset_key'] = $reset_key;
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }
        
        return $data;
    }
}
