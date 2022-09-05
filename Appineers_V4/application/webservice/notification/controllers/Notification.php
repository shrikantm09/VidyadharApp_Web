<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Add Controller
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage controllers
 *
 * @module Notification Add
 *
 * @class Notification.php
 *
 * @path application\webservice\notification\controllers\Notification.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class Notification extends Cit_Controller
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
            "get_all_notification"
        );
        
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('notification_model');
        $this->load->library('lib_log');
    }

    /**
     * Used to initiate api execution flow.
     *
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     *
     * @return array $output_response returns output response of API.
     */
    public function start_notification($request_arr = array(), $inner_api = false)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->getNotifications($request_arr);

                return  $output_response;
                break;
            case 'DELETE':
                $output_response = $this->deleteNotifications($request_arr);

                return  $output_response;
                break;
        }
    }

   /**
     * Used to process notification list query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $output_response returns modfied output_response array.
     */
    public function getNotifications($request_arr = array())
    {
        try {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = $func_array = array();
            $result_params = $this->get_all_notification($request_arr);
            
            $key = "get_all_notification";
            $condition_res = $this->condition($result_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->get_notification_list_finish_success($input_params, $result_params);

                return $output_response;
            } else {
                $output_response = $this->get_notification_list_finish_failure($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * Used to process query block for get notification list.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_all_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->notification_model->get_all_notification($input_params, $this->settings_params);
           
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }

            $this->block_result['data'] = array_map(function (array $arr) {
                $image_arr = array();
                $image_arr["image_name"] = $arr["sender_profile_image"];
                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));

                $image_arr["color"] = "FFFFFF";
                $image_arr["no_img"] = false;
                $image_arr["path"]=  $this->config->item("AWS_FOLDER_NAME") . "/user_profile/". $arr["sender_id"];
                $data_1 = $this->general->get_image_aws($image_arr);
                $arr['sender_profile_image'] = $data_1;

                return $arr;
            }, $this->block_result['data']);

        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["count"] =  $this->block_result["count"];
        $input_params["get_all_notification"] = $this->block_result["data"];
        
        return $input_params;
    }

    /**
     * Used to process delete Notifications query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $output_response returns modfied output_response array.
     */
    public function deleteNotifications($request_arr = array(), $inner_api = false)
    {
        try {
            $output_response = array();

            $input_params = $this->delete_notification($request_arr);
              
            $key = "delete_notification";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->delete_notification_finish_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->delete_notification_finish_failure($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * Used to process delete Notifications query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $output_response returns modfied output_response array.
     */
    public function delete_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $arrResult = array();
           
            $this->block_result = $this->notification_model->delete_notification($input_params);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
            $this->block_result["data"] = $result_arr;
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_notification"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * Condition method is used to process conditions.
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
     * This method used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_notification_list_finish_success($input_params = array(), $result_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "get_notification_finish_success",
            "count" => $result_params["count"]
        );
        $output_fields = array(
            'notification_id',
            'message_id',
            'entity_id',
            'item_id',
            'reserved_item_id',
            'reserved_status',
            'entity_type',
            'notification_message',
            'status',
            'sender_id',
            'sender_first_name',
            'sender_last_name',
            'sender_profile_image',
            'redirection_type',
            'category_id',
            'category_name',
            "block_status",
            "block_by",
            'created_at'
        );
        $output_keys = array(
            'get_all_notification',
        );
        $ouput_aliases = array(
            'notification_id'=> 'notification_id',
            'message_id' => 'message_id',
            'item_id' => 'item_id',
            'reserved_item_id' => 'reserved_item_id',
            'reserved_status' => 'reserved_status',
            'entity_id' => 'entity_id',
            'entity_type' => 'entity_type',
            'notification_message' => 'notification_message',
            'status' => 'status',
            'sender_id' => 'sender_id',
            'sender_first_name' => 'sender_first_name',
            'sender_last_name' => 'sender_last_name',
            'sender_profile_image' => 'sender_profile_image',
            'redirection_type' => 'redirection_type',
            'category_id' => 'category_id',
            'category_name' => 'category_name',
            "block_status" => "block_status",
            "block_by" => "block_by",
            'created_at' => 'created_at'
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $result_params;

        $func_array["function"]["name"] = "notification_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
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
    public function get_notification_list_finish_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "get_notification_list_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = "";

        $func_array["function"]["name"] = "notification_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
    * This method is used to process finish flow.
    *
    * @param array $input_params input_params array to process loop flow.
    * @return array $responce_arr returns responce array of api.
    */
    public function delete_notification_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "delete_notification_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_notification";
        $func_array["function"]["single_keys"] = $this->single_keys;

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
    public function delete_notification_finish_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "delete_notification_finish_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_notification";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
