<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Get Config Paramaters Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Get Config Paramaters
 *
 * @class Get_config_paramaters.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Get_config_paramaters.php
 *
 * @version 4.4
 *
 * @author Sandeep c
 *
 * @since 06.09.2021
 */

class Get_config_paramaters extends Cit_Controller
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
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->multiple_keys = array(
            "get_config_params",
        );
        $this->block_result = array();
        $this->load->library('lib_log');
        $this->load->library('wsresponse');
        $this->load->model('get_config_paramaters_model');
    }


    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request_arr array is used for api input.
     *
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_config_paramaters($request_arr = array())
    {
        $valid_arr = array();
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_config_paramaters");

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
    public function start_get_config_paramaters($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_get_config_paramaters($request_arr);
            if ($validation_res["success"] == "0") {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            if (isset($input_params["user_id"]) && $input_params["user_id"] > 0)
            {
                $input_params = $this->update_app_version($input_params);
            }
           
            $input_params = $this->get_config_params($input_params);

            $output_response = $this->finish_success($input_params);
            return $output_response;
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }

        return $output_response;
    }

    
    /**
     * This method is used to process query block.

     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_app_version($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            $updateFlag = 0;

            if (isset($input_params["user_id"]))
            {
                $where_arr["user_id"] = $input_params["user_id"];
            }
           
            if (isset($input_params["app_version"]) && !empty($input_params['app_version']) && trim($input_params['app_version']) != trim($input_params["AppVersion"])) {
                $params_arr["app_version"] = $input_params["app_version"];
                $updateFlag = 1;
            }

            if (isset($input_params["device_type"]) && !empty($input_params['device_type']) && trim($input_params['device_type']) != trim($input_params["DeviceType"])) {
                $params_arr["device_type"] = $input_params["device_type"];
                $updateFlag = 1;
            }

            if (isset($input_params["device_model"]) && !empty($input_params['device_model']) && trim($input_params['device_model']) != trim($input_params["DeviceModel"])) {
                $params_arr["device_model"] = $input_params["device_model"];
                $updateFlag = 1;
            }

            if (isset($input_params["device_os"]) && !empty($input_params['device_os']) && trim($input_params['device_os']) != trim($input_params["DeviceOS"])) {
                $params_arr["device_os"] = $input_params["device_os"];
                $updateFlag = 1;
            }

            if (isset($input_params["terms_conditions_version"]) && !empty($input_params['terms_conditions_version']) && trim($input_params['terms_conditions_version']) != trim($input_params["TermsConditionsVersion"])) {
                $params_arr["terms_conditions_version"] = $input_params["terms_conditions_version"];
                $updateFlag = 1;
            }

            if (isset($input_params["privacy_policy_version"]) && !empty($input_params['privacy_policy_version']) && trim($input_params['privacy_policy_version']) != trim($input_params["PrivacyPolicyVersion"])) {
                $params_arr["privacy_policy_version"] = $input_params["privacy_policy_version"];
                $updateFlag = 1;
            }

            $params_arr["_dtupdatedat"] = "NOW()";
            
            if($updateFlag == 1){
                $this->block_result = $this->get_config_paramaters_model->update_app_version($params_arr, $where_arr);
           
                if (!$this->block_result["success"])
                {
                    throw new Exception("updation failed.");
                }
            }
            
            
        }catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_app_version"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process custom function.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function get_config_params($input_params = array())
    {
        if (!method_exists($this, "returnConfigParams")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->returnConfigParams($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["get_config_params"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
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
            "success" => SUCCESS_CODE,
            "message" => "finish_success",
        );
        $output_fields = array(
            'version_update_check',
            'android_version_number',
            'iphone_version_number',
            'version_update_mandatory_ios',
            'version_update_mandatory_android',
            'version_check_message',
            'privacy_policy_version_application',
            'terms_conditions_version_application',
            'privacy_policy_version',
            'terms_conditions_version',
            'log_status_updated',
            'ios_app_id',
            'ios_banner_id',
            'ios_interstitial_id',
            'ios_native_id',
            'ios_rewarded_id',
            'ios_mopub_banner_id',
            'ios_mopub_interstitial_id',
            'android_app_id',
            'android_banner_id',
            'android_interstitial_id',
            'android_native_id',
            'android_rewarded_id',
            'android_mopub_interstitial_id',
            'android_mopub_banner_id',
            'project_debug_level',
            'subscription',
            'mandatory_array',
            'is_first_login',
            'is_mobile_number_verified',
            'address',
            'stripe_public_key',
            'fcm_key',
            'google_places_key',

        );
        $output_keys = array(
            'get_config_params',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_config_paramaters";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
