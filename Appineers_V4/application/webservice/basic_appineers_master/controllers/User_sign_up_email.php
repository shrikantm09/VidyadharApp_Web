<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email.php
 *
 * @path application\webservice\basic_appineers_master\controllers\User_sign_up_email.php
 *
 * @version 4.4
 *
 * @author Suresh Nakate
 *
 * @since 06.09.2021
 */
class User_sign_up_email extends Cit_Controller
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

        $this->output_params = array();
        $this->single_keys = array(
            "create_user",
            "get_user_details",
        );
        $this->multiple_keys = array(
            "format_email_v4",
            "custom_function",
            "email_verification_code",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('user_sign_up_email_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     * 
     * @modified Suresh Nakate | 31.08.2021
     *
     * @param array $request_arr request input array.
     *
     * @return array $valid_res validation output response.
     */
    public function rules_user_sign_up_email($request_arr = array())
    {
         $valid_arr = array(
            "first_name" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "first_name_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => FIRST_NAME_MIN_LENGTH,
                    "message" => "first_name_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => FIRST_NAME_MAX_LENGTH,
                    "message" => "first_name_maxlength",
                ), 
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z ]*$/",
                    "message" => "first_name_alphabets_with_spaces",
                ),

            ),
            "last_name" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "last_name_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => LAST_NAME_MIN_LENGTH,
                    "message" => "last_name_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => LAST_NAME_MAX_LENGTH,
                    "message" => "last_name_maxlength",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z ]*$/",
                    "message" => "last_name_alphabets_with_spaces",
                ),
            ),
            "user_name" => array(
                array(
                    "rule" => "regex",
                    "value" => "/^[0-9a-zA-Z]+$/",
                    "message" => "user_name_alpha_numeric_without_spaces",
                ),
                array(
                    "rule" => "minlength",
                    "value" => USER_NAME_MIN_LENGTH,
                    "message" => "user_name_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => USER_NAME_MAX_LENGTH,
                    "message" => "user_name_maxlength",
                )
            ),
            "email" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "email_required",
                ),
                array(
                    "rule" => "email",
                    "value" => true,
                    "message" => "email_email",
                )
            ),
            "mobile_number" => array(
                array(
                    "rule" => "number",
                    "value" => true,
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
            ),
            "dob" => array(
                array(
                    "rule" => "regex",
                    "value" => "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
                    "message" => "dob_formate",
                )
            ),
            "password" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "password_required",
                ),
                array(
                    "rule" => "minlength",
                    "value" => PASSWORD_MIN_LENGTH,
                    "message" => "password_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => PASSWORD_MAX_LENGTH,
                    "message" => "password_maxlength",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/[a-z]/",
                    "message" => "password_lower_case",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/[A-Z]/",
                    "message" => "password_upper_case",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/[0-9]/",
                    "message" => "password_must_contain_digit",
                )
            ),
            "zipcode" => array(
                array(
                    "rule" => "minlength",
                    "value" => ZIPCODE_MIN_LENGTH,
                    "message" => "zipcode_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => ZIPCODE_MAX_LENGTH,
                    "message" => "zipcode_maxlength",
                )
            ),
            "device_type" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_type_required",
                )
            ),
            "device_model" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_model_required",
                )
            ),
            "device_os" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_os_required",
                )
            )
        );
       $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "user_sign_up_email");

        return $valid_res;
    }

    /**
     * This method is used to initiate api execution flow.
     * 
     * @modified Suresh Nakate | 31.08.2021
     *
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     *
     * @return array $output_response returns output response of API.
     */
    public function start_user_sign_up_email($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_user_sign_up_email($request_arr);

            if ($validation_res["success"] == FAILED_CODE) { //Validation Failed
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->format_email_v4($input_params);

            $input_params = $this->check_unique_user($input_params);
            

            if ($input_params["check_unique_user"]["status"]) {

                $input_params = $this->email_verification_code($input_params);

                $input_params = $this->check_device_token_exists($input_params);

                if ($input_params["check_device_token_exists"]["status"]) {
                    $input_params = $this->remove_device_token($input_params);
                }
                $input_params = $this->create_user($input_params);

                if ($input_params["create_user"]["success"]) {

                    $input_params = $this->email_notification($input_params);
                    $output_response = $this->users_finish_success($input_params);

                    return $output_response;
                } else {
                    $output_response = $this->users_finish_success_1($input_params);

                    return $output_response;
                }
            } else {
                $output_response = $this->finish_success_1($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return $output_response;
    }

    /**
     * This method is used to formate email to lower case.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function format_email_v4($input_params = array())
    {
        if (!method_exists($this->general, "format_email")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->format_email($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["format_email_v4"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to check unique user.
     *
     * @modified Suresh Nakate | 31.08.2021
     * 
     * @param array $input_params  array to process loop flow.
     *
     * @return array $input_params returns modified input_params array.
     */
    public function check_unique_user($input_params = array())
    {
        if (!method_exists($this, "checkUniqueUser")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkUniqueUser($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["check_unique_user"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }


    


    /**
     * This method is used to check unique user.
     * 
     * @created  Suresh Nakate | 31.08.2021 
     * @modified Suresh Nakate | 31.08.2021
     *
     * @param array $input_params  array to process loop flow.
     *
     * @return array $input_params returns modified input_params inside check_device_token_exists array.
     */
    public function check_device_token_exists($input_params = array())
    {

        if (!method_exists($this, "checkDeviceTokenExist")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkDeviceTokenExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["check_device_token_exists"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }



    /**
     * This method is used to remove other device token.
     * 
     * @created  Suresh Nakate | 31.08.2021 
     * @modified Suresh Nakate | 31.08.2021
     * 
     * @param array $input_params  array to process loop flow.
     * 
     * @return array $input_params returns modified input_params array.
     */
    public function remove_device_token($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["device_token"])) {
                $where_arr["device_token"] = $input_params["device_token"];
            }
            if (isset($input_params["device_token"])) {
                $params_arr["device_token"] = $input_params["device_token"];
            }

            $this->block_result = $this->users_model->remove_device_token($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["remove_device_token"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



    /**
     * This method is used to prepare email verification code.
     *
     * @param array $input_params  array to process loop flow.
     *
     * @return array $input_params returns modified input_params array.
     */
    public function email_verification_code($input_params = array())
    {
        if (!method_exists($this->general, "prepareEmailVerificationCode")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->prepareEmailVerificationCode($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["email_verification_code"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * Used to process query block & create_user.
     * 
     * @modified Suresh Nakate | 31.08.2021
     * 
     * @param array $input_params array to process loop flow.
     *
     * @return array $input_params returns modified input_params array.
     */
    public function create_user($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();
            if (isset($_FILES["user_profile"]["name"]) && isset($_FILES["user_profile"]["tmp_name"])) {
                $sent_file = $_FILES["user_profile"]["name"];
            } else {
                $sent_file = "";
            }
            if (!empty($sent_file)) {
                list($file_name, $ext) = $this->general->get_file_attributes($sent_file);
                $images_arr["user_profile"]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                $images_arr["user_profile"]["size"] = "202400";
                if ($this->general->validateFileFormat($images_arr["user_profile"]["ext"], $_FILES["user_profile"]["name"])) {
                    if ($this->general->validateFileSize($images_arr["user_profile"]["size"], $_FILES["user_profile"]["size"])) {
                        $images_arr["user_profile"]["name"] = $file_name;
                    }
                }
            }
            if (!empty($input_params["first_name"])) {
                $params_arr["first_name"] = $input_params["first_name"];
            }
            if (!empty($input_params["last_name"])) {
                $params_arr["last_name"] = $input_params["last_name"];
            }
            if (!empty($input_params["user_name"])) {
                $params_arr["user_name"] = $input_params["user_name"];
            }
            if (!empty($input_params["email"])) {
                $params_arr["email"] = $input_params["email"];
            }
            if (!empty($input_params["mobile_number"])) {
                $params_arr["mobile_number"] = $input_params["mobile_number"];
            }
           /*  if (!empty($images_arr["user_profile"]["name"])) {
                $params_arr["user_profile"] = $images_arr["user_profile"]["name"];
            } */
            if (!empty($input_params["dob"])) {
                $params_arr["dob"] = $input_params["dob"];
            }
            if (!empty($input_params["password"])) {
                $params_arr["password"] = $input_params["password"];
            }
            if (method_exists($this->general, "encryptCustomerPassword")) {
                $params_arr["password"] = $this->general->encryptCustomerPassword($params_arr["password"], $input_params);
            }
            if (!empty($input_params["address"])) {
                $params_arr["address"] = $input_params["address"];
            }
            if (!empty($input_params["city"])) {
                $params_arr["city"] = $input_params["city"];
            }
            if (!empty($input_params["latitude"])) {
                $params_arr["latitude"] = $input_params["latitude"];
            }
            if (!empty($input_params["longitude"])) {
                $params_arr["longitude"] = $input_params["longitude"];
            }
            if (!empty($input_params["state_id"])) {
                $params_arr["state_id"] = $input_params["state_id"];
            }
            if (!empty($input_params["state_name"])) {
                $params_arr["state_name"] = $input_params["state_name"];
            }
            if (!empty($input_params["zipcode"])) {
                $params_arr["zipcode"] = $input_params["zipcode"];
            }
            $params_arr["status"] = "Inactive";
            $params_arr["_dtaddedat"] = "NOW()";
            if (!empty($input_params["device_type"])) {
                $params_arr["device_type"] = $input_params["device_type"];
            }
            if (!empty($input_params["device_model"])) {
                $params_arr["device_model"] = $input_params["device_model"];
            }
            if (!empty($input_params["device_os"])) {
                $params_arr["device_os"] = $input_params["device_os"];
            }
            if (!empty($input_params["device_token"])) {
                $params_arr["device_token"] = $input_params["device_token"];
            }
            $params_arr["_eemailverified"] = "No";
            if (!empty($input_params["email_confirmation_code"])) {
                $params_arr["email_confirmation_code"] = $input_params["email_confirmation_code"];
            }

            if (!empty($input_params["terms_conditions_version"])) {
                $params_arr["_vtermsconditionsversion"] = $input_params["terms_conditions_version"];
            }
            if (!empty($input_params["privacy_policy_version"])) {
                $params_arr["_vprivacypolicyversion"] = $input_params["privacy_policy_version"];
            }
            $this->block_result = $this->users_model->create_user($params_arr);

            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["data"]['0'];

            if (!empty($images_arr["user_profile"]["name"])) {
             
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $folder_name = $aws_folder_name . "/" . USER_PROFILE . "/" . $data_arr['insert_id'];
              
                $file_path = $folder_name;
                $file_name = $images_arr["user_profile"]["name"];
                $file_tmp_path = $_FILES["user_profile"]["tmp_name"];
                $file_tmp_size = $_FILES["user_profile"]["size"];
                $valid_extensions = $images_arr["user_profile"]["ext"];
                $valid_max_size = $images_arr["user_profile"]["size"];
                $upload_arr = $this->general->file_upload($file_path, $file_tmp_path, $file_name, $valid_extensions, $file_tmp_size, $valid_max_size);
                if ($upload_arr[0] == "") {
                    throw new Exception("Uploading file(s) is failed.");
                }
                if (!empty($images_arr["user_profile"]["name"])) {
                    $params_arr["user_profile"] =  $images_arr["user_profile"]["name"];
                }
                if (!empty($data_arr['insert_id'])) {
                    $where_arr["u_user_id"] = $data_arr['insert_id'];
                }
             $arrupdate = $this->users_model->update_image($params_arr, $where_arr);
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["create_user"] = $this->block_result;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



    /**
     * Used to send email notification for signup confirmation.
     *
     * @param array $input_params input_params array to process loop flow
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function email_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $email_arr["vEmail"] = $input_params["email"];

            $email_arr["vUsername"] = $input_params["first_name"] . ' ' . $input_params["last_name"];

            $email_arr["email_confirmation_link"] = $input_params["email_confirmation_link"];

            $success = $this->general->sendMail($email_arr, "SIGNUP_EMAIL_CONFIRMATION", $input_params);

            $log_arr = array();
            $log_arr['eEntityType'] = 'General';
            $log_arr['vReceiver'] = is_array($email_arr["vEmail"]) ?
                implode(",", $email_arr["vEmail"]) : $email_arr["vEmail"];

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

        $func_array["function"]["name"] = "user_sign_up_email";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(CREATED);

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

        $func_array["function"]["name"] = "user_sign_up_email";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

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
    public function finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_sign_up_email";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(CONFLICT);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
