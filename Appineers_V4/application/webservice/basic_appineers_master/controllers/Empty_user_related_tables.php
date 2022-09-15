<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of empty_user_related_table Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module empty_user_related_tables
 *
 * @class empty_user_related_tables.php
 *
 * @path application\webservice\basic_appineers_master\controllers\empty_user_related_tables.php
 *
 * @version 4.4
 *
 */

class Empty_user_related_tables extends Cit_Controller
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
            "delete_user_account",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('truncate_tables_model');
        $this->load->model("basic_appineers_master/users_model");
    }

    /**
     * This method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_empty_user_related_tables($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "delete_account");

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
    public function start_empty_user_related_tables($request_arr = array(), $inner_api = FALSE)
    {

        try {

            $validation_res = $this->rules_empty_user_related_tables($request_arr);
<<<<<<< HEAD
            if ($validation_res["success"] == "0") {
=======
            if ($validation_res["success"] == "-5") {
>>>>>>> 14a6119c4f3f7f8ad041f8c20a9df3fa9d20e46f
                if ($inner_api === TRUE) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }

            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $input_params = $this->truncate_user_related_tables($input_params);

            $condition_res = $this->is_truncated($input_params);

            if ($condition_res["success"]) {

                $output_response = $this->users_finish_success_1($input_params);

                return $output_response;
            } else {

                $output_response = $this->users_finish_success($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function truncate_user_related_tables($input_params = array())
    {

        $this->block_result = array();
        try {

            $this->block_result = $this->truncate_tables_model->truncate_user_related_tables();

            if ($this->block_result['success'] == 0) {
                throw new Exception('Failed to truncate tables.');
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_user_tables"] = $this->block_result["success"];
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
    public function is_truncated($input_params = array())
    {

        $this->block_result = array();
        try {

            $cc_lo_0 = $input_params["delete_user_tables"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Sorry, tables not truncated. please try again.");
            }
            $success = 1;
            $message = "Tables deleted";
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
            "success" => "1",
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "empty_user_related_tables";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

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
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "empty_user_related_tables";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
