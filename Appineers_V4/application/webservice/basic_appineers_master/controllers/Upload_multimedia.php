<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Upload Media
 *
 * @category webservice
 *
 * @package check_in
 *
 * @subpackage controllers
 *
 * @module Upload Media
 *
 * @class Upload Media.php
 *
 * @path application\webservice\controllers\Upload Media.php
 *
 */

class Upload_multimedia extends Cit_Controller
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
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
        );
        $this->multiple_keys = array(
            "get_missing_images",
            "upload_multiple_image",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        // $this->load->library('flowplayer');
        $this->load->model('upload_multimedia_model');
        //$this->load->model('restroom_model');
    }

    /**
     * rules_upload_multimedia method is used to validate api input params.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @return array $valid_res returns output response of API.
     */
    public function rules_upload_multimedia($request_arr = array())
    {
        $valid_arr = array(
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            ),
            "img_category" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "img_category_required",
                )
            ),
           
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "upload_multimedia");

        return $valid_res;
    }

    /**
     * start_upload_multimedia method is used to initiate api execution flow.
     * 
     * @param array $request_arr request_arr array is used for api input.
     * 
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * 
     * @return array $output_response returns output response of API.
     */
    public function start_upload_multimedia($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $validation_res = $this->rules_upload_multimedia($request_arr);
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
        
            if (false == empty($input_params['deleted_images']) && $input_params['img_category'] == "user_images") {

                $input_params = $this->get_missing_images($input_params);


               
            }

            if (false == empty($input_params['img_category']) && $input_params['img_category'] == "user_images") {
                
                $input_params = $this->check_user_exit($input_params);

                $condition_res = $this->is_user_found($input_params);

                if ($condition_res["success"] == 0) {

                    $output_response = $this->post_images_finish_success_4($input_params);
                    return $output_response;
                }

            }
                $input_params = $this->get_user_images($input_params);

                $condition_res = $this->is_image_count_over($input_params);

                if ($condition_res["success"])
                {
                    $input_params = $this->upload_multiple_image($input_params);
                    if ($input_params['success']) {
                        $output_response = $this->post_images_finish_success_1($input_params);

                        return $output_response;
                    } else {
                        $output_response = $this->post_images_finish_success_2($input_params);
                        return $output_response;
                    }
                } else {
                    $output_response = $this->post_images_finish_success_3($input_params);
                    return $output_response;
                }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->general->apiLogger($input_params, $e);
        }
        return $output_response;
    }

    
    /**
     * Get review details for requested review.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function check_user_exit($input_params = array())
    {
        $this->block_result = array();
        try {
           
            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";

            $this->block_result = $this->upload_multimedia_model->check_user_exit($user_id);
            
            if (!$this->block_result["success"]) {
                throw new Exception("No records found for this restroom id.");
            }
           
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["check_user_exit"] = $this->block_result["data"];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
    
     /**
     * Used to process conditions & check review is added or not.
     *
     * @param array $input_params input_params array to process condition flow.
     *
     * @return array $block_result returns result of condition block as array.
     */
    public function is_user_found($input_params = array())
    {
        //print_r($input_params);

        $this->block_result = array();
        try {

            $cc_lo_0 = (empty($input_params["check_user_exit"]) ? 0 : 1);
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Invalid User id.");
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
     * get_missing_images method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_missing_images($input_params = array())
    {

        $this->block_result = array();
        try
        {
 
            $this->block_result = $this->upload_multimedia_model->get_missing_image($input_params);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $selected = array();
                $data =array();
                $upper_limit=5;

                $aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
                $folder_name = $aws_folder_name . "/user_profile/" . $input_params['user_id'];
                $insert_arr = array();
                $temp_var   = 0;
              foreach($result_arr as $key=>$value)
                {
                    $new_file_name=$value['user_images'];
                        if(false == empty($new_file_name))
                        {  
                            $file_name = $new_file_name;
                            $res = $this->general->deleteAWSFileData($folder_name,$file_name);
                           
                        }                      
                }
             
                $result_arr = $this->delete_images($input_params);

                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
    
        return $input_params;
    }

     /**
     * This method is used to process query block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_images($input_params = array())
    {

        $this->block_result = array();
        try
        {
            if (empty($input_params['img_category']) == false && $input_params['img_category'] == "profile_image") {
                
                $input_params["get_existing_image_count"] = 0;

                return $input_params;
            }

            $this->block_result = $this->upload_multimedia_model->get_user_images($input_params);

            $imgCount = 0;
            $result_arr = $this->block_result["data"];
            //print_r( $result_arr); exit;
            if (is_array($result_arr) && count($result_arr) > 0) {

                foreach ($result_arr as $key=>$value) {
                    $imgCount = $value["image_count"];
                }
            }

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
        
        $input_params["get_existing_image_count"] = $imgCount;

       // echo "user image count is--".$input_params["get_existing_image_count"];
        //$input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

        /**
     * This method is used to process conditions.
     *
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function is_image_count_over($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["get_existing_image_count"];
            $cc_ro_0 = 5;

            $cc_fr_0 = ($cc_lo_0 < $cc_ro_0) ? TRUE : FALSE;

            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }
  
      /**
     * delete delete_images method is used to process review block.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_images($arrResult = array())
    {
        $this->block_result = array();
        try {
            $input_params = array();

            $this->block_result = $this->upload_multimedia_model->delete_images($arrResult);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];

            $this->block_result["data"] = $result_arr;
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_images"] = $this->block_result["data"];

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }


    /**
     * upload_multiple_image method is used to process custom function.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function upload_multiple_image($input_params = array())
    {
        if (!method_exists($this, "uploadMultipleImages")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->uploadMultipleImages($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["upload_multiple_image"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);

        return $input_params;
    }

    /**
     * post_images_finish_success_1 method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function post_images_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "post_images_finish_success_1",
        );
        
        $output_fields = array();
      
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "upload_multimedia";
      //  $func_array["function"]["name"] = "upload_multiple_image";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * post_images_finish_success_2 method is used to process finish flow.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $responce_arr returns responce array of api.
     */
    public function post_images_finish_success_2($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "post_images_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "upload_multimedia";
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
    public function post_images_finish_success_3($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "post_images_finish_success_3",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "upload_multimedia";
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
    public function post_images_finish_success_4($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "post_images_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "upload_multimedia";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
