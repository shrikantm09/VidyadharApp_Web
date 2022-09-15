<?php
defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Description of Reset Password Confirmation Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Reset Password Confirmation
 *
 * @class Reset_password_confirmation.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Reset_password_confirmation.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.02.2020
 */

class Reset_password_confirmation extends Cit_Controller
{
    /** @var array $settings_params */
    public $settings_params;

    /** @var array $output_params */
    public $output_params;

    /** @var array $multiple_keys */
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
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('reset_password_confirmation_model');
        $this->load->library('lib_log');
    }

    /**
     * Used to validate api input params.
     *
     * @param array $request_arr array is used for api input.
     *
     * @return array $valid_res returns validation response of API.
     */
    public function rules_reset_password_confirmation($request_arr = array())
    {
        $valid_arr = array();
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "reset_password_confirmation");

        return $valid_res;
    }

    /**
     * Used to initiate api execution flow.
     *
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     *
     * @return array $output_response returns output response of API.
     */
    public function start_reset_password_confirmation($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_reset_password_confirmation($request_arr);
            if ($validation_res["success"] == FAILED_CODE) {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->custom_function($input_params);

            $condition_res = $this->check_status($input_params);
            if ($condition_res["success"]) {
                $output_response = $this->users_finish_success($input_params);

                return $output_response;
            } else {
                $output_response = $this->finish_success($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }

        return $output_response;
    }

    /**
     * Used to check user exist with reset key.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "checkUserExistsWithResetKey")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkUserExistsWithResetKey($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * Used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_status($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["status"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;

        return $this->block_result;
    }

    /**
     * Used to process finish API success flow.
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

        $func_array["function"]["name"] = "reset_password_confirmation";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);
        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish API failure flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "reset_password_confirmation";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(SESSION_EXPIRE);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}