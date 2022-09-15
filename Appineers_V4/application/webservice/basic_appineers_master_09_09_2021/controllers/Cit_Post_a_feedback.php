<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Post a Feedback Extended Controller
 *
 * @module Extended Post a Feedback
 *
 * @class Cit_Post_a_feedback.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Cit_Post_a_feedback.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Cit_Post_a_feedback extends Post_a_feedback
{

	/**
	 * To initialize class objects/variables.
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->library('lib_log');
	}

	/**
	 * This method is used to upload images on AWS and in db.
	 *
	 * @param array $input_params input_params array to process query block.
	 *
	 * @param array $where_arr where_arr are used to process where condition(s).
	 */
	public function uploadQueryImages($input_params = array())
	{
		try {
			$return = array();
			$return["success"]	= 0;
			$user_id = $input_params['user_id'];
			$img_name = "image_";
			$query_id = $input_params['query_id'];
			$aws_folder_name = $this->config->item("AWS_FOLDER_NAME");
			$folder_name = $aws_folder_name . "/query_images/" . $query_id . "/";

			$insert_arr = array();
			$temp_var   = 0;
			$upper_limit = 3;
			$insert_flag = 1;
            if ($input_params['images_count'] > 0) {

                $upper_limit = $input_params['images_count'];
                for ($i = 1; $i <= $upper_limit; $i++) {
                    $new_file_name = $img_name . $i;

                    if ($_FILES[$new_file_name]['name'] != '') {
                        $temp_file 		= $_FILES[$new_file_name]['tmp_name'];
                        $image_name 	= $_FILES[$new_file_name]['name'];
                        list($file_name, $extension) 	= $this->general->get_file_attributes($image_name);
                        $res = $this->general->uploadAWSData($temp_file, $folder_name, $file_name);

                        if ($res) {
                            $insert_arr[$temp_var]['iUserQueryId'] = $query_id;
                            $insert_arr[$temp_var]['vQueryImage'] = $file_name;
                            $insert_arr[$temp_var]['dtAddedAt'] = date('Y-m-d H:i:s');
                            $insert_arr[$temp_var]['eStatus'] = "Active";
                            $temp_var++;
                        }
                    }
                }

                if (is_array($insert_arr) && !empty($insert_arr)) {
                    $insert_flag = $this->db->insert_batch("user_query_images", $insert_arr);
                }
            }

			$db_error = $this->db->error();
			if ($db_error['code']) {
				throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
			}

			if ($insert_flag == 0) {
				throw new Exception("Failed to insert images");
			}

			$return["success"]	= 1;
			$this->db->trans_commit();
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$params_arr['db_query'] = $this->db->last_query();
			$this->general->apiLogger($params_arr, $e);
			$return["success"]	= 0;
		}

		return $return;
	}
}
