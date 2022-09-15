<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Add Post_v1 Model
 *
 * @category webservice
 *
 * @package checkin
 *
 * @subpackage models
 *
 * @module upload images
 *
 * @class Upload_multimedia_model.php
 *
 * @path application\webservice\check_in\models\Upload_multimedia_model.php
 *
 * @version 4.3
 *
 * @author CIT Dev Team
 *
 * @since 11.07.2019
 */

class Upload_multimedia_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * get_post_details method is used to execute database queries for Add Post API.
     * 
     * @param string $insert_id insert_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_post_details($insert_id = '')
    {
        try {
            $result_arr = array();
                                
            $this->db->from("missing_pets AS mp");
           
            
            $this->db->select("mp.iMissingPetId  AS missing_pet_id");
        
            if(false == empty($insert_id)){
                $this->db->where("mp.iMissingPetId =", $insert_id);
            }
            $this->db->limit(1);
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

      /**
     * get_missing_image method is used fetch missing images.
     * 
     * @param string $post_id business_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_missing_image($params_arr = '')
    {
       try {
            $result_arr = array();

            if (!empty($params_arr['img_category']) ){

                if (isset($params_arr['deleted_images']) && $params_arr['deleted_images'] != "" && $params_arr['img_category'] == "user_images")
                {
                    if(strpos($params_arr['deleted_images'], ',') !== false){
                        $strWhere = "ui.iImageId IN ('" . str_replace(",", "','", $params_arr['deleted_images']) . "')";            
                    }
                    else{
                        $strWhere = "ui.iImageId=".$params_arr['deleted_images'];
                    }
                    
                    $this->db->from("user_images AS ui");
    
                    $this->db->select("ui.iImageId AS u_user_id");
                    $this->db->select("ui.vImage AS user_images");
                    if (isset($strWhere) && $strWhere != "") {
                        $this->db->where($strWhere);
                    }
                }

            }          

            $result_obj = $this->db->get();

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            /* if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            } */
            $success = 1;
        } catch (Exception $e) {
         
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }


     /**
     * This method is used to execute database queries to get review details.
     * 
     * @param string $insert_id insert_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function check_user_exit($user_id = '')
    {
        try {

            if ($user_id < 0 || $user_id == "") {
                throw new Exception("user id.");
            }

            $result_arr = array();

             $this->db->from("users AS u");
            $this->db->select("u.iUserId AS u_user_id");
        
            if (isset($user_id) && $user_id != "") {
                $this->db->where("u.iUserId =", $user_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();

            // echo $this->db->last_query();

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }

           
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            $success = 1;
            $message = "";
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);

            $success = 0;
            $message = $e->getMessage();
        }

       // print_r($result_arr); exit;
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

      
    /**
     * This method is used fetch user images.
     * 
     * @param string $post_id business_id is used to process query block.
     * 
     * @return array $return_arr returns response of query block.
     */
    public function get_user_images($params_arr = '')
    {
       try {
            $result_arr = array();

            if (!empty($params_arr['user_id']) ){

                    $this->db->from("user_images AS mpi");
                    $this->db->select("count(mpi.iImageId) as image_count");
                    $this->db->where("mpi.iUserId",$params_arr['user_id']);
                    
                

            }else{
                throw new Exception("Invalid user id.");
            }         

            $result_obj = $this->db->get();

            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            /* if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            } */
            $success = 1;
        } catch (Exception $e) {
         
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * This method is used to execute database queries for Edit Profile API.
     * 
     * @param array $params_arr params_arr array to process query block.
     * 
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function delete_images($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            
            if (isset($params_arr['deleted_images']) && $params_arr['deleted_images'] != "")
            {
                if(strpos($params_arr['deleted_images'], ',') !== false){
                    $strWhere = "iImageId IN ('" . str_replace(",", "','", $params_arr['deleted_images']) . "')";            
                }
                else{
                    $strWhere = "iImageId =".$params_arr['deleted_images'];
                }                
            }
            if (isset($strWhere))
            {
                $this->db->where($strWhere);
            }

            if (isset($params_arr["user_id"]))
            {
                $this->db->where("iUserId",$params_arr["user_id"]);
            }

            $this->db->stop_cache();

            $res = $this->db->delete("user_images");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
