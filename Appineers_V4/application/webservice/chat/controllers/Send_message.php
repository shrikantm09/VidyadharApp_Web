<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Send Message Controller
 *
 * @category webservice
 *
 * @package chat
 *
 * @subpackage controllers
 *
 * @module Send Message
 *
 * @class Send_message.php
 *
 * @path application\webservice\chat\controllers\Send_message.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 31.07.2019
 */
class Send_message extends Cit_Controller
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
            "if_blocked",
            "check_chat_intiated_or_not",
            "update_message",
            "get_user_details_for_send_notifi",
            "post_notification",
            "get_sender_image",
            "add_message",
          
        );
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('send_message_model');
        $this->load->model("user_block_model");
        $this->load->model("messages_model");
        $this->load->model("notification/notification_model");
       
        $this->load->model("basic_appineers_master/users_model");
        $this->load->library('lib_log');
    }

   
    /**
     * Used to validate api input params.
     *
     * @param array $request_arr request_arr array is used for api input.
     *
     * @return array $valid_res returns output response of API.
     */
    public function rules_send_message($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "user_id_required",
                )
            ),
            "receiver_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "receiver_id_required",
                )
            ),
           "firebase_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "firebase_id_required",
                )
            )
        );
        
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);

        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "send_message");

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
    public function start_send_message($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_send_message($request_arr);

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
            $input_params = $this->if_blocked($input_params);
            $condition_res = $this->is_blocked($input_params);

            if ($condition_res["success"]) {
                $input_params = $this->check_chat_intiated_or_not($input_params);
                
                $condition_res = $this->is_intiated($input_params);

                if ($condition_res["success"]) {
                    $input_params = $this->update_message($input_params);
                } else {
                    $input_params = $this->add_message($input_params);
                }
  
                $input_params = $this->get_user_details_for_send_notifi($input_params);
             
                $input_params = $this->custom_function($input_params);
            
                $input_params = $this->post_notification($input_params);

                $condition_res = $this->check_receiver_device_token($input_params);
               
                if ($condition_res["success"]) {
                    $input_params = $this->push_notification($input_params);
                    $output_response = $this->messages_finish_success_1($input_params);
                   
                    return $output_response;
                } else {
                    $output_response = $this->messages_finish_success($input_params);
                  
                    return $output_response;
                }
            } else {
                $output_response = $this->blocked_user_finish_success($input_params);
             
                return $output_response;
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $message = $e->getMessage();
        }
      
        return $output_response;
    }

    /**
     * Used to process query block to check is blocked .
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function if_blocked($input_params = array())
    {
        $this->block_result = array();
        try {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $this->block_result = $this->user_block_model->if_blocked($user_id, $receiver_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["if_blocked"] = $this->block_result["data"];
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
    public function is_blocked($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params["if_blocked"]) ? 0 : 1);
            $cc_ro_0 = 0;

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
     * Used to process query block to check_chat_intiated_or_not.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function check_chat_intiated_or_not($input_params = array())
    {
        $this->block_result = array();
        try {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";
            $firebase_id = isset($input_params["firebase_id"]) ? $input_params["firebase_id"] : "";
            $this->block_result = $this->messages_model->check_chat_intiated_or_not($user_id, $receiver_id, $firebase_id);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = FAILED_CODE;
            $this->block_result["data"] = array();
        }
        $input_params["check_chat_intiated_or_not"] = $this->block_result["data"];
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
    public function is_intiated($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params["check_chat_intiated_or_not"]) ? 0 : 1);
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
     * update_message method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function update_message($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();

            if (isset($_FILES["upload_doc"]["name"]) && isset($_FILES["upload_doc"]["tmp_name"])) {
                $sent_file2 = $_FILES["upload_doc"]["name"];
            } else {
                $sent_file2 = "";
            }
            if (!empty($sent_file2)) {
                list($file_name, $ext) = $this->general->get_file_attributes($sent_file2);
                $images_arr["upload_doc"]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                $images_arr["upload_doc"]["size"] = "102400";
                if ($this->general->validateFileFormat($images_arr["upload_doc"]["ext"], $_FILES["upload_doc"]["name"])) {
                    if ($this->general->validateFileSize($images_arr["upload_doc"]["size"], $_FILES["upload_doc"]["size"])) {
                        $images_arr["upload_doc"]["name"] = $file_name;

                        $folder_name = $this->config->item("AWS_FOLDER_NAME") . "/chat_uploads";
                
                        $temp_file = $_FILES["upload_doc"]["tmp_name"];
                        $res = $this->general->uploadAWSData($temp_file, $folder_name, $images_arr["upload_doc"]["name"]);
                        if ($upload_arr[0] == "") {
                            //file upload failed
                        }
                    }
                }
            }

            if (isset($images_arr["upload_doc"]["name"])) {
                $params_arr["upload_doc"] = $images_arr["upload_doc"]["name"];
            }
            //*******upload doc *******************
            if (isset($input_params["m_firebase_id"])) {
                $where_arr["m_firebase_id"] = $input_params["m_firebase_id"];
            }

            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["message"])) {
                $params_arr["message"] = $input_params["message"];
            }
            
            $params_arr["_dtmodifieddate"] = "NOW()";

            $this->block_result = $this->messages_model->update_message($params_arr, $where_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     *  Used to process query block for add_message.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function add_message($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            if (isset($_FILES["upload_doc"]["name"]) && isset($_FILES["upload_doc"]["tmp_name"])) {
                $sent_file2 = $_FILES["upload_doc"]["name"];
            } else {
                $sent_file2 = "";
            }
            if (!empty($sent_file2)) {
                list($file_name, $ext) = $this->general->get_file_attributes($sent_file2);
                $images_arr["upload_doc"]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                $images_arr["upload_doc"]["size"] = "102400";
                if ($this->general->validateFileFormat($images_arr["upload_doc"]["ext"], $_FILES["upload_doc"]["name"])) {
                    if ($this->general->validateFileSize($images_arr["upload_doc"]["size"], $_FILES["upload_doc"]["size"])) {
                        $images_arr["upload_doc"]["name"] = $file_name;
                        $folder_name = $this->config->item("AWS_FOLDER_NAME") . "/chat_uploads";
                
                        $temp_file = $_FILES["upload_doc"]["tmp_name"];
                        $res = $this->general->uploadAWSData($temp_file, $folder_name, $images_arr["upload_doc"]["name"]);
                        if ($upload_arr[0] == "") {
                            //file upload failed
                        }
                    }
                }
            }

            if (isset($images_arr["upload_doc"]["name"])) {
                $params_arr["upload_doc"] = $images_arr["upload_doc"]["name"];
            }
            
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }
            if (isset($input_params["firebase_id"])) {
                $params_arr["firebase_id"] = $input_params["firebase_id"];
            }
        
            if (isset($input_params["message"])) {
                $params_arr["message"] = $input_params["message"];
            }
           
            $params_arr["_dtaddeddate"] = "NOW()";
            $params_arr["_dtmodifieddate"] = "NOW()";
            $this->block_result = $this->messages_model->add_message($params_arr);
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = FAILED_CODE;
            $this->block_result["data"] = array();
        }
        $input_params["add_message"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * Method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {
        $this->block_result = array();
        try {
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["receiver_id"]) ? $input_params["receiver_id"] : "";

            $this->block_result = $this->messages_model->get_user_details_for_send_notifi($user_id, $receiver_id);
           
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }

            $this->block_result['data'] = array_map(function (array $arr) {
                $image_arr = array();
                $image_arr["image_name"] = $arr["sender_profile_image"];
                $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));

                $image_arr["color"] = "FFFFFF";
                $image_arr["no_img"] = false;
                $image_arr["path"]=  $this->config->item("AWS_FOLDER_NAME") . "/user_profile/". $arr["s_users_id"];
                $data_1 = $this->general->get_image_aws($image_arr);
                $arr['sender_profile_image'] = $data_1;

                return $arr;
            }, $this->block_result['data']);

        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
    
    /**
     * Used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "PrepareHelperMessage")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->PrepareHelperMessage($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
    * post_notification method is used to process query block.
    *
    * @param array $input_params input_params array to process loop flow.
    *
    * @return array $input_params returns modfied input_params array.
    */
    public function post_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            if (isset($input_params["notification_message"])) {
                $params_arr["notification_message"] = $input_params["notification_message"];
            }
            if (isset($input_params["receiver_id"])) {
                $params_arr["receiver_id"] = $input_params["receiver_id"];
            }

            if (isset($input_params["m_message_id"])) {
                $params_arr["entity_type"] = "Message";
                $params_arr["entity_id"] = $input_params["m_message_id"];
            }

            $params_arr["redirection_type"] = "Message";
            $params_arr["_enotificationtype"] = "Message";
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Unread";
            if (isset($input_params["user_id"])) {
                $params_arr["user_id"] = $input_params["user_id"];
            }

            $this->block_result = $this->notification_model->post_notification($params_arr);
        } catch (Exception $e) {
            $success = FAILED_CODE;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    public function check_notification_exists($params_arr = array())
    {
        if (!method_exists($this, "checkNotificationExists")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->checkNotificationExists($params_arr);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checknotificationexists"] = $format_arr;

        $params_arr = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $params_arr;
    }

    /**
     * check_receiver_device_token method is used to process conditions.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["r_device_token"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            
            $success = SUCCESS_CODE;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = FAILED_CODE;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * push_notification method is used to process mobile push notification.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            
            $device_id = $input_params["r_device_token"];
            $input_params["notification_message"] = $input_params["s_name"] ." - " . $input_params["message"];
            $code = "USER";
            $sound = "default";
            $badge = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "type",
                    "value" => "Message",
                    "send" => "Yes",
                ),
                array(
                    "key" => "receiver_id",
                    "value" => "".$input_params["r_users_id"]."",
                    "send" => "Yes",
                ),
                array(
                    "key" => "sender_id",
                    "value" => "".$input_params["s_users_id"]."",
                    "send" => "Yes",
                ),
                array(
                    "key" => "sender_name",
                    "value" => $input_params["s_name"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_name",
                    "value" => $input_params["s_user_name"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "sender_profile_image",
                    "value" => $input_params["sender_profile_image"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "notification_type",
                    "value" => "Message",
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["notification_message"]."";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            
            if (!$uni_id) {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = SUCCESS_CODE;
            $message = "Push notification send succesfully.";
        } catch (Exception $e) {
            $success = FAILED_CODE;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * This method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "messages_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish API success flow.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "messages_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(OK);

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
    public function blocked_user_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => INVALID_CREDENTIAL,
            "message" => "blocked_user_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
