<?php
  

/**
 * Description of check_subscription_status_v1 Extended Controller
 * 
 * @module Extended check_subscription_status_v1
 * 
 * @class Cit_Check_subscription_status_v1.php
 * 
 * @path application\notification\master\controllers\Cit_Check_subscription_status_v1.php
 * 
 * @author CIT Dev Team
 * 
 * @date 27.04.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Error_log_report_minutes extends Error_log_report_minutes {
    
    public function __construct()
    {
        parent::__construct();
    }

}
