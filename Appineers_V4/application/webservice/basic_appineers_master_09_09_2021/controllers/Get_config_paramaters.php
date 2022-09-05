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
            if ($validation_res["success"] == "-5") {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

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
            "success" => "1",
            "message" => "finish_success",
        );
        $output_fields = array(
            'version_update_check',
            'android_version_number',
            'iphone_version_number',
            'version_update_optional',
            'version_check_message',
            'privacy_policy_updated',
            'terms_conditions_updated',
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

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
