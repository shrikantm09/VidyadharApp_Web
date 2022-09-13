<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Model
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage models
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email_model.php
 *
 * @path application\webservice\user\models\User_sign_up_email_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Post_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->load->library('lib_log');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    
    /**
     * Used to execute database queries for create User using email.
     *
     * @param array $params_arr params_arr array to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function create_user($params_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["post_title"])) {
                $this->db->set("post_title", $params_arr["post_title"]);
            }
            if (isset($params_arr["post_description"])) {
                $this->db->set("post_description", $params_arr["post_description"]);
            }
            if (isset($params_arr["posted_by"])) {
                $this->db->set("posted_by", $params_arr["posted_by"]);
            }
            if (isset($params_arr["posted_type"])) {
                $this->db->set("posted_type", $params_arr["posted_type"]);
            }
            
            $this->db->insert("post_profile");
            $insert_id = $this->db->insert_id();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * Used to execute database queries for create User using email.
     *
     * @param array $params_arr params_arr array to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function post_image($params_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["post_id"])) {
                $this->db->set("post_id", $params_arr["post_id"]);
            }
            if (isset($params_arr["post_media"])) {
                $this->db->set("post_image", $params_arr["post_media"]);
            }
            
            
            $this->db->insert("post_media");
            $insert_id = $this->db->insert_id();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * Get user details for Sign Up Email API.
     *
     * @param string $insert_id used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();
            $result_media_arr["post_media"] = array();
            $params_arr = array();
            $params_arr['db_query'] = $insert_id;

            $this->db->from("post_profile AS pp");
            $this->db->select("pp.post_id AS pp_post_id");
            $this->db->select("pp.post_title AS pp_post_title");
            $this->db->select("pp.post_description AS pp_post_description");
            $this->db->select("pp.posted_by AS pp_posted_by");
            $this->db->select("pp.posted_type AS pp_posted_type");
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("pp.post_id =", $insert_id);
            }
            if (isset($insert_id) && $insert_id != "") {
                $this->db->limit(1);
            }            

            $result_obj = $this->db->get();
            if(!empty($result_obj->result_array())) {
                $result = $result_obj->result_array();
                foreach($result as $row) {
                    $this->db->from("post_media AS pm");
                    $this->db->select("pm.media_id AS pm_media_id");
                    $this->db->select("pm.post_id AS pm_post_id");
                    $this->db->select("pm.post_image AS pm_post_media");                    
                    $this->db->where("pm.post_id =", $row["pp_post_id"]);
                    $result_obj1 = $this->db->get();
                    if(!empty($result_obj1->result_array())) {
                        $result_media = $result_obj1->result_array();
                        foreach ($result_media as $row_media) {
                            $img_arr = array();
                            $img_arr["image_id"] =   $row_media["pm_media_id"];
                            $img_arr["image_name"] =   $row_media["pm_post_media"];
                            array_push($result_media_arr["post_media"],$img_arr);                            
                        }
                        $row["post_media"] = $result_media_arr["post_media"];
                        $result = $row;
                    }
                }
            }
           
            $result_arr = is_object((object)$result) ? $result : array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * Get user details for Sign Up Email API.
     *
     * @param string $insert_id used to process query block.
     *
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_all()
    {
        try {
            $message = "";
            $result_arr = array();
            $params_arr = array();

            $this->db->from("post_profile AS pp");
            $this->db->select("pp.post_id AS pp_post_id");
            $this->db->select("pp.post_title AS pp_post_title");
            $this->db->select("pp.post_description AS pp_post_description");
            $this->db->select("pp.posted_by AS pp_posted_by");
            $this->db->select("pp.posted_type AS pp_posted_type");  

            $result_obj = $this->db->get();
            
            if(!empty($result_obj->result_array())) {
                $result = $result_obj->result_array();
                foreach($result as $key => $row) {
                    $result_media_arr["post_media"] = array();
                    $this->db->from("post_media AS pm");
                    $this->db->select("pm.media_id AS pm_media_id");
                    $this->db->select("pm.post_id AS pm_post_id");
                    $this->db->select("pm.post_image AS pm_post_media");                    
                    $this->db->where("pm.post_id =", $row["pp_post_id"]);
                    $result_obj1 = $this->db->get();
                    if(!empty($result_obj1->result_array())) {
                        $result_media = $result_obj1->result_array();
                        $row_media_id = '';
                        foreach ($result_media as $row_media) {
                            $img_arr = array();
                            $row_media_id = $row_media["pm_post_id"];
                            $img_arr["image_id"] =   $row_media["pm_media_id"];
                            $img_arr["image_name"] =   $row_media["pm_post_media"];
                            array_push($result_media_arr["post_media"],$img_arr);                 
                        }
                        if($row["pp_post_id"]==$row_media_id) {
                            $row["post_media"] = $result_media_arr["post_media"];
                            $result[$key] = $row;
                        }                        
                    }
                }
            }
           
            $result_arr = is_object((object)$result) ? $result : array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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

    public function get_post_image($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();
            $params_arr = array();
            $params_arr['db_query'] = $insert_id;

            $this->db->from("post_media AS pm");
            $this->db->select("pm.media_id AS pm_media_id");
            $this->db->select("pm.post_id AS pm_post_id");
            $this->db->select("pm.post_image AS pm_post_media");       
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("pm.post_id", $insert_id);
            }  

            $result_obj = $this->db->get();
            //echo "<pre>";print_r($result_obj->result_array());exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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

    public function delete_images($insert_id = '')
    {
        try {
            $message = "";
            $result_arr = array();
            $params_arr = array();
            $params_arr['db_query'] = $insert_id;

            $result_obj = $this->db->delete('post_media', array('post_id' => $insert_id));
            if($result_obj) {
                $result_obj1 = $this->db->delete('post_profile', array('post_id' => $insert_id));

                if($result_obj1) {
                    $result_arr = is_object($result_obj1) ? $result_obj1->result_array() : array();
                }
            }
            
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
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
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_post($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->start_cache();
            if (isset($where_arr["post_id"]) && $where_arr["post_id"] != "") {
                $this->db->where("post_id =", $where_arr["post_id"]);
            }
            $this->db->stop_cache();
            if (isset($params_arr["post_title"])) {
                $this->db->set("post_title", $params_arr["post_title"]);
            }
            if (isset($params_arr["post_description"])) {
                $this->db->set("post_description", $params_arr["post_description"]);
            }           

            if (isset($params_arr["posted_by"])) {
                $this->db->set("posted_by", $params_arr["posted_by"]);
            }
            if (isset($params_arr["post_type"])) {
                $this->db->set("posted_type", $params_arr["post_type"]);
            }
            
            $res = $this->db->update("post_profile");
            $affected_rows = $this->db->affected_rows();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    public function delete_user_media_image($where_arr = array())
    {
        try {
            $result_arr = array();
            $affected_rows = 0;

            if (isset($where_arr["post_id"]) && $where_arr["post_id"] != "") {
                $this->db->where("post_id =", $where_arr["post_id"]);

                if (isset($where_arr["media_id"])) {
                    $this->db->where_in("media_id", $where_arr["media_id"]);

                    $res = $this->db->delete("post_media");

                    //echo $this->db->last_query();
                    //exit;
                    $db_error = $this->db->error();
                    if ($db_error['code']) {
                        throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                    }
                    $affected_rows = $this->db->affected_rows();
                    if (!$res || $affected_rows == -1) {
                        throw new Exception("Failure in updation.");
                    }
                }
            }


            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            $message = "";
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        //print_r($return_arr);exit;
        return $return_arr;
    }


    /**
     * This method is used to execute database queries for Edit Profile API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function update_profile($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->start_cache();
            if (isset($where_arr["post_id"]) && $where_arr["post_id"] != "") {
                $this->db->where("post_id =", $where_arr["post_id"]);
            }
            //$this->db->where_in("eStatus", array('Active'));
            $this->db->stop_cache();
            if (isset($params_arr["post_title"])) {
                $this->db->set("post_title", $params_arr["post_title"]);
            }
            if (isset($params_arr["post_description"])) {
                $this->db->set("post_description", $params_arr["post_description"]);
            }
            if (isset($params_arr["posted_by"]) && !empty($params_arr["posted_by"])) {
                $this->db->set("posted_by", $params_arr["posted_by"]);
            }

            if (isset($params_arr["posted_type"]) && !empty($params_arr["posted_type"])) {
                $this->db->set("posted_type", $params_arr["posted_type"]);
            }

            $res = $this->db->update("post_profile");
            $affected_rows = $this->db->affected_rows();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
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
     * @param string $user_id user_id is used to process query block.

     * @return array $return_arr returns response of query block.
     */
    public function get_updated_details($user_id = '')
    {
        try {
            $message = "";
            $result_arr = array();

            $this->db->from("post_profile AS pp");
          

            $this->db->select("pp.post_id AS pp_post_id");
            $this->db->select("pp.post_title AS pp_post_title");
            $this->db->select("pp.post_description AS pp_post_description");
            $this->db->select("pp.posted_by AS pp_posted_by");
            $this->db->select("pp.posted_type AS pp_posted_type");
            
            if (isset($user_id) && $user_id != "") {
                $this->db->where("pp.post_id =", $user_id);
            }

            $this->db->limit(1);

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }

            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
        }

        $this->db->_reset_all();
        
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }


    /**
     * This method is used to execute database queries for Delete Account API.
     *
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * 
     * @return array $return_arr returns response of query block.
     */
    public function delete_user_account($params_arr = array(), $where_arr = array())
    {
        try {
            $message = "";
            $result_arr = array();
            if (isset($where_arr["post_id"]) && $where_arr["post_id"] != "") {
                $this->db->where("post_id =", $where_arr["post_id"]);
            }

            
            $res = $this->db->delete("post_profile");
            $affected_rows = $this->db->affected_rows();

            $db_error = $this->db->error();
            if ($db_error['code']) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }

            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
            //delete_user_media_image
        } catch (Exception $e) {
            $params_arr['db_query'] = $this->db->last_query();
            $this->general->apiLogger($params_arr, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }
}
