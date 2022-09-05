<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of notification_count Controller
 *
 * @category webservice
 *
 * @package notifications
 *
 * @subpackage controllers
 *
 * @module notification_count
 *
 * @class Notification_count.php
 *
 * @path application\webservice\notifications\controllers\Notification_count.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 16.07.2019
 */

class Notification_count extends Cit_Controller
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
    public function rules_notification_count($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "user_id_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "notification_count");

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
    public function start_notification_count($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_notification_count($request_arr);
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

            $input_params = $this->get_count($input_params);

            $output_response = $this->notification_finish_success($input_params);
            return $output_response;
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * Used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_count($input_params = array())
    {
        $this->block_result = array();
        try {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->notification_model->get_count($user_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = FAILED_CODE;
            $this->block_result["data"] = array();
        }
        $input_params["get_count"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * notification_finish_success method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function notification_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "notification_finish_success",
        );
        $output_fields = array(
            'notification_count',
        );
        $output_keys = array(
            'get_count',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "notification_count";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
