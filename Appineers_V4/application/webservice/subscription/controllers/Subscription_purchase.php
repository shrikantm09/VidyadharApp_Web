<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Subscription Purchase Controller
 *
 * @category webservice
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Subscription Purchase
 *
 * @class Subscription_purchase.php
 *
 * @path application\webservice\master\controllers\Subscription_purchase.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 29.05.2020
 */

class Subscription_purchase extends Cit_Controller
{
    /* @var array $settings_params contains setting parameters */
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
            "subscription_purchase",
            "subscription_purchase_android",
        );
        $this->multiple_keys = array(
            "validate_reciept",
            "get_subscription_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('subscription_purchase_model');
        // $this->load->model("users/users_model");
        $this->load->library('lib_log');
    }

    /**
     * This method is used to validate api input params.
     *
     * @param array $request_arr request input array.
     *
     * @return array $valid_res validation output response.
     */
    public function rules_subscription_purchase($request_arr = array())
    {
        $valid_arr = array(
            "receipt_type" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "receipt_type_required",
                )
            ),
              
        );

        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "subscription_purchase");

        return $valid_res;
    }

    /**
     * Method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_subscription_purchase($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_subscription_purchase($request_arr);
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

            /* $input_params = $this->get_user_subscription_details($input_params);
             print_r($input_params); exit();
             */

            $condition_res = $this->check_receipt_type($input_params);
            if ($condition_res["success"]) {
                $condition_res = $this->check_for_receipt_data($input_params);


                if ($condition_res["success"]) {
                    if (empty($input_params['subscription_purchase_type']) == true) {
                        $output_response = $this->users_finish_success_5($input_params);

                        return $output_response;
                    }

                    $input_params = $this->validate_reciept($input_params);

                    $condition_res = $this->is_validate_receipt($input_params);
                    if ($condition_res["success"]) {
                        $input_params = $this->check_user_transaction_exists($input_params);

                        $condition_res = $this->check_status($input_params);

                        if ($condition_res["success"]) {
                            $input_params = $this->update_subscription_purchase($input_params);

                            $input_params = $this->get_user_subscription_details($input_params);

                            $output_response = $this->users_finish_success($input_params);
                            return $output_response;
                        } else {
                            $input_params = $this->subscription_purchase($input_params);
                        
                            $input_params = $this->get_user_subscription_details($input_params);

                            $output_response = $this->users_finish_success($input_params);
                            return $output_response;
                        }
                    } else {
                        $output_response = $this->users_finish_success_1($input_params);
                        return $output_response;
                    }
                } else {
                    $output_response = $this->finish_success_1($input_params);
                    return $output_response;
                }
            } else {
                $condition_res = $this->check_for_subscription_id($input_params);

              
                if ($condition_res["success"]) {
                    $input_params = $this->get_subscription_details($input_params);

                    $condition_res = $this->is_android_subscription($input_params);

                    if ($condition_res["success"]) {
                        $input_params_res = $this->check_user_android_transaction_exists($input_params);

                        //   print_r($input_params_res);

                        $condition_res12 = $this->check_status($input_params_res);

                        // print_r($input_params); exit();

                        if ($condition_res12["success"]) {
                            $input_params = $this->update_subscription_purchase_android($input_params);
                        } else {
                            $input_params = $this->subscription_purchase_android($input_params);
                        }
                        
                        $input_params = $this->get_user_android_subscription_details($input_params);

                        $output_response = $this->users_finish_success_3($input_params);
                        return $output_response;
                    } else {
                        $output_response = $this->users_finish_success_4($input_params);
                        return $output_response;
                    }
                } else {
                    $output_response = $this->users_finish_success_2($input_params);
                    return $output_response;
                }
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
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
    public function check_user_android_transaction_exists($input_params = array())
    {
        if (!method_exists($this, "check_user_android_transaction_exists")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->check_user_android_transaction_exists($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to check user transaction exists.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function check_user_transaction_exists($input_params = array())
    {
        if (!method_exists($this, "checkUserTransactionExit")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkUserTransactionExit($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to check status.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function check_status($input_params = array())
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
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receipt_type($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["receipt_type"];
            $cc_ro_0 = "ios";

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
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_receipt_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["receipt_data"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? true : false;
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
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function validate_reciept($input_params = array())
    {
        if (!method_exists($this, "validateReceiptCheck")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->validateReceiptCheck($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["validate_reciept"] = $format_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_validate_receipt($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["success"];
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
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_subscription_purchase($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();

            if (isset($input_params["user_id"])) {
                $where_arr["user_id"] = $input_params["user_id"];
            }

            if (isset($input_params["original_transaction_id"])) {
                $where_arr["original_transaction_id"] = $input_params["original_transaction_id"];
            }

            if (isset($input_params["expiry_date"])) {
                $params_arr["expiry_date"] = $input_params["expiry_date"];
            }
            $params_arr["_ereceipttype"] = "ios";
            if (isset($input_params["receipt_data_v1"])) {
                $params_arr["receipt_data_v1"] = $input_params["receipt_data_v1"];
            }

            $params_arr["autorenewal"] = 1;
            
            if (isset($input_params["product_id"])) {
                $params_arr["product_id"] = $input_params["product_id"];
            }

            $this->block_result = $this->subscription_purchase_model->update_subscription_purchase($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function subscription_purchase($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();
          
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            if (isset($input_params["original_transaction_id"])) {
                $params_arr["original_transaction_id"] = $input_params["original_transaction_id"];
            }

            if (isset($input_params["auto_renew_product_id"])) {
                $params_arr["auto_renew_product_id"] = $input_params["auto_renew_product_id"];
            }
            
            if (isset($input_params["expiry_date"])) {
                $params_arr["expiry_date"] = $input_params["expiry_date"];
            }
            $params_arr["_ereceipttype"] = "ios";
            if (isset($input_params["receipt_data_v1"])) {
                $params_arr["receipt_data_v1"] = $input_params["receipt_data_v1"];
            }

            $params_arr["autorenewal"] = 1;
            
            if (isset($input_params["product_id"])) {
                $params_arr["product_id"] = $input_params["product_id"];
            }

            $this->block_result = $this->subscription_purchase_model->subscription_purchase($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_subscription_details($input_params = array())
    {
        $this->block_result = array();
        try {
            if (isset($input_params["user_id"])) {
                $user_id = $input_params["user_id"];
            }

            if (isset($input_params["original_transaction_id"])) {
                $original_transaction_id = $input_params["original_transaction_id"];
            }

            $this->block_result = $this->subscription_purchase_model->get_user_subscription_details($user_id, $original_transaction_id);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0) {
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_subscription_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_android_subscription_details($input_params = array())
    {
        $this->block_result = array();
        try {
            if (isset($input_params["user_id"])) {
                $user_id = $input_params["user_id"];
            }

            if (isset($input_params["purchase_token"])) {
                $purchase_token = $input_params["purchase_token"];
            }

            $this->block_result = $this->subscription_purchase_model->get_user_android_subscription_details($user_id, $purchase_token);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0) {
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_android_subscription_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
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
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success",
        );
        $output_fields = array(
            'subscription',
        );
        $output_keys = array(
            'get_user_subscription_details',
        );


        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["output_keys"] = $output_keys;
        // $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

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
    public function users_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

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
    public function finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_for_subscription_id($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["subscription_id"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? true : false;
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
     * This method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_subscription_details($input_params = array())
    {
        if (!method_exists($this, "subscriptionDetails")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->subscriptionDetails($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["get_subscription_details"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * This method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_android_subscription($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["success_v1"];
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
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function subscription_purchase_android($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();
            
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            //$params_arr["_eonetimetransaction"] = "Yes";
            if (isset($input_params["expiry_date_v1"])) {
                $params_arr["expiry_date_v1"] = $input_params["expiry_date_v1"];
            }
            if (isset($input_params["autoRenewing"])) {
                $params_arr["autoRenewing"] = $input_params["autoRenewing"];
            }
            
            if (isset($input_params["subscription_id"])) {
                $params_arr["subscription_id"] = $input_params["subscription_id"];
            }
            if (isset($input_params["purchase_token"])) {
                $params_arr["purchase_token"] = $input_params["purchase_token"];
            }
            $params_arr["_ereceipttype"] = "android";
            $this->block_result = $this->subscription_purchase_model->subscription_purchase_android($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase_android"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_subscription_purchase_android($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();
            
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            //$params_arr["_eonetimetransaction"] = "Yes";
            if (isset($input_params["expiry_date_v1"])) {
                $params_arr["expiry_date_v1"] = $input_params["expiry_date_v1"];
            }
            if (isset($input_params["autoRenewing"])) {
                $params_arr["autoRenewing"] = $input_params["autoRenewing"];
            }
            
            if (isset($input_params["subscription_id"])) {
                $params_arr["subscription_id"] = $input_params["subscription_id"];
            }
            if (isset($input_params["purchase_token"])) {
                $params_arr["purchase_token"] = $input_params["purchase_token"];
            }
            $params_arr["_ereceipttype"] = "android";
            $this->block_result = $this->subscription_purchase_model->update_subscription_purchase_android($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["subscription_purchase_android"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_3($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success_3",
        );
        
        $output_fields = array(
            'subscription',
        );
        $output_keys = array(
            'get_user_android_subscription_details',
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["output_keys"] = $output_keys;
        // $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

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
    public function users_finish_success_4($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);
        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * This method is used to validate subscription purchase type.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_5($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_success_5",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

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

        $func_array["function"]["name"] = "subscription_purchase";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
