<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Get Template Message Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Get Template Message
 *
 * @class Get_template_message.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Get_template_message.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Get_template_message extends Cit_Controller
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
            "get_template",
        );
        $this->multiple_keys = array(
            "prepare_sms",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('get_template_message_model');
        $this->load->model("basic_appineers_master/sms_template_model");
        $this->load->library('lib_log');
    }

    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request input array.
     *
     * @return array $valid_res validation output response.
     */
    public function rules_get_template_message($request_arr = array())
    {
        $valid_arr = array(
            "template_code" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "template_code_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_template_message");

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
    public function start_get_template_message($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_get_template_message($request_arr);
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

            $input_params = $this->get_template($input_params);

            $input_params = $this->prepare_sms($input_params);

            $output_response = $this->sms_template_finish_success($input_params);

            return $output_response;
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }

        return $output_response;
    }

    /**
     * Used to process query for get template.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_template($input_params = array())
    {
        $this->block_result = array();
        try {
            $template_code = isset($input_params["template_code"]) ? $input_params["template_code"] : "";
            $this->block_result = $this->sms_template_model->get_template($template_code);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_template"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * Used to prepare sms
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function prepare_sms($input_params = array())
    {
        if (!method_exists($this->general, "prepareSMS")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->prepareSMS($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["prepare_sms"] = $format_arr;

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
    public function sms_template_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "sms_template_finish_success",
        );
        $output_fields = array(
            'message',
            'activity',
        );
        $output_keys = array(
            'prepare_sms',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_template_message";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
