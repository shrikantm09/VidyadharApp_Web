<?php


/**
 * Description of Forgot Password Phone Extended Controller
 * 
 * @module Extended Forgot Password Phone
 * 
 * @class Cit_Forgot_password_phone.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Forgot_password_phone.php
 * 
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Forgot_password_phone extends Forgot_password_phone
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Used to prepare reset password key .
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function prepareResetPasswordKey($input_params = array())
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
        $return_arr['reset_key'] = $reset_key;

        return $return_arr;
    }

     /**
     * Used to format forgot phone response.
     * 
     * @param array $input_params input_params array to process condition flow.
     * 
     * @return array $block_result returns result of condition block as array.
     */
    public function formatForgotPhoneResponse($input_params = array())
    {
        $return_arr[0]['otp_final'] = $input_params['otp'];
        $return_arr[0]['reset_key_final'] = $input_params['reset_key'];

        return $return_arr;
    }
}
