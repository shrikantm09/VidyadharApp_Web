<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Get Config Paramaters Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module Get Config Paramaters
 *
 * @class Get_config_paramaters_model.php
 *
 * @path application\webservice\basic_appineers_master\models\Get_config_paramaters_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 23.12.2019
 */

class Get_config_paramaters_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * This method is used to execute database queries for Update user app version.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     *
     * @return array $return_arr returns response of query block.
     */
    public function update_app_version($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            $updatedEntity = "";

            $this->db->trans_begin();

            if (isset($where_arr["user_id"]) && $where_arr["user_id"] != "") {
                $this->db->where("iUserId =", $where_arr["user_id"]);
            }
            if (isset($params_arr["app_version"])) {
                $this->db->set("vAppVersion", $params_arr["app_version"]);

                $updatedEntity .= 'app_version = "'.$params_arr["app_version"].'",';
            }

            if (isset($params_arr["device_type"])) {
                $this->db->set("eDeviceType", $params_arr["device_type"]);

                $updatedEntity .= 'device_type = "'.$params_arr["device_type"].'",';
            }
            if (isset($params_arr["device_model"])) {
                $this->db->set("vDeviceModel", $params_arr["device_model"]);

                $updatedEntity .= 'device_model = "'.$params_arr["device_model"].'",';
            }
            if (isset($params_arr["device_os"])) {
                $this->db->set("vDeviceOS", $params_arr["device_os"]);

                $updatedEntity .= 'device_os = "'.$params_arr["device_os"].'",';
            }
            if (isset($params_arr["terms_conditions_version"])) {
                $this->db->set("vTermsConditionsVersion", $params_arr["terms_conditions_version"]);

                $updatedEntity .= 'terms_conditions_version = "'.$params_arr["terms_conditions_version"].'",';
            }
            if (isset($params_arr["privacy_policy_version"])) {
                $this->db->set("vPrivacyPolicyVersion", $params_arr["privacy_policy_version"]);

                $updatedEntity .= 'privacy_policy_version = "'.$params_arr["privacy_policy_version"].'",';
            }

            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
            $this->db->reset_query();

            if(strlen($updatedEntity) > 2){
                $insertArr["vEntity"] = $updatedEntity;
                $insertArr["iUserId"] = $where_arr["user_id"];
                //$insertArr["dAddedAt"] = "NOW()";

                $this->db->insert("user_metadata",$insertArr);
            }
            
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
            
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            $this->db->trans_commit();
        } catch (Exception $e) {

            $this->db->trans_rollback();
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
