<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of notification_read Controller
 *
 * @category webservice
 *
 * @package notifications
 *
 * @subpackage controllers
 *
 * @module notification_read
 *
 * @class Notification_read.php
 *
 * @path application\webservice\notifications\controllers\Notification_read.php
 */

class Notification_read extends Cit_Controller
{
    /** @var array $settings_params contains settings parameters */
    public $settings_params;

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
            "get_count",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('notification_model');
        $this->load->library('lib_log');
    }

    /**
     * Used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_notification_read($request_arr = array())
    {
        $valid_arr = array(
            "notification_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "notification_id_required",
                )
            ),
            "status" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "status_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "notification_read");

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
    public function start_notification_read($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_notification_read($request_arr);
            
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

            $input_params = $this->update_notification_read_data($input_params);
               
            $key = "update_notification_read_data";
            $condition_res = $this->condition($input_params, $key);

            if ($condition_res["success"]) {
                $output_response = $this->finish_notification_read_update_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->finish_notification_read_update_failure($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * Used to process update_notification_read_data query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_notification_read_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            $this->block_result = $this->notification_model->update_notification_read_data($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("update failed.");
            }
            $data_arr = $this->block_result["array"];

        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_notification_read_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * Used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array(), $key)
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params[$key]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = SUCCESS_CODE;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = FAILED_CODE;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * Used to process finish success flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_notification_read_update_success($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "finish_notification_read_update_success",
        );
        $output_fields = array(
            'affected_rows'
        );
        $output_keys = array(
            'update_notification_read_data',
        );

        $ouput_aliases = array(
            "affected_rows" => "affected_rows",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "notification_read_update";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(CREATED);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * used to process finish failure flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_notification_read_update_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_notification_read_update_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "notification_read_update";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
