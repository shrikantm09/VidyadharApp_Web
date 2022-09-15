<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Social Login Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Social Login
 *
 * @class Social_login.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Social_login.php
 * 
 * @version 4.4
 *
 * @author Suresh Nakate
 *
 * @since 06.09.2021
 *
 */

class Social_login extends Cit_Controller
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

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {

        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "get_user_login_details_v1_v1",
            "update_device_details_v1_v1",
        );
        $this->multiple_keys = array(
            "prepare_where",
            "generate_auth_token",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('social_login_model');
        $this->load->library('lib_log');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     *
     * @return array $valid_res returns output response of API.
     */
    public function rules_social_login($request_arr = array())
    {
        $valid_arr = array(
            "device_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "device_type_required",
                )
            ),
            "device_model" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "device_model_required",
                )
            ),
            "device_os" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "device_os_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "social_login");

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
    public function start_social_login($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_social_login($request_arr);
            if ($validation_res["success"] == "0") {
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }




            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->prepare_where($input_params);

            if ($input_params["status"]) {
                $input_params = $this->generate_auth_token($input_params);
                if (count($input_params['generate_auth_token']) <= 0) {

                    throw new Exception("failed to generate auth token");
                }

                $input_params = $this->get_user_login_details_v1_v1($input_params);

                if ($input_params['get_user_login_details_v1_v1']['success']) {

                    $condition_res = $this->check_login_status($input_params);

                    if ($condition_res["success"]) {

                        $input_params = $this->update_device_details_v1_v1($input_params);

                        if ($input_params['update_device_details_v1_v1']["success"]) {

                            $output_response = $this->users_finish_success_3($input_params);

                            return $output_response;
                        } else {

                            $output_response = $this->users_finish_success_4($input_params);

                            return $output_response;
                        }
                    } else {

                        $condition_res = $this->is_archived($input_params);
                        if ($condition_res["success"]) {

                            $output_response = $this->users_finish_success_1($input_params);

                            return $output_response;
                        } else {

                            $output_response = $this->users_finish_success_2($input_params);

                            return $output_response;
                        }
                    }
                } else {

                    $output_response = $this->users_finish_success($input_params);

                    return $output_response;
                }
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
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function prepare_where($input_params = array())
    {
        if (!method_exists($this, "helperPrepareWhere")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->helperPrepareWhere($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["prepare_where"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

   

    /**
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function generate_auth_token($input_params = array())
    {
        if (!method_exists($this->general, "generateAuthToken")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->generateAuthToken($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["generate_auth_token"] = $format_arr;

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
    public function get_user_login_details_v1_v1($input_params = array())
    {

        $this->block_result = array();
        try {

            $current_timezone = date_default_timezone_get();
            // convert the current timezone to UTC
            date_default_timezone_set('UTC');
            $current_date = date("Y-m-d H:i:s");
            // Again coverting into local timezone
            date_default_timezone_set($current_timezone);

            $auth_token = isset($input_params["auth_token"]) ? $input_params["auth_token"] : "";
            $where_clause = isset($input_params["where_clause"]) ? $input_params["where_clause"] : "";
            $this->block_result = $this->users_model->get_user_login_details_v1_v1($auth_token, $where_clause);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0) {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr) {

                    if(isset($data_arr["eModeratorFlag"]) && $data_arr["eModeratorFlag"] == 1){
                        $subscription[0]["subscription_status"] = 1;
                    }
                    else{

                         //get subscription data
                        $subscribeData = $this->get_subscription_details($data_arr["u_user_id"]);
                        $subscription = array();
                        $subscription_plans = array();
                        foreach ($subscribeData as $key => $value) {
                            if (in_array($value['product_id'], $subscription_plans)) {
                                continue;
                            }

                            $expire_date = $value['dLatestExpiryDate'];

                            unset($value['dLatestExpiryDate']);
                            //latest expire date is greater than current date

                            if (strtotime($expire_date) > strtotime($current_date) || $expire_date == "0000-00-00 00:00:00") {
                                $value['subscription_status'] = 1;
                            } else {
                                $value['subscription_status'] = 0;
                            }

                            $subscription[] = $value;

                            $subscription_plans[] = $value['product_id'];
                        }

                    }

                   
                    $result_arr[$data_key]["subscription"] = $subscription;

                    $data = $data_arr["u_profile_image"];
                    $user_id = $data_arr["u_user_id"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "user_profile";
                    $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                    //$folder_name = $aws_folder_name . "/user_profile";
                   
                   $folder_name = $aws_folder_name."/" . USER_PROFILE . "/".$user_id;
                
                   $data11 = $this->general->getFileFromAWS('', $folder_name, $data);

                   $data = $data11['@metadata']['effectiveUri'];

                    $result_arr[$data_key]["u_profile_image"] = $data;

                      if(false== empty($data_arr["u_user_id"])){
                     $arrImage = $this->users_model->user_images($data_arr);
                   }

                   $arrImage = (false == empty($arrImage["data"])) ? $arrImage["data"] : '';

                    if (is_array($arrImage) && count($arrImage) > 0) {
                      
                        foreach ($arrImage as $img_key => $img_arr) {
                            if(false == empty($img_arr["user_image"])){
                                $data = $img_arr["user_image"];
                                $image_arr = array();
                                $image_arr["image_name"] = $data;
                                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                                $user_id=$data_arr["u_user_id"];
                                $image_arr["color"] = "FFFFFF";
                                $image_arr["no_img"] = FALSE;
                                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                                $folder_name = $aws_folder_name . "/user_profile/" . $user_id;
                                //echo  $folder_name; exit;
                              
                               $data11 = $this->general->getFileFromAWS('', $folder_name, $data);
                               $data = $data11['@metadata']['effectiveUri'];
                               
                                if (false == empty($data)) {
                                    $result_arr[$data_key]["user_images"][$i]["url"] = $data;
                                    $strImageID = $img_arr["image_id"];
                                      $strLocalImageID = $img_arr["local_image_id"];
                                    $result_arr[$data_key]["user_images"][$i]["image_id"] = $strImageID;
                                      $result_arr[$data_key]["user_images"][$i]["local_image_id"] = $strLocalImageID;
                                    $i++;
                                }
                            
                            }
                           
                        }
                    }else
                    {
                     $var1 = array();

                     $result_arr[$data_key]["user_images"]=$var1;

                    }


                    $i++;
                }
                $is_email_id_mandatory = $this->config->item('IS_EMAIL_ID_MANDATORY');
                $is_address_mandatory = $this->config->item('IS_ADDRESS_MANDATORY');
                $is_mobile_no_mandatory = $this->config->item('IS_MOBILE_NO_MANDATORY');
        
                $mandatory_array = array(
                    'is_email_id_mandatory' => $is_email_id_mandatory,
                    'is_address_mandatory' => $is_address_mandatory,
                    'is_mobile_no_mandatory' => $is_mobile_no_mandatory
                );

                $result_arr[$data_key]["mandatory_array"] = array($mandatory_array);
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_login_details_v1_v1"] = $this->block_result["data"];
        $input_params["get_user_login_details_v1_v1"]["success"] = $this->block_result["success"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * This method is used to  get subscription details.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */

    public function get_subscription_details($user_id)
    {
        $arrShareResult = array();
        $arrShareResult = $this->users_model->get_subscription_details($user_id);
        $result_arr =  $arrShareResult["data"];
        $arrShareResult['data'] = $result_arr;

        return $arrShareResult['data'];
    }



    /**
     * This method is used to check device token exists.
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
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function check_login_status($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["u_status"];
            $cc_ro_0 = "Active";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("User is not Active.");
            }
            $success = 1;
            $message = "User is Active.";
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
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function update_device_details_v1_v1($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["u_user_id"])) {
                $where_arr["u_user_id"] = $input_params["u_user_id"];
            }
            if (isset($input_params["device_type"])) {
                $params_arr["device_type"] = $input_params["device_type"];
            }
            if (isset($input_params["device_model"])) {
                $params_arr["device_model"] = $input_params["device_model"];
            }
            if (isset($input_params["device_os"])) {
                $params_arr["device_os"] = $input_params["device_os"];
            }
            if (isset($input_params["device_token"])) {
                $params_arr["device_token"] = $input_params["device_token"];
            }
            $params_arr["_vaccesstoken"] = "'" . $input_params["auth_token"] . "'";
            $this->block_result = $this->users_model->update_device_details_v1_v1($params_arr, $where_arr);

            if (!$this->block_result["success"]) {
                throw new Exception("failed to update device details.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_device_details_v1_v1"] = $this->block_result;
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
    public function users_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success_3",
        );
        $output_fields = array(
            'u_user_id',
            'u_first_name',
            'u_last_name',
            'u_user_name',
            'u_email',
            'u_mobile_no',
            'u_profile_image',
            'u_dob',
            'u_address',
            'u_city',
            'u_latitude',
            'u_longitude',
            'u_state_name',
            'u_zip_code',
            'u_email_verified',
            'u_device_type',
            'u_device_model',
            'u_device_os',
            'u_device_token',
            'u_status',
            'u_added_at',
            'u_updated_at',
            'auth_token1',
            'u_social_login_type',
            'u_social_login_id',
            'u_push_notify',
            'u_terms_conditions_version',
            'u_privacy_policy_version',
            'u_log_status_updated',
            'u_first_login',
            'mandatory_array',
            'subscription',
            'eModeratorFlag',
            'user_images',
            'change_password',
        );
        $output_keys = array(
            'get_user_login_details_v1_v1',
        );
        $ouput_aliases = array(
            "get_user_login_details_v1_v1" => "get_user_details",
            "u_user_id" => "user_id",
            "u_first_name" => "first_name",
            "u_last_name" => "last_name",
            "u_user_name" => "user_name",
            "u_email" => "email",
            "u_mobile_no" => "mobile_no",
            "u_profile_image" => "profile_image",
            "u_dob" => "dob",
            "u_address" => "address",
            "u_city" => "city",
            "u_latitude" => "latitude",
            "u_longitude" => "longitude",
            "u_state_name" => "state_name",
            "u_zip_code" => "zip_code",
            "u_email_verified" => "email_verified",
            "u_device_type" => "device_type",
            "u_device_model" => "device_model",
            "u_device_os" => "device_os",
            "u_device_token" => "device_token",
            "u_status" => "status",
            "u_added_at" => "added_at",
            "u_updated_at" => "updated_at",
            "auth_token1" => "access_token",
            "u_social_login_type" => "social_login_type",
            "u_social_login_id" => "social_login_id",
            "u_push_notify" => "push_notify",
            "u_terms_conditions_version" => "terms_conditions_version",
            "u_privacy_policy_version" => "privacy_policy_version",
            "u_log_status_updated" => "log_status_updated",
            "u_first_login" => "is_first_login",
            "mandatory_array" => "mandatory_array",
            "subscription" => "subscription",
            "eModeratorFlag" => "moderator_flag",
            "user_images" => "user_images",
            "change_password" => "change_password",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * This method is used to API failure flow.
     * 
     * @param array $input_params  array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_4($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

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
                throw new Exception("User Status is not Archived.");
            }
            $success = 1;
            $message = "User is Archived";
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
     * This method is used to process finish flow.
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

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(RESOURSE_IS_FORBIDDEN);

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
    public function users_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(RESOURSE_IS_FORBIDDEN);

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
            "success" => "2",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(UNAUTHORIZED);

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

        $func_array["function"]["name"] = "social_login";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
