<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Update mobile number Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Update mobile number
 *
 * @class Update_mobile_number.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Update_mobile_number.php
 *
 */
class Mobile_number_verified extends Cit_Controller
{
    /* @var array $output_params contains settings parameters */
    public $settings_params;
    
    /* @var array $output_params contains output parameters */
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
            "otp_generation",
        );
        $this->multiple_keys = array(
            "format_email_v1",
            "custom_function",
            "get_message_api",
            "send_sms_api",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('lib_log');
        $this->load->model('users_model');
    }

    /**
     * Used to validate api input params.
     *      
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_mobile_number_verified($request_arr = array())
    {
        $valid_arr = array(
            "is_mobile_number_verified" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "is_mobile_number_verified_required",
                )
            ),
            
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "mobile_number_verified");

        return $valid_res;
    }

    /**
     * This method used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.  
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_mobile_number_verified($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_mobile_number_verified($request_arr);
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

            $input_params = $this->update_mobile_number_verified($input_params);

            $condition_res = $this->is_updated($input_params);
            if ($condition_res["success"]) {

                $output_response = $this->finish_success_2($input_params);
                return $output_response;
            } else {

                $output_response = $this->finish_success_3($input_params);
                return $output_response;
            }

          
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        return $output_response;
    }

   

    
    /**
     * update_mobile_number method is used to process query block.
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_mobile_number_verified($input_params = array())
    {

        $this->block_result = array();
        try {

            
            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"])) {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["is_mobile_number_verified"])) {
                $params_arr["is_mobile_number_verified"] = $input_params["is_mobile_number_verified"];
            }
          
            $this->block_result = $this->users_model->update_mobile_number_verified($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_mobile_number_verified"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    
    /**
     * is_updated method is used to process conditions.
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_updated($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["update_mobile_number_verified"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("mobile number verified not updated.");
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
    public function finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "Updated Successfully",
        );
        $output_fields = array(
           
        );
       

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "mobile_number_verified";
      
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "mobile_number_verified";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
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
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = array_merge($this->output_params, $output_fields);
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_unique_user";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
