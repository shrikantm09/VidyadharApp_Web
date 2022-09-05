<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User_list Add Controller
 *
 * @category webservice
 *
 * @package business
 *
 * @subpackage controllers
 *
 * @module User_list Add
 *
 * @class User_list.php
 *
 * @path application\webservice\business\controllers\User_list.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class User_list extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "set_user_list",
            "get_user_list_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('users_model');
    }
    
    /**
     * start_user_list method is used to initiate api execution flow.
     * @created  Shri | 15.03.2020
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_user_list($request_arr = array(), $inner_api = false)
    {
        try {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = $func_array = array();
            $input_params = $this->get_all_user_list($input_params);
            
            $key = "get_all_user_list";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->get_user_list_finish_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->get_user_list_finish_failure($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_all_user_list method is used to process query block.
     * @created shri | 15.03.2020
     * @modified shri | 15.03.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_user_list($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->users_model->get_all_user_list();

            $this->block_result['data'] = array_map(function (array $arr) {
                $image_arr = array();
                $image_arr["image_name"] = $arr["u_profile_image"];
                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));

                $image_arr["color"] = "FFFFFF";
                $image_arr["no_img"] = false;
                $image_arr["path"]=  $this->config->item("AWS_FOLDER_NAME") . "/user_profile/". $arr["u_user_id"]; 
                $data_1 = $this->general->get_image_aws($image_arr);
                $arr['u_name'] = $arr['u_first_name'] ." ". $arr['u_last_name'];
                $arr['u_profile_image'] = $data_1;

                return $arr;
            }, $this->block_result['data']);
            
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_all_user_list"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * condition method is used to process conditions.
     * @created shri | 15.03.2020
     * @modified shri | 15.03.2020
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array(), $key)
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params[$key]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
    * get_user_list_finish_success method is used to process finish flow.
    * @created shri | 15.03.2020
    * @modified shri | 15.03.2020
    * @param array $input_params input_params array to process loop flow.
    * @return array $responce_arr returns responce array of api.
    */
    public function get_user_list_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "get_user_list_finish_success",
        );
        $output_fields = array(
            'u_user_id',
            'u_name',
            'u_profile_image'
        );
        $output_keys = array(
            'get_all_user_list',
        );
        $ouput_aliases = array(
            'get_all_user_list' => 'get_user_list_list',
            'u_user_id' => 'user_id',
            'u_name' => 'name',
            'u_profile_image' => 'profile_image'
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_list_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;
         
        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

    /**
     * get_user_list_finish_failure method is used to process finish flow.
     * @created shri | 15.03.2020
     * @modified shri | 15.03.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_user_list_finish_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "get_user_list_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_list_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
