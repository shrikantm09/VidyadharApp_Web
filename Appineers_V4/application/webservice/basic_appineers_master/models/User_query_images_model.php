<?php
defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Description of User Query Images Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User Query Images
 *
 * @class User_query_images_model.php
 *
 * @path application\webservice\basic_appineers_master\models\User_query_images_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 11.12.2019
 */

class User_query_images_model extends CI_Model
{
    public $default_lang = 'EN';

      /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->load->library('lib_log');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * This method is used to execute database queries for Feedback images.
     * 
     * @param string $query_id query_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function query_images($query_id = '')
    {
        try {
            $result_arr = array();

            $this->db->from("user_query_images AS uqi");

            $this->db->select("uqi.vQueryImage AS uqi_query_image");
            $this->db->select("uqi.iUserQueryId AS uqi_user_query_id");
            if (isset($query_id) && $query_id != "") {
                $this->db->where("uqi.iUserQueryId =", $query_id);
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

            $message = "";
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        
        return $return_arr;
    }
}
