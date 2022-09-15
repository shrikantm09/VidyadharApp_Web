<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Forgot Password Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Forgot Password
 *
 * @class Forgot_password.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Forgot_password.php
 *
 */

class Forgot_password extends Cit_Controller
{
    /** @var array $output_params contains output parameters */
    public $output_params;

    /** @var array $single_keys contains single array */
    public $single_keys;

    /** @var array $multiple_keys contains multiple array */
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
            "check_email_exists",
            "update_reset_key",
        );
        $this->multiple_keys = array(
            "reset_password_link",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('forgot_password_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_forgot_password($request_arr = array())
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
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "forgot_password");

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
    public function start_forgot_password($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_forgot_password($request_arr);
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

            $input_params = $this->check_email_exists($input_params);

            $condition_res = $this->is_user_exists($input_params);
            if ($condition_res["success"]) {

                $input_params = $this->reset_password_link($input_params);

                $input_params = $this->update_reset_key($input_params);

                $input_params = $this->email_notification($input_params);

                $output_response = $this->users_finish_success_2($input_params);
                return $output_response;
            } else {

                $condition_res = $this->is_archived($input_params);
                if ($condition_res["success"]) {

                    $output_response = $this->users_finish_success_3($input_params);
                    return $output_response;
                } else {

                    $condition_res = $this->check_for_user_inactive($input_params);
                    if ($condition_res["success"]) {

                        $output_response = $this->users_finish_success($input_params);
                        return $output_response;
                    } else {

                        $output_response = $this->users_finish_success_1($input_params);
                        return $output_response;
                    }
                }
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
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
    public function check_email_exists($input_params = array())
    {

        $this->block_result = array();
        try {

            $email = isset($input_params["email"]) ? $input_params["email"] : "";
            $this->block_result = $this->users_model->check_email_exists($email);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["check_email_exists"] = $this->block_result["data"];
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
    public function is_user_exists($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_email_exists"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("User is not exists.");
            }
            $cc_lo_1 = $input_params["u_status"];
            $cc_ro_1 = "Active";

            $cc_fr_1 = ($cc_lo_1 == $cc_ro_1) ? TRUE : FALSE;
            if (!$cc_fr_1) {
                throw new Exception("User Status is not active.");
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
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function reset_password_link($input_params = array())
    {
        if (!method_exists($this, "generateLink")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->generateLink($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["reset_password_link"] = $format_arr;

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
    public function update_reset_key($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["email"])) {
                $where_arr["email"] = $input_params["email"];
            }
            if (isset($input_params["reset_key"])) {
                $params_arr["reset_key"] = $input_params["reset_key"];
            }
            $params_arr["_dtupdatedat"] = "NOW()";
            $this->block_result = $this->users_model->update_reset_key($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_reset_key"] = $this->block_result["data"];
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

            $email_arr["vUserName"] = $input_params["email_username"];
            $email_arr["reset_link"] = $input_params["reset_link"];

            $success = $this->general->sendMail($email_arr, "FRONT_FORGOT_PASSWORD", $input_params);

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
        } catch (Exception $e) {
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

        $func_array["function"]["name"] = "forgot_password";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_archived($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["u_status"];
            $cc_ro_0 = "Archived";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("User is not archived.");
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
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "forgot_password";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(RESOURSE_IS_FORBIDDEN);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_user_inactive($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["u_status"];
            $cc_ro_0 = "Inactive";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("user is not inactive.");
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
     * Used to process finish flow.
     * 
     * @param array $input_params  array to process loop flow.
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

        $func_array["function"]["name"] = "forgot_password";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(RESOURSE_IS_FORBIDDEN);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish API failure flow.
  
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "forgot_password";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(RESOURSE_WAS_NOT_FOUND);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
