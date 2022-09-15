<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Manage Likes Count Controller
 *
 * @category notification
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Manage Likes Count
 *
 * @class Manage_likes_count.php
 *
 * @path application\notifications\user\controllers\Manage_likes_count.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */

class Verify_number extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->multiple_keys  = array(
            "get_active_users",
            "proceed_user_records"
        );
        $this->block_result = array();

        $this->load->library('notifyresponse');
        $this->load->model('verify_number_model');
        $this->load->model("tools/setting_model");
        $this->load->model("users/users_model");
    }

    /**
     * start_manage_likes_count method is used to initiate api execution flow.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 29.07.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $output_response returns output response of API.
     */
    public function start_verify_number($request_arr = array())
    {
        try
        {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = array();

            $input_params12 = $this->get_active_users();

            if(!empty($input_params12) && count($input_params12) > 0){

                $input_params12 = $this->proceed_user_records($input_params12);
    
                $input_params = $this->email_notification($input_params12['proceed_user_records']);
    
                $output_response = $this->finish_success($input_params12);
                return $output_response;
            }
           
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_likes_per_day method is used to process query block.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_active_users($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $this->block_result = $this->users_model->get_active_users();
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["a"] = $this->block_result["data"];

        return $input_params["a"];
    }

    /**
     * proceed_user_records method is used to delete records of user from db.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function proceed_user_records($input_params = array())
    {

        $this->block_result = array();
        try
        {
            
           $this->block_result = $this->users_model->delete_records_frm_connection_tables($input_params);

            foreach ($input_params as $key => $value) 
            {
                if(!empty($value['profile_image']))
                {
                     $data11 = $this->general->deleteAWSFileData($folder_name = "widsconnect/user_profile", $value['profile_image']);
                }

                $img_array = explode(",", $value['personal_images']);

                foreach ($img_array as $key1 => $image_name) {
                   
                    if(!empty($image_name))
                    {
                         $data11 = $this->general->deleteAWSFileData($folder_name = "widsconnect/personal_images", $image_name);
                    }

                }
                
            }

        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["proceed_user_records"] = $this->block_result["data"];

        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * email_notification method is used to process email notification.
     * @created priyanka chillakuru | 12.09.2019
     * @modified priyanka chillakuru | 12.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function email_notification($input_params12 = array())
    {

        $this->block_result = array();
        try
        {
            
            foreach ($input_params12 as $key => $input_params) {
                
                $email_arr["vEmail"] = $input_params["email"];

              //  echo "--send email--". $email_arr["vEmail"]. "---"; continue;

                $email_arr["vUsername"] = $input_params["email_user_name"];

                $success = $this->general->sendMail($email_arr, "DeleteAccount", $input_params);


                $log_arr = array();
                $log_arr['eEntityType'] = 'General';
                $log_arr['vReceiver'] = is_array($email_arr["vEmail"]) ? implode(",", $email_arr["vEmail"]) : $email_arr["vEmail"];
                $log_arr['eNotificationType'] = "EmailNotify";
                $log_arr['vSubject'] = $this->general->getEmailOutput("subject");
                $log_arr['tContent'] = $this->general->getEmailOutput("content");
                if (!$success)
                {
                    $log_arr['tError'] = $this->general->getNotifyErrorOutput();
                }
                $log_arr['dtSendDateTime'] = date('Y-m-d H:i:s');
                $log_arr['eStatus'] = ($success) ? "Executed" : "Failed";
                $this->general->insertExecutedNotify($log_arr);
                if (!$success)
                {
                    throw new Exception("Failure in sending mail.");
                }
                $success = 1;
                $message = "Email notification send successfully.";
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["email_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * finish_success method is used to process finish flow.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "user records deleted successfully.",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        //$func_array["function"]["name"] = "manage_likes_count";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
