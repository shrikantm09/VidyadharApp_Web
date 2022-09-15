<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Verification Link Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Send Verification Link
 *
 * @class Send_verification_link.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Send_verification_link.php
 *
 * @version 4.4
 *
 */

class Send_verification_link extends Cit_Controller
{
    public $settings_params;
   
    /* @var array $output_params contains output parameters  */
    public $output_params;

    /* @var array $single_keys contains single array */
    public $single_keys;

    /* @var array $multiple_keys contains multiple array */
    public $multiple_keys;

    /* @var array $block_result contains query returns result array*/
    public $block_result;
    
/*
      * To initialize class objects/variables.
*/
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "check_user_exists_or_not",
            "update_email_verification_code",
        );
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('send_verification_link_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     *
     * @return array $valid_res returns output response of API.
     */
    public function rules_send_verification_link($request_arr = array())
    {
        $valid_arr = array(
            "email" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "email_required",
                ),
                array(
                    "rule" => "email",
                    "value" => TRUE,
                    "message" => "email_email",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "send_verification_link");

        return $valid_res;
    }

    /**
     * This method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_send_verification_link($request_arr = array(), $inner_api = FALSE)
    {

        try {
            $validation_res = $this->rules_send_verification_link($request_arr);
            if ($validation_res["success"] == "0") { if ($inner_api === TRUE) {

                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }

            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->check_user_exists_or_not($input_params);
             
            $condition_res = $this->check_user_exists($input_params);
            
            if ($condition_res["success"]) {
                
                $input_params = $this->custom_function($input_params);

                if(count($input_params['custom_function'])<=0){
                    throw new Exception("No Email confirmation data found.");
                }
                
                $input_params = $this->update_email_verification_code($input_params);
                 
                 if(count($input_params['update_email_verification_code'])<=0){
                    throw new Exception("failed to update email verification code");
                }
               
                $input_params = $this->email_notification($input_params);
                $output_response = $this->users_finish_success_2($input_params);

                return $output_response;
            } else {
                $output_response = $this->users_finish_success($input_params);

                return $output_response;
            }
        } catch(Exception $e) {
            $this->general->apiLogger($input_params, $e);
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
    public function check_user_exists_or_not($input_params = array())
    {

        $this->block_result = array();
        try {

            $email = isset($input_params["email"]) ? $input_params["email"] : "";
            $this->block_result = $this->users_model->check_user_exists_or_not($email);
           
             if ($this->block_result["success"]=='0') {
               
                throw new Exception("No records found.");
            }
        } catch(Exception $e) {
            $this->general->apiLogger($input_params, $e);  
            $success = 0;
            $this->block_result["data"] = array();
        }
       
        $input_params["check_user_exists_or_not"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function check_user_exists($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_user_exists_or_not"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch(Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;

        return $this->block_result;
    }

    /**
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this->general, "prepareEmailVerificationCode")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->prepareEmailVerificationCode($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function update_email_verification_code($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["email"])) {
                $where_arr["email"] = $input_params["email"];
            } if (isset($input_params["email_confirmation_code"])) {
                $params_arr["email_confirmation_code"] = $input_params["email_confirmation_code"];
            }
            $this->block_result = $this->users_model->update_email_verification_code($params_arr, $where_arr);

        } catch(Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_email_verification_code"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process email notification.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function email_notification($input_params = array())
    {

        $this->block_result = array();
        try {

            $email_arr["vEmail"] = $input_params["email"];

            $email_arr["vUsername"] = $input_params["email_user_name"];
            $email_arr["email_confirmation_link"] = $input_params["email_confirmation_link"];

            $success = $this->general->sendMail($email_arr, "SIGNUP_EMAIL_CONFIRMATION", $input_params);

            $log_arr = array();
            $log_arr['eEntityType'] = 'General';
            $log_arr['vReceiver'] = is_array($email_arr["vEmail"]) ? implode(",", $email_arr["vEmail"]) : $email_arr["vEmail"];
            $log_arr['eNotificationType'] = "EmailNotify";
            $log_arr['vSubject'] = $this->general->getEmailOutput("subject");
            $log_arr['tContent'] = $this->general->getEmailOutput("content");
            if (!$success) {
                $log_arr['tError'] = $this->general->getNotifyErrorOutput();
            }
            $log_arr['dtSendDateTime'] = date('Y-m-d H:i:s');
            $log_arr['eStatus'] = ($success) ? "Executed" : "Failed";
            $this->general->insertExecutedNotify($log_arr);
            if (!$success) {
                throw new Exception("Failure in sending mail.");
            }
            $success = 1;
            $message = "Email notification send successfully.";
        } catch(Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["email_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_verification_link";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
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
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_verification_link";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
