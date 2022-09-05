<?php


/**
 * Description of Reason Management Extended Controller
 * 
 * @module Extended Reason Management
 * 
 * @class Cit_Reason_management.php
 * 
 * @path application\admin\masters\controllers\Cit_Reason_management.php
 * 
 * @author CIT Dev Team
 * 
 * @date 01.10.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Reason_management extends Reason_management {
        public function __construct()
    {
        parent::__construct();
        $this->load->model('cit_api_model');
    }
    public function checkUniqueReason($variable = array()){

        $return_arr='0';
        if(false == empty($variable)){

        foreach ($variable as $key => $value) {

            //print_r($value);
            $this->db->select('iReasonId');
            $this->db->from('reasons');
            $this->db->where_in('iReasonId', $value);
            $arrInterestData=$this->db->get()->result_array();

            if(false == empty($arrInterestData)){
                $return_arr = "1";

                break;
            }
        }

        } 
    return  $return_arr; 
        
    }
    public function showStatusButton($id='',$arr=array())
    {     
            $url = $this->general->getAdminEncodeURL('masters/reason_management/add').'|mode|'.$this->general->getAdminEncodeURL('Update').'|id|'.$this->general->getAdminEncodeURL($arr);
        return '<button type="button" data-id='.$arr.' class="btn btn-success operBut" data-url='.$url.' >Edit</button>';
        
    }

}
