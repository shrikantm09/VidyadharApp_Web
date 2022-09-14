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
 * @module Post_profile
 *
 * @class Post.php
 *
 * @path application\webservice\user\controllers\Post.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 08.09.2022
 */

class Post extends Cit_Controller
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
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    
        //$this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            //"check_unique_user_email",
            "post_user_details",
            "get_user_details",
        );
        $this->multiple_keys = array(
            "custom_function",
        );
        $this->block_result = array();
        $this->load->library('wsresponse');
        $this->load->library('general');
        $this->load->model('post_model');
    }

    /**
     * rules_post method is used to validate api input params.
     * @created vidyadhar sawant | 08.09.2022
     * @modified vidyadhar sawant | 08.09.2022
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_post($request_arr = array())
    {
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
        $this->wsresponse->setResponseStatus(UNPROCESSABLE_ENTITY);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "post");

        return $valid_res;
    }

    /**
     * start_post method is used to initiate api execution flow.
     *
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request. 
     * 
     * @return array $output_response returns output response of API.
     * 
     */
    public function start_post($request_arr = array(), $inner_api = FALSE)
    {
       
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
            case 'GET':                
                $output_response = $this->get_user_details($request_arr);
                return  $output_response;
                break;
            case 'PUT':                
                $output_response = $this->update_post_details($request_arr);
                return  $output_response;
                break;
            case 'POST':
                if((isset($request_arr['post_id']) && $request_arr['post_id']!='') && (isset($request_arr['post_title']) && $request_arr['post_title']!='')){
                    $output_response = $this->update_post_details($request_arr);
                }
                else if((isset($request_arr['post_id']) && $request_arr['post_id']!='' && $request_arr['post_id']!='all') && (!isset($request_arr['method']))){
                    $output_response = $this->get_user_details($request_arr);
                }else if(isset($request_arr['post_id']) && $request_arr['post_id']=='all'){
                    $output_response = $this->get_user_details_all();
                }else if(isset($request_arr['method']) && $request_arr['method']=='delete'){
                    $output_response = $this->delete_user($request_arr);
                }else{
                    $output_response = $this->add_user($request_arr);
                }
                
                return  $output_response;
                break;
            case 'DELETE':
                $output_response = $this->delete_user($request_arr);
                return  $output_response;
                break;
        }
    }

    public function add_user($request_arr = array(), $inner_api = FALSE)
    {
    
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            
            if (isset($request_arr["post_title"]))
            {
                $params_arr["post_title"] = $request_arr["post_title"];
            }
            if (isset($request_arr["post_description"]))
            {
                $params_arr["post_description"] = $request_arr["post_description"];
            }
            if (isset($request_arr["posted_by"]))
            {
                $params_arr["posted_by"] = $request_arr["posted_by"];
            }
            if (isset($request_arr["post_type"]))
            {
                $params_arr["posted_type"] = $request_arr["post_type"];
            }
            if (!empty($_FILES["post_image"]))
            {
                $params_arr["post_image"] = $_FILES["post_image"];
            }
            
            if (isset($_FILES["post_image"]["name"]) && isset($_FILES["post_image"]["tmp_name"])) {
                for ($i=0; $i <count($_FILES["post_image"]["name"]) ; $i++) { 
                    $sent_file [] = $_FILES["post_image"]["name"][$i];
                }
            } else {
                $sent_file = "";
            }
            if (!empty($sent_file)) {
                for ($j=0; $j <count($sent_file) ; $j++) { 
                
                    list($file_name, $ext) = $this->general->get_file_attributes($sent_file[$j]);
                    $images_arr["post_image"]["ext"][$j] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                    $images_arr["post_image"]["size"][$j] = "2024000";
                    if ($this->general->validateFileFormat($images_arr["post_image"]["ext"][$j], $_FILES["post_image"]["name"][$j])) {
                        if ($this->general->validateFileSize($images_arr["post_image"]["size"][$j], $_FILES["post_image"]["size"][$j])) {
                             $images_arr["post_image"]["name"][$j] = $file_name;
                        }
                    }
                }
            }


            $this->block_result = $this->post_model->create_user($params_arr);
        
            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
                $output_response = $this->users_finish_success_1($params_arr);
            }else{
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $folder_name = $aws_folder_name . "/" . USER_PROFILE . "/POST/" . $this->block_result["data"][0]["insert_id"];
                if (!empty($images_arr)) {
                    for($i=0;$i<count($images_arr["post_image"]["name"]);$i++) {
                        $file_path = $folder_name;
                        $file_name = $images_arr["post_image"]["name"][$i];
                        $file_tmp_path = $_FILES["post_image"]["tmp_name"][$i];
                        $file_tmp_size = $_FILES["post_image"]["size"][$i];
                        $valid_extensions = $images_arr["post_image"]["ext"][$i];
                        $valid_max_size = $images_arr["post_image"]["size"][$i];
                        $upload_arr = $this->general->file_upload($file_path, $file_tmp_path, $file_name, $valid_extensions, $file_tmp_size, $valid_max_size);
                        if ($upload_arr[0] == "") {
                            throw new Exception("Uploading file(s) is failed.");
                        }
                        
                        $post_arr["post_id"] = $this->block_result["data"][0]["insert_id"];
                        $post_arr["post_media"] = $file_name;
                        $arrupdate = $this->post_model->post_image($post_arr);

                        if ($arrupdate) {

                            $output_response = $this->users_finish_success($params_arr);
                            //return $output_response;
                        } else {
                            $output_response = $this->users_finish_success_1($params_arr);
        
                            //return $output_response;
                        }

                    }                    
                }
            }
            $data_arr = $this->block_result["array"];            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["create_post"] = $output_response["settings"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params["create_post"], $this->block_result["data"]);

        return $input_params;
    }
    


    /**
     * get_user_details method is used to process query block.
     * @created vidyadhar sawant | 12.09.2022
     * @modified vidyadhar sawant | 12.09.2022
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details($input_params = array())
    {
        $this->block_result = array();
        try
        {
            //echo "<pre>";print_r($input_params);exit;
            $insert_id = isset($input_params["post_id"]) ? $input_params["post_id"] : "";
            $this->block_result = $this->post_model->get_user_details($insert_id);
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
        $input_params["get_user_details"] = $this->block_result["data"];
        $this->wsresponse->setResponseStatus(OK);
        //$input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_user_details method is used to process query block.
     * @created vidyadhar sawant | 12.09.2022
     * @modified vidyadhar sawant | 12.09.2022
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_all()
    {
        $this->block_result = array();
        try
        {
            //echo "<pre>";print_r($input_params);exit;
            //$insert_id = isset($input_params["post_id"]) ? $input_params["post_id"] : "";
            $this->block_result = $this->post_model->get_user_details_all();
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
        $input_params["get_user_details_all"] = $this->block_result["data"];
        
        //$input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * update_profile method is used to process query block.
     * @created vidyadhar sawant | 18.09.2019
     * @modified vidyadhar sawant | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_post_details($input_params = array())
    {
        //echo "<pre>";print_r($input_params);exit;
        $this->block_result = array();
        try
        {

            $params_arr = $where_arr = array();
            if (isset($input_params["post_id"]))
            {
                $where_arr["post_id"] = $input_params["post_id"];
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
            if (isset($input_params["post_type"]))
            {
                $params_arr["posted_type"] = $input_params["post_type"];
            }

            if (!empty($_FILES["post_image"]))
            {
                $params_arr["post_image"] = $_FILES["post_image"];
            }

            if (isset($_FILES["post_image"]["name"]) && isset($_FILES["post_image"]["tmp_name"])) {
                for ($i=0; $i <count($_FILES["post_image"]["name"]) ; $i++) { 
                    $sent_file [] = $_FILES["post_image"]["name"][$i];
                }
            } else {
                $sent_file = "";
            }
            if (!empty($sent_file)) {
                for ($j=0; $j <count($sent_file) ; $j++) { 
                
                    list($file_name, $ext) = $this->general->get_file_attributes($sent_file[$j]);
                    $images_arr["post_image"]["ext"][$j] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
                    $images_arr["post_image"]["size"][$j] = "2024000";
                    if ($this->general->validateFileFormat($images_arr["post_image"]["ext"][$j], $_FILES["post_image"]["name"][$j])) {
                        if ($this->general->validateFileSize($images_arr["post_image"]["size"][$j], $_FILES["post_image"]["size"][$j])) {
                             $images_arr["post_image"]["name"][$j] = $file_name;
                        }
                    }
                }
            }

            $this->block_result = $this->post_model->update_post($params_arr, $where_arr);
            if (!$this->block_result["success"])
            {
                throw new Exception("updation failed.");
                $output_response = $this->users_finish_success_3($params_arr);
            }else{
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $folder_name = $aws_folder_name . "/" . USER_PROFILE . "/POST/" . $input_params["post_id"];
                if (!empty($images_arr)) {
                    for($i=0;$i<count($images_arr["post_image"]["name"]);$i++) {
                        $file_path = $folder_name;
                        $file_name = $images_arr["post_image"]["name"][$i];
                        $file_tmp_path = $_FILES["post_image"]["tmp_name"][$i];
                        $file_tmp_size = $_FILES["post_image"]["size"][$i];
                        $valid_extensions = $images_arr["post_image"]["ext"][$i];
                        $valid_max_size = $images_arr["post_image"]["size"][$i];
                        $upload_arr = $this->general->file_upload($file_path, $file_tmp_path, $file_name, $valid_extensions, $file_tmp_size, $valid_max_size);
                        if ($upload_arr[0] == "") {
                            throw new Exception("Uploading file(s) is failed.");
                        }
                        
                        $post_arr["post_id"] = $input_params["post_id"];
                        $post_arr["post_media"] = $file_name;
                        $arrupdate = $this->post_model->post_image($post_arr);

                        if ($arrupdate) {

                            $output_response = $this->users_finish_success_2($params_arr);
                            //return $output_response;
                        } else {
                            $output_response = $this->users_finish_success_3($params_arr);
        
                            //return $output_response;
                        }
                    }                    
                }
            }
            $data_arr = $this->block_result["array"];
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_post"] = $output_response["settings"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params["update_post"], $this->block_result["data"]);

        return $input_params;
    }

    public function delete_user($input_params = array())
    {

        $this->block_result = array();
        try {

            $post_id = isset($input_params["post_id"]) ? $input_params["post_id"] : "";
            $this->block_result = $this->post_model->get_post_image($post_id);
           
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
        
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $folder_name = $aws_folder_name . "/" . USER_PROFILE . "/POST/" . $this->block_result["data"][0]["pm_post_id"]."/";
                //$folder_name= "post_image_names/".$input_params["post_id"]."/";
                $insert_arr = array();
                $temp_var   = 0;
                foreach($result_arr as $key=>$value)
                {
                    //$new_file_name=$value['post_images'];
                    if($value['pm_post_media'] != "") {
                        $new_file_name=$value['pm_post_media'];
                    } else {
                        $new_file_name=$value['post_video'];
                    }
                    if(false == empty($new_file_name))
                    {  
                        $file_name = $new_file_name;
                        $res = $this->general->deleteAWSFileData($folder_name,$file_name);
                    }                      
                }
                $result_arr = $this->post_model->delete_images( $post_id );
                if ($result_arr) {

                    $output_response = $this->users_finish_success_4($input_params);
                    $input_params["delete"] = $output_response["settings"];
                    //return $output_response;
                } else {
                    $output_response = $this->users_finish_success_5($input_params);

                    //return $output_response;
                }

                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
            $input_params["delete"] = $this->block_result;
            $this->general->apiLogger($input_params, $e);
        }
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params["delete"]);

        return $input_params;
    }

    /**
     * Used to process finish flow.
     *
     * @param array $input_params  array to process loop flow.
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
            "insert_id"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;
        
        $this->wsresponse->setResponseStatus(CREATED);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * Used to process finish flow.
     *
     * @param array $input_params  array to process loop flow.
     *
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_succes_1($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_succes1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

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
    public function users_finish_success_2($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_succes2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
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
    public function finish_success_3($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_succes3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

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
    public function users_finish_success_4($input_params = array())
    {
        $setting_fields = array(
            "success" => SUCCESS_CODE,
            "message" => "users_finish_success4",
        );
        $output_fields = array(
            "post_id"
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
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
    public function users_finish_success_5($input_params = array())
    {
        $setting_fields = array(
            "success" => FAILED_CODE,
            "message" => "users_finish_succes5",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "post";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(INTERNAL_SERVER_ERROR);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
