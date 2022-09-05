<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User_block Controller
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage controllers
 *
 * @module User_block Add
 *
 * @class User_block.php
 *
 * @path application\webservice\chat\controllers\User_block.php
 *
 */
class User_block extends Cit_Controller
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

        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('user_block_model');
        $this->load->model('users/users_model');
        $this->load->library('lib_log');
    }

    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request input array.
     *
     * @return array $valid_res validation output response.
     */
    public function rules_user_block($request_arr = array())
    {
        $valid_arr = array(
            "block_user_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "block_user_id_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "user_block");

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
    public function start_user_block($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_user_block($request_arr);

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

            $input_params = $this->custom_function($input_params);

            $condition_res = $this->is_blocked_user($input_params);
            if ($condition_res["success"]) {
                $input_params['user_block_status'] = 'block';
                $input_params = $this->insert_user_block_data($input_params);
            } else {
                if ($input_params['user_block_status'] == 'block') {
                    $input_params['user_block_status'] = 'unblock';
                } else {
                    $input_params['user_block_status'] = 'block';
                }
                $input_params = $this->update_user_block_data($input_params);
            }
            $key = "update_user_block_data";
            $condition_res = $this->condition($input_params, $key);

            if ($condition_res["success"]) {
                $input_params['other_user_id'] = $input_params['block_user_id'];
                $input_params = $this->get_user_profile_details($input_params);

                $output_response = $this->finish_user_block_update_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->finish_user_block_update_failure($input_params);
                return $output_response;
            }
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
    public function get_user_profile_details($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->users_model->get_user_profile_details($input_params);
           
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }

            $this->block_result['data'] = array_map(function (array $arr) {
                $image_arr = array();
                $image_arr["image_name"] = $arr["u_profile_image"];
                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));

                $image_arr["color"] = "FFFFFF";
                $image_arr["no_img"] = false;
                $image_arr["path"]=  $this->config->item("AWS_FOLDER_NAME") . "/user_profile/". $arr["u_user_id"];
                $data_1 = $this->general->get_image_aws($image_arr);
                $arr['u_profile_image'] = $data_1;

                return $arr;
            }, $this->block_result['data']);

        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_profile_details"] = $this->block_result['data'];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_user_profile_details"]);

        return $input_params;
    }

    /**
     * Method is used to process custom function.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "userBlockedOrNot")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->userBlockedOrNot($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * Used to process query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function insert_user_block_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            $this->block_result = $this->user_block_model->insert_user_block_data($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_user_block_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * Used to process query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function update_user_block_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            $this->block_result = $this->user_block_model->update_user_block_data($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_user_block_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * Method is used to process conditions.
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
     * Used to process conditions.
     *
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function is_blocked_user($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["status"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
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
     * Used to process finish sucess flow.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_user_block_update_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "finish_user_". $input_params['user_block_status'] ."_update_success",
        );

        $output_fields = array(
            "u_user_id" ,
            "u_first_name",
            "u_last_name",
            "u_profile_image",
            "u_address",
            "u_city",
            "u_state_name",
            "u_zip_code",
            "u_latitude",
            "u_longitude",
            "block_status"
        );
        $output_keys = array(
            'get_user_profile_details',
        );
        $ouput_aliases = array(
            "u_user_id" => "u_user_id" ,
            "u_first_name" => "u_first_name",
            "u_last_name" => "u_last_name",
            "u_profile_image" => "u_profile_image",
            "u_address" => "u_address",
            "u_city" => "u_city",
            "u_state_name" => "u_state_name",
            "u_zip_code" => "u_zip_code",
            "u_latitude" => "u_latitude",
            "u_longitude" => "u_longitude",
            "block_status" => "block_status"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_block_update";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(201);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish API failure flow.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_user_block_update_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "finish_user_block_update_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_block_update";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(0);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
