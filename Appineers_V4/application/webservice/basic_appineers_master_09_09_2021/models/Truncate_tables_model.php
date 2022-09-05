<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Query Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User Query
 *
 * @class truncate_tables_model.php
 *
 * @path application\webservice\basic_appineers_master\models\truncate_tables_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 11.12.2019
 */

class Truncate_tables_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_log');
        $this->request_arr = array('abusive_reports', 'api_accesslogs', 'user_query', 'user_query_images', 'users' );
    }

    /**
     * This method is used to execute database queries for Truncate user related tables API.
     *
     * @param array $params_arr params_arr array to process query block.
     * 
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */

    public function truncate_user_related_tables()
    {
        try {

            if (count($this->request_arr) > 0) {
                foreach ($this->request_arr as $value) {

                    $this->db->query("TRUNCATE TABLE $value ");

                }

                $db_error = $this->db->error();
                if ($db_error['code']) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }

                $success = 1;
               
            }
        } catch (Exception $e) {
            $success = 0;
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }

        $return_arr["success"] = $success;
        return $return_arr;
    }
}
