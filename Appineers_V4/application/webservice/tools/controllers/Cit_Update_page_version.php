<?php

/**
 * Description of Update Page Version Extended Controller
 * 
 * @module Extended Update Page Version
 * 
 * @class Cit_Update_page_version.php
 * 
 * @path application\webservice\tools\controllers\Cit_Update_page_version.php
 * 
 * @author CIT Dev Team
 * 
 * @date 23.12.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Update_page_version extends Update_page_version {
        public function __construct()
{
    parent::__construct();
}
public function pageVersionUpdate($input_params=array()){
    $page_type=$input_params['page_type'];
    $user_id  =$input_params['user_id'];
    $return_arr['success']=0;
    $return_arr['message']='';
    if($page_type=='termsconditions'){
        
        //get termsconditions version
        $this->db->select('vVersion');
        $this->db->from('mod_page_settings');
        $this->db->where('vPageCode','termsconditions');
        $termsconditions_version=$this->db->get()->row_array();
        //end
        if(!empty($termsconditions_version['vVersion']) && $termsconditions_version['vVersion']!=''){
     
            $updatedEntity = "";

            //update user version
            $this->db->set('vTermsConditionsVersion',$termsconditions_version['vVersion']);
            $this->db->set('dtUpdatedAt',date('Y-m-d H:i:s'));

            if (isset($input_params["app_version"]) && !empty($input_params['app_version']) && trim($input_params['app_version']) != trim($input_params["AppVersion"])) {
                $this->db->set("vAppVersion", $input_params["app_version"]);
                $updatedEntity .= 'app_version = "'.$input_params["app_version"].'",';
            }

            if (isset($input_params["device_type"]) && !empty($input_params['device_type']) && trim($input_params['device_type']) != trim($input_params["DeviceType"])) {
                $this->db->set("eDeviceType", $input_params["device_type"]);
                $updatedEntity .= 'device_type = "'.$input_params["device_type"].'",';
            }

            if (isset($input_params["device_model"]) && !empty($input_params['device_model']) && trim($input_params['device_model']) != trim($input_params["DeviceModel"])) {
                $this->db->set("vDeviceModel", $input_params["device_model"]);
                $updatedEntity .= 'device_model = "'.$input_params["device_model"].'",';
            }

            if (isset($input_params["device_os"]) && !empty($input_params['device_os']) && trim($input_params['device_os']) != trim($input_params["DeviceOS"])) {
                $this->db->set("vDeviceOS", $input_params["device_os"]);
                $updatedEntity .= 'device_os = "'.$input_params["device_os"].'",';
            }

            if ( trim($termsconditions_version['vVersion']) != trim($input_params["TermsConditionsVersion"])) {
                $updatedEntity .= 'terms_conditions_version = "'.$termsconditions_version["vVersion"].'",';
            }

            $this->db->where('iUserId',$user_id);
            $this->db->update('users');
            if($this->db->affected_rows()>0){

                if(strlen($updatedEntity) > 2){
                    $insertArr["vEntity"] = $updatedEntity;
                    $insertArr["iUserId"] = $user_id;
                    //$insertArr["dAddedAt"] = "NOW()";
    
                    $this->db->insert("user_metadata",$insertArr);
                }

                $return_arr['success']=1;
                $return_arr['message']='Version update successfully';
                
            }
        }
    }else if($page_type=='privacypolicy'){

        $updatedEntity = "";

        //get privacy policy version
        $this->db->select('vVersion');
        $this->db->from('mod_page_settings');
        $this->db->where('vPageCode','privacypolicy');
        $privacypolicy_version=$this->db->get()->row_array();
        //end
        if(!empty($privacypolicy_version['vVersion']) && $privacypolicy_version['vVersion']!=''){
            //update user version
            $this->db->set('vPrivacyPolicyVersion',$privacypolicy_version['vVersion']);
            $this->db->set('dtUpdatedAt',date('Y-m-d H:i:s'));

           
            if (isset($input_params["app_version"]) && !empty($input_params['app_version']) && trim($input_params['app_version']) != trim($input_params["AppVersion"])) {
                $this->db->set("vAppVersion", $input_params["app_version"]);
                $updatedEntity .= 'app_version = "'.$input_params["app_version"].'",';
            }

            if (isset($input_params["device_type"]) && !empty($input_params['device_type']) && trim($input_params['device_type']) != trim($input_params["DeviceType"])) {
                $this->db->set("eDeviceType", $input_params["device_type"]);
                $updatedEntity .= 'device_type = "'.$input_params["device_type"].'",';
            }

            if (isset($input_params["device_model"]) && !empty($input_params['device_model']) && trim($input_params['device_model']) != trim($input_params["DeviceModel"])) {
                $this->db->set("vDeviceModel", $input_params["device_model"]);
                $updatedEntity .= 'device_model = "'.$input_params["device_model"].'",';
            }

            if (isset($input_params["device_os"]) && !empty($input_params['device_os']) && trim($input_params['device_os']) != trim($input_params["DeviceOS"])) {
                $this->db->set("vDeviceOS", $input_params["device_os"]);
                $updatedEntity .= 'device_os = "'.$input_params["device_os"].'",';
            }

            if ( trim($privacypolicy_version['vVersion']) != trim($input_params["PrivacyPolicyVersion"])) {
                $updatedEntity .= 'privacy_policy_version = "'.$privacypolicy_version["vVersion"].'",';
            }

            $this->db->where('iUserId',$user_id);
            $this->db->update('users');
            if($this->db->affected_rows()>0){

                if(strlen($updatedEntity) > 2){
                    $insertArr["vEntity"] = $updatedEntity;
                    $insertArr["iUserId"] = $user_id;
                    //$insertArr["dAddedAt"] = "NOW()";
    
                    $this->db->insert("user_metadata",$insertArr);
                }

                $return_arr['success']=1;
                $return_arr['message']='Version update successfully';
                
            }
        }
        
    }else if($page_type=='other'){

        $updatedEntity = "";

        //get privacy policy version
        $this->db->select('vVersion');
        $this->db->from('mod_page_settings');
        $this->db->where('vPageCode','privacypolicy');
        $privacypolicy_version=$this->db->get()->row_array();
        //end
        if(!empty($privacypolicy_version['vVersion']) && $privacypolicy_version['vVersion']!=''){
            //update user version
            $this->db->set('vPrivacyPolicyVersion',$privacypolicy_version['vVersion']);
            $this->db->set('dtUpdatedAt',date('Y-m-d H:i:s'));

           
            if (isset($input_params["app_version"]) && !empty($input_params['app_version']) && trim($input_params['app_version']) != trim($input_params["AppVersion"])) {
                $this->db->set("vAppVersion", $input_params["app_version"]);
                $updatedEntity .= 'app_version = "'.$input_params["app_version"].'",';
            }

            if (isset($input_params["device_type"]) && !empty($input_params['device_type']) && trim($input_params['device_type']) != trim($input_params["DeviceType"])) {
                $this->db->set("eDeviceType", $input_params["device_type"]);
                $updatedEntity .= 'device_type = "'.$input_params["device_type"].'",';
            }

            if (isset($input_params["device_model"]) && !empty($input_params['device_model']) && trim($input_params['device_model']) != trim($input_params["DeviceModel"])) {
                $this->db->set("vDeviceModel", $input_params["device_model"]);
                $updatedEntity .= 'device_model = "'.$input_params["device_model"].'",';
            }

            if (isset($input_params["device_os"]) && !empty($input_params['device_os']) && trim($input_params['device_os']) != trim($input_params["DeviceOS"])) {
                $this->db->set("vDeviceOS", $input_params["device_os"]);
                $updatedEntity .= 'device_os = "'.$input_params["device_os"].'",';
            }

            $this->db->where('iUserId',$user_id);
            $this->db->update('users');
            if($this->db->affected_rows()>0){

                if(strlen($updatedEntity) > 2){
                    $insertArr["vEntity"] = $updatedEntity;
                    $insertArr["iUserId"] = $user_id;
                    //$insertArr["dAddedAt"] = "NOW()";
    
                    $this->db->insert("user_metadata",$insertArr);
                }

                $return_arr['success']=1;
                $return_arr['message']='Version update successfully';
                
            }
        }
        
    }
    return $return_arr;
}
}
