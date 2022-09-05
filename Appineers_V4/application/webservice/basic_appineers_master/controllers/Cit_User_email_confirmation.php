<?php


/**
 * Description of User Email Confirmation Extended Controller
 * 
 * @module Extended User Email Confirmation
 * 
 * @class Cit_User_email_confirmation.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_User_email_confirmation.php

 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_User_email_confirmation extends User_email_confirmation
{
    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Used to prepare where condition.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return where condition.
     */

    public function prepareDecodeEmailVerification($input_params = array())
    {
        $confirmation_code = base64_decode($input_params['confirmation_code']);
        $explode_data = explode("&", $confirmation_code);
        $return_arr['email'] = $explode_data[0];

        return $return_arr;
    }
}
