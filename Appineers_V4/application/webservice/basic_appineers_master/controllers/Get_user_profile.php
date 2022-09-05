<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of get user profile and reviews Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module get user profile
 *
 * @class Get_user_profile.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Get_user_profile.php
 *
 * @version 4.4
 *
 * @author Suresh Nakate
 *
 * @since 22.09.2021
 */

class Get_user_profile extends Cit_Controller
{
    /* @var array $settings_params contains setting parameters */
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
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "get_user_review_details",
        );
        $this->multiple_keys = array(
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('basic_appineers_master/users_model');

    }

    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request input array.
     *
     * @return array $valid_res validation output response.
     */
    public function rules_get_user_profile($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            
          
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_user_profile");

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
    public function start_get_user_profile($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_get_user_profile($request_arr);
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
          
                $input_params = $this->get_user_details($input_params);

                

                $condition_res = $this->check_user_exists($input_params);

                if ($condition_res["success"]) {

                    $output_response = $this->user_finish_success($input_params);
                    return $output_response;
                }
                else {

                    $output_response = $this->user_finish_success_1($input_params);
                    return $output_response;
                }
                
            
        } catch (Exception $e) {

            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }

        return $output_response;
    }

   
     /**
     * This method is used to get user details.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details($input_params = array())
    {
        $this->block_result = array();
        try {
            
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $this->block_result = $this->users_model->get_user_profile_details( $user_id);

           if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                    $image_arr["path"] =$aws_folder_name."/user_profile/".$user_id;
                   // $data = $this->general->get_image_aws($image_arr);

                   $folder_name = $aws_folder_name."/user_profile/".$user_id;
                
                    $data11 = $this->general->getFileFromAWS('', $folder_name, $data);

                    $data = $data11['@metadata']['effectiveUri'];
                    $result_arr[$data_key]["u_profile_image"] = (false == empty($data)) ? $data:"";

                    //get user images
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
                    }   
                }


                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_details"] = $this->block_result["data"];

        // print_r($input_params); exit;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * check_user_exists method is used to process conditions.

     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_user_exists($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["get_user_details"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("user details not found.");
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
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "user_finish_success",
        );
        $output_fields = array(
            'u_user_id',
            'u_first_name',
            'u_last_name',
            'u_email',
            'u_mobile_no',
            'u_profile_image',
            'user_images',
            'u_dob',
            'u_social_login_type',
            'u_social_login_id',
            'u_address',
            'u_city',
            'u_latitude',
            'u_longitude',
            'u_state_id',
            'u_state_name',
            'u_zip_code',
            'u_push_notify',
            'u_access_token',
            'u_device_type',
            'u_device_model',
            'u_device_os',
            'u_device_token',
            'u_status',
            'u_added_at',
            'u_updated_at',
            'u_email_verified',
            'ms_state',
            'u_terms_conditions_version',
            'u_privacy_policy_version',
        );
        $output_keys = array(
            'get_user_details',
        );
        $ouput_aliases = array(
            "get_updated_details" => "get_user_details",
            "u_user_id" => "user_id",
            "u_first_name" => "first_name",
            "u_last_name" => "last_name",
            "u_email" => "email",
            "u_mobile_no" => "mobile_no",
            "u_profile_image" => "profile_image",
            "user_images" => "user_images",
            "u_dob" => "dob",
            "u_social_login_type" => "social_login_type",
            "u_social_login_id" => "social_login_id",
            "u_address" => "address",
            "u_city" => "city",
            "u_latitude" => "latitude",
            "u_longitude" => "longitude",
            "u_state_id" => "state_id",
            "u_state_name" => "state_name",
            "u_zip_code" => "zip_code",
            "u_push_notify" => "push_notify",
            "u_access_token" => "access_token",
            "u_device_type" => "device_type",
            "u_device_model" => "device_model",
            "u_device_os" => "device_os",
            "u_device_token" => "device_token",
            "u_status" => "status",
            "u_added_at" => "added_at",
            "u_updated_at" => "updated_at",
            "u_email_verified" => "email_verified",
            "ms_state" => "state",
            "u_terms_conditions_version" => "terms_conditions_version",
            "u_privacy_policy_version" => "privacy_policy_version",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_user_profile";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

   
    /**
     * This  method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function user_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "user_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_user_profile";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

}
