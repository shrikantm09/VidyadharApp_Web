<?php
defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Description of Logout Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Logout
 *
 * @class Logout.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Logout.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Logout extends Cit_Controller
{
    /* @var array $settings_params contains setting (config) parameters */
    public $settings_params;

    /* @var array $output_params contains output parameters */
    public $output_params;

    /* @var array $single_keys contains single array */
    public $single_keys;

    /* @var array $multiple_keys contains multiple array */
    public $multiple_keys;

    /** @var array $block_result contains query returns result array*/
    public $block_result;

    /**
     * To initialize class objects/variables.
     */

    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "logout",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('logout_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_logout($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "logout");

        return $valid_res;
    }

    /**
     * This method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_logout($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_logout($request_arr);
            if ($validation_res["success"] == FAILED_CODE) {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            /*********unset user acces token and logout from application ***********/
            $input_params = $this->logout($input_params);

            $output_response = $this->users_finish_success($input_params);
            return $output_response;
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        
        return $output_response;
    }

    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function logout($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"])) {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            $params_arr["_vaccesstoken"] = "''";
            //$params_arr["_vdevicetoken"] = "''";
            $this->block_result = $this->users_model->logout($params_arr, $where_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["logout"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "logout";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
