<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Check Unique User Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Check Unique User
 *
 * @class Check_unique_user.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Check_unique_user.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.02.2020
 */
class Check_unique_user extends Cit_Controller
{
    /* @var array $output_params contains settings parameters */
    public $settings_params;
    
    /* @var array $output_params contains output parameters */
    public $output_params;

    /* @var array $single_keys contains single array */
    public $single_keys;

    /* @var array $multiple_keys contains multiple array */
    public $multiple_keys;

    /* @var array $block_result contains query returns result array*/
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
            "otp_generation",
        );
        $this->multiple_keys = array(
            "format_email_v1",
            "custom_function",
            "get_message_api",
            "send_sms_api",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('check_unique_user_model');
    }

    /**
     * Used to validate api input params.
     *      
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_check_unique_user($request_arr = array())
    {
        $valid_arr = array(
            "type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "type_required",
                )
            ),
            "email" => array(
                array(
                    "rule" => "email",
                    "value" => TRUE,
                    "message" => "email_email",
                )
            ),
            "mobile_number" => array(
                array(
                    "rule" => "number",
                    "value" => TRUE,
                    "message" => "mobile_number_number",
                ),
                array(
                    "rule" => "minlength",
                    "value" => MOBILE_NO_MIN_LENGTH,
                    "message" => "mobile_number_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => MOBILE_NO_MAX_LENGTH,
                    "message" => "mobile_number_maxlength",
                )
            )
            
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "check_unique_user");

        return $valid_res;
    }

    /**
     * This method used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.  
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_check_unique_user($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_check_unique_user($request_arr);
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

            $input_params = $this->format_email_v1($input_params);

            $input_params = $this->custom_function($input_params);

            $condition_res = $this->is_unique_user($input_params);
            if ($condition_res["success"]) {

                $condition_res = $this->condition($input_params);
                if ($condition_res["success"]) {

                    $condition_res = $this->check_mobile_number($input_params);

                    if ($condition_res["success"]) {

                        $input_params = $this->otp_generation($input_params);

                        $input_params = $this->get_message_api($input_params);

                        $input_params = $this->send_sms_api($input_params);

                        $output_response = $this->finish_success_2($input_params);
                        return $output_response;
                    } else {

                        $output_response = $this->finish_success_3($input_params);
                        return $output_response;
                    }
                } else {

                    $output_response = $this->finish_success($input_params);
                    return $output_response;
                }
            } else {

                $output_response = $this->finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        return $output_response;
    }

    /**
     * Used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function format_email_v1($input_params = array())
    {
        if (!method_exists($this->general, "format_email")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->format_email($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["format_email_v1"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     *Used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "checkUniqueUser")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkUniqueUser($input_params);
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
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_unique_user($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["status"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("No Unique User");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        
        return $this->block_result;
    }

    /**
     * Used to process conditions.
    
     * @param array $input_params input_params array to process condition flow.
    
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["type"];
            $cc_ro_0 = "phone";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("User Type not specified");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        
        return $this->block_result;
    }

    /**
     * used to process conditions.
     *
     * @param array $input_params input_params array to process condition flow.
     
     * @return array $block_result returns result of condition block as array.
     */
    public function check_mobile_number($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["mobile_number"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Not unique mobile number");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        
        return $this->block_result;
    }

    /**
     * Used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function otp_generation($input_params = array())
    {
        if (!method_exists($this->general, "generateOtp")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->generateOtp($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["otp_generation"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        
        return $input_params;
    }

    /**
     * used to process custom function.
    
     * @param array $input_params input_params array to process loop flow.
     
     * @return array $input_params returns modfied input_params array.
     */
    public function get_message_api($input_params = array())
    {

        $this->load->module("basic_appineers_master/get_template_message");
        $api_params = array();

        $api_params["template_code"] = "signup_otp";
        if (array_key_exists("otp", $input_params)) {
            $api_params["otp"] = $input_params["otp"];
        }
        $maping_arr = array(
            "message" => "message_signup",
        );
        $result_arr = $this->get_template_message->start_get_template_message($api_params, TRUE);
        $result_keys = is_array($result_arr) ? array_keys($result_arr) : array();
        if ($result_arr["success"] == "-5") {
            $input_params["get_message_api_success"] = $result_arr["success"];
            $input_params["get_message_api_message"] = $result_arr["message"];
            $result_arr["data"] = array();
        } else {
            $input_params["get_message_api_success"] = $result_arr["settings"]["success"];
            $input_params["get_message_api_message"] = $result_arr["settings"]["message"];
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr, $maping_arr);
        $input_params["get_message_api"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * Used to process custom function.
          
     * @param array $input_params input_params array to process loop flow.
     
     * @return array $input_params returns modfied input_params array.
     */
    public function send_sms_api($input_params = array())
    {

        $this->load->module("basic_appineers_master/send_sms");
        $api_params = array();
        if (array_key_exists("mobile_number", $input_params)) {
            $api_params["mobile_number"] = $input_params["mobile_number"];
        }
        if (array_key_exists("message_signup", $input_params)) {
            $api_params["message"] = $input_params["message_signup"];
        }
        $maping_arr = array();
        $result_arr = $this->send_sms->start_send_sms($api_params, TRUE);
        $result_keys = is_array($result_arr) ? array_keys($result_arr) : array();
        if ($result_arr["success"] == "-5") {
            $input_params["send_sms_api_success"] = $result_arr["success"];
            $input_params["send_sms_api_message"] = $result_arr["message"];
            $result_arr["data"] = array();
        } else {
            $input_params["send_sms_api_success"] = $result_arr["settings"]["success"];
            $input_params["send_sms_api_message"] = $result_arr["settings"]["message"];
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr, $maping_arr);
        $input_params["send_sms_api"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        
        return $input_params;
    }

    /**
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "finish_success_2",
        );
        $output_fields = array(
            'otp',
        );
        $output_keys = array(
            'otp_generation',
        );
        $ouput_aliases = array(
            "otp_generation" => "otp",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     *
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(CONFLICT);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
