<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Change Mobile Number Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Change Mobile Number
 *
 * @class Change_mobile_number.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Change_mobile_number.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 11.12.2019
 */

class Change_mobile_number extends Cit_Controller
{
    public $settings_params;
    
    /* @var array $output_params contains output parameters */
    public $output_params;

    /* @var array $single_keys contains single array */
    public $single_keys;

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
            "check_unique_mobile_number",
            "update_mobile_number",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('change_mobile_number_model');
        $this->load->library('lib_log');
        $this->load->model("basic_appineers_master/users_model");
    }


    /**
     * Used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_change_mobile_number($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            "new_mobile_number" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "new_mobile_number_required",
                ),
                array(
                    "rule" => "number",
                    "value" => TRUE,
                    "message" => "new_mobile_number_number",
                ),
                array(
                    "rule" => "minlength",
                    "value" => 10,
                    "message" => "new_mobile_number_minlength",
                ),
                array(
                    "rule" => "maxlength",
                    "value" => 13,
                    "message" => "new_mobile_number_maxlength",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "change_mobile_number");

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
    public function start_change_mobile_number($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_change_mobile_number($request_arr);
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

            $input_params = $this->check_unique_mobile_number($input_params);

            $condition_res = $this->is_unique($input_params);
            if ($condition_res["success"]) {

                $output_response = $this->users_finish_success($input_params);
                return $output_response;
            } else {

                $input_params = $this->update_mobile_number($input_params);

                $condition_res = $this->check_number_updated($input_params);
                if ($condition_res["success"]) {

                    $output_response = $this->users_finish_success_1($input_params);
                    return $output_response;
                } else {

                    $output_response = $this->users_finish_success_2($input_params);
                    return $output_response;
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
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
    public function check_unique_mobile_number($input_params = array())
    {

        $this->block_result = array();
        try {

            $new_mobile_number = isset($input_params["new_mobile_number"]) ? $input_params["new_mobile_number"] : "";
            $this->block_result = $this->users_model->check_unique_mobile_number($new_mobile_number);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
            $this->general->apiLogger($input_params, $e);
        }
        $input_params["check_unique_mobile_number"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * Used to check the unique user.
     *
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function is_unique($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_unique_mobile_number"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Unique Mobile Number Not Found");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
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
    public function users_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "change_mobile_number";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function update_mobile_number($input_params = array())
    {

        $this->block_result = array();
        try {

            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"])) {
                $where_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["new_mobile_number"])) {
                $params_arr["new_mobile_number"] = $input_params["new_mobile_number"];
            }
            $params_arr["_dtupdatedat"] = "NOW()";
            $this->block_result = $this->users_model->update_mobile_number($params_arr, $where_arr);

            if (!$this->block_result) {
                throw new Exception("Exception Failed");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
            $this->general->apiLogger($input_params, $e);
        }
        $input_params["update_mobile_number"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function check_number_updated($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["affected_rows"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Mobile number not updated");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
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
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "change_mobile_number";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(OK);

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

        $func_array["function"]["name"] = "change_mobile_number";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}