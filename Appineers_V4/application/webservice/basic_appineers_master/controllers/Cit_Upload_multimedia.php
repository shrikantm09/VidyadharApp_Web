<?php

/**
 * Description ofUpload media  Extended Controller
 * 
 * @module Extended Upload media
 * 
 * @class Cit_Upload_multimedia.php
 * 
 * @path application\webservice\user\controllers\Cit_Upload_multimedia.php
 * 
 * @author CIT Dev Team
 * 
 * @date 20.05.2019
 */

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}


class Cit_Upload_multimedia extends Upload_multimedia
{
	 /**
     * To initialize class objects/variables.
     */
	public function __construct()
	{
		parent::__construct();
	}
	  /**
     * Used to upload images.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return image upload  status & message.
     */
	public function uploadMultipleImages($input_params = array())
	{
		//print_r($input_params);exit;

		$user_id = $input_params['user_id'];
		$img_name = "image";
		$video_name = "video";

		if(!isset($input_params["local_media_id"]) || empty($input_params["local_media_id"]) == true){
			$input_params["local_media_id"] = "";
		}

		// $folder="public/upload/post_image_names/".$input_params['insert_post_v1'][0]['insert_id']."/";
		
		$input_params['single_file'] = 'yes';

		$return_arr = array();
		$insert_arr = array();
		$temp_var   = 0;
		$upper_limit = 50;

		$aws_folder_name = $this->config->item("AWS_FOLDER_NAME");

		if (strtolower($input_params['single_file']) == 'yes') {
			$upper_limit = 1;
			$upper_limit_video =1;
		}
		$temp_var   = 0;
		$insert_arr = array();
		try {
		if ((!empty($input_params['image']))&&(!empty($_FILES["image"]["name"]))) {

			for ($i = 1; $i <= $upper_limit; $i++) {
				//$new_file_name = $img_name . $i;

				if (strtolower($input_params['single_file']) == 'yes') {
					$new_file_name = $img_name;
				}

				if ($_FILES[$new_file_name]['name'] != '') {

					list($file_name, $ext) = $this->general->get_file_attributes($_FILES[$new_file_name]['name']);

					$temp_file = $_FILES[$new_file_name]['tmp_name'];

                    if (false == empty($input_params['img_category']) && $input_params['img_category'] == "user_images" && $user_id > 0) {

					
						$folder_name = $aws_folder_name . "/user_profile/" . $user_id;
                        $res = $this->general->uploadAWSData($temp_file, $folder_name, $file_name);

							if ($res) {
								$insert_arr[$temp_var]['vImage'] = $file_name;
								$insert_arr[$temp_var]['dtAddedAt'] = date('Y-m-d H:i:s');
								$insert_arr[$temp_var]['iUserId'] = $user_id;
								//$insert_arr[$temp_var]['eStatus'] = "Active";
								$insert_arr[$temp_var]['vLocalImageId'] = $input_params["local_media_id"];
								$temp_var++;
							} else {
								throw new Exception('images not uploaded');
							}
                    	}

						if (false == empty($input_params['img_category']) && $input_params['img_category'] == "profile_image") {

							$folder_name = $aws_folder_name . "/user_profile/" . $user_id;

							$res = $this->general->uploadAWSData($temp_file, $folder_name, $file_name);
	
								if ($res) {
									$update_arr['vProfileImage'] = $file_name;
									$update_arr['dtUpdatedAt'] = date('Y-m-d H:i:s');
					
								} else {
									throw new Exception('images not uploaded');
								}
							}
				}
			}
		}
		
		if (is_array($update_arr) && !empty($update_arr)) {
			$this->db->where("iUserId",$user_id);
			$this->db->set($update_arr);
			$this->db->update("users", $update_arr);
			$db_error = $this->db->error();
			if ($db_error['code']) {
				throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
			}

			$return["user_image_id"] = "";

		}
		
		if (is_array($insert_arr) && !empty($insert_arr)) {
			$this->db->insert_batch("user_images", $insert_arr);
			$db_error = $this->db->error();
			if ($db_error['code']) {
				throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
			}

			$return["user_image_id"] = $this->db->insert_id();

		}

	} catch (Exception $e) {
	
		$success = 0;
		$message = $e->getMessage();
	}

		$return["success"]	= true;

		return $return;
	}
}
