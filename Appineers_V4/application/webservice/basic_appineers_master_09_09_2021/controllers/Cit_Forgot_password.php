<?php


/**
 * Description of Forgot Password Extended Controller
 * 
 * @module Extended Forgot Password
 * 
 * @class Cit_Forgot_password.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Forgot_password.php
 * 
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Forgot_password extends Forgot_password
{
     /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Used to generate link.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $return_arr return unique user status & message.
     */
    public function generateLink($input = '', $arr = array())
    {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, $characters_length - 1)];
        }
        $time                 = time();
        $date =  date('Y-m-d', $time);
        $reset_key = "$random_string&$time";
        $reset_key = base64_encode($reset_key);
        $return_arr[0]['reset_key'] = $reset_key;
        $reset_password_link = $this->general->generateResetPasswordLink($input_params['email'], $reset_key);
        $return_arr[0]['reset_link'] = $reset_password_link;
        
        return $return_arr;
    }
}
