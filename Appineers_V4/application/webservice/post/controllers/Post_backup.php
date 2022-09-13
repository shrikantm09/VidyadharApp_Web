<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Controller
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage controllers
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email.php
 *
 * @path application\webservice\user\controllers\User_sign_up_email.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Post extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            //"check_unique_user_email",
            "post_user_details",
            "get_user_details",
        );
        $this->multiple_keys = array(
            //"custom_function",
        );
        $this->block_result = array();
        $this->load->library('wsresponse');
        //$this->load->model('post_model');
        $this->load->model("basic_appineers_master/post_model");
    }

    /**
     * rules_user_sign_up_email method is used to validate api input params.
     * @created priyanka chillakuru | 06.09.2019
     * @modified priyanka chillakuru | 06.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_post($request_arr = array())
    {
//        pr($request_arr );exit;
        $valid_arr = array(
            "post_title" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "post_title_required",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z ]+$/",
                    "message" => "title_alpha_with_spaces",
                )
            ),
            "post_description" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "post_description_required",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z ]+$/",
                    "message" => "post_description_alpha_with_spaces",
                )
            ),
            "posted_by" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "posted_by_required",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z ]+$/",
                    "message" => "posted_by_alpha_with_spaces",
                )
            ),
            "post_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "post_type_required",
                ),
                array(
                    "rule" => "regex",
                    "value" => "/^[a-zA-Z]+$/",
                    "message" => "posted_type_alpha_without_spaces",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "post_profile");

        return $valid_res;
    }

    /**
     * start_user_sign_up_email method is used to initiate api execution flow.
     * @created priyanka chillakuru | 06.09.2019
     * @modified priyanka chillakuru | 06.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_post($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_post($request_arr);
            if ($validation_res["success"] == "0")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
print_r(  $input_params );exit;
            //$input_params = $this->check_unique_user_email($input_params);


            //$condition_res = $this->is_unique_user_exists($input_params);
            $condition_res["success"] = false;
            if ($condition_res["success"])
            {

                $output_response = $this->users_finish_success($input_params);
                return $output_response;
            }

            else
            {

                //$input_params = $this->custom_function($input_params);

                $input_params = $this->post_user_details($input_params);

                //$condition_res = $this->is_user_registered($input_params);
                $condition_res["success"] = true;
                if ($condition_res["success"])
                {

                    $input_params = $this->get_user_details($input_params);

                    //$input_params = $this->email_notification($input_params);

                    //$output_response = $this->users_finish_success($input_params);
                    //return $output_response;
                }

                else
                {

                    //$output_response = $this->users_finish_success_1($input_params);
                    //return $output_response;
                }
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }
    
    /**
     * post_user_details method is used to process query block.
     * @created priyanka chillakuru | 06.09.2019
     * @modified priyanka chillakuru | 06.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_user_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $params_arr = array();
            if (isset($_FILES["post_image"]["name"]) && isset($_FILES["post_image"]["tmp_name"]))
            {
                $sent_file = $_FILES["post_image"]["name"];
            }
            else
            {
                $sent_file = "";
            }
            if (!empty($sent_file))
            {
                list($file_name, $ext) = $this->general->get_file_attributes($sent_file);
                $images_arr["post_image"]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                $images_arr["post_image"]["size"] = "102400";
                if ($this->general->validateFileFormat($images_arr["post_image"]["ext"], $_FILES["post_image"]["name"]))
                {
                    if ($this->general->validateFileSize($images_arr["post_image"]["size"], $_FILES["post_image"]["size"]))
                    {
                        $images_arr["post_image"]["name"] = $file_name;
                    }
                }
            }
            if (isset($input_params["post_title"]))
            {
                $params_arr["post_title"] = $input_params["post_title"];
            }
            if (isset($input_params["post_description"]))
            {
                $params_arr["post_description"] = $input_params["post_description"];
            }
            if (isset($input_params["posted_by"]))
            {
                $params_arr["posted_by"] = $input_params["posted_by"];
            }
            if (isset($input_params["posted_type"]))
            {
                $params_arr["posted_type"] = $input_params["posted_type"];
            }
            
            if (isset($images_arr["post_image"]["name"]))
            {
                $params_arr["post_image"] = $images_arr["post_image"]["name"];
            }
            
            $this->block_result = $this->users_model->create_user($params_arr);
            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];
            $upload_path = $this->config->item("upload_path");
            if (!empty($images_arr["post_image"]["name"]))
            {

                $folder_name = $this->general->getImageNestedFolders("post_image");
                $file_path = $upload_path.$folder_name.DS;
                $this->general->createUploadFolderIfNotExists($folder_name);
                $file_name = $images_arr["post_image"]["name"];
                $file_tmp_path = $_FILES["post_image"]["tmp_name"];
                $file_tmp_size = $_FILES["post_image"]["size"];
                $valid_extensions = $images_arr["post_image"]["ext"];
                $valid_max_size = $images_arr["post_image"]["size"];
                $upload_arr = $this->general->file_upload($file_path, $file_tmp_path, $file_name, $valid_extensions, $file_tmp_size, $valid_max_size);
                if ($upload_arr[0] == "")
                {
                    //file upload failed

                }
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_user_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_user_details method is used to process query block.
     * @created priyanka chillakuru | 06.09.2019
     * @modified priyanka chillakuru | 06.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $insert_id = isset($input_params["insert_id"]) ? $input_params["insert_id"] : "";
            $this->block_result = $this->users_model->get_user_details($insert_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = $this->general->getImageNestedFolders("post_image");
                    $data = $this->general->get_image($image_arr);

                    $result_arr[$data_key]["u_profile_image"] = $data;

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    


}
