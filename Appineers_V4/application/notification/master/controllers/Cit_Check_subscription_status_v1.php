<?php


/**
 * Description of check_subscription_status_v1 Extended Controller
 * 
 * @module Extended check_subscription_status_v1
 * 
 * @class Cit_Check_subscription_status_v1.php
 * 
 * @path application\Notification\master\controllers\Cit_Check_subscription_status_v1.php
 * 
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cit_Check_subscription_status_v1 extends Check_subscription_status_v1
{
     /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();

        $current_timezone = date_default_timezone_get();
        // convert the current timezone to UTC
        date_default_timezone_set('UTC');
        $current_date = date("Y-m-d H:i:s");
        // Again coverting into local timezone
        date_default_timezone_set($current_timezone);
    }

     /**
     * Used to check subscription.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $return_arr return unique user status & message.
     */
    public function checkSubscription($input_params = array())
    {
        $return_arr = array();
        $return_arr['success'] = '0';

        if (!empty($input_params['fetch_the_subscribed_users'])) {
            foreach ($input_params['fetch_the_subscribed_users'] as $data) {
                if ($data['u_receipt_type'] == 'ios') {

                    $upload_url = $this->config->item('upload_url'); // upload url
                    $expiry_date  = $data['u_expiry_date'];
                    // fetch the current timezone

                    //if(strtotime($current_date) > strtotime($expiry_date)) {
                    $sample_json           = $data['u_receipt_data'];
                    $applesharedsecret     = $this->config->item("SUBSCRIPTION_PASSWORD");
                    //$appleurl              = $this->config->item("SUBSCRIPTION_ITUNES_URL");
                    $appleurl = "https://buy.itunes.apple.com/verifyReceipt";
                    $request = json_encode(array("receipt-data" => $sample_json, "password" => $applesharedsecret, "exclude-old-transaction" => true));
                    // setting up the curl

                    $ch = curl_init($appleurl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    $jsonresult = curl_exec($ch);


                    $err = curl_error($ch);
                    curl_close($ch);
                    $decoded_json = json_decode($jsonresult);
                


                    if ($decoded_json->status == "21007") {

                        $appleurl = "https://sandbox.itunes.apple.com/verifyReceipt";
                        $request = json_encode(array("receipt-data" => $sample_json, "password" => $applesharedsecret, "exclude-old-transaction" => true));
                        // setting up the curl

                        $ch = curl_init($appleurl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                        $jsonresult = curl_exec($ch);


                        $err = curl_error($ch);
                        curl_close($ch);
                        $decoded_json = json_decode($jsonresult);
                      
                    }

                    if (!empty($decoded_json->latest_receipt_info) && $decoded_json->status == '0') {
                        $expiry_date_curr = "";
                        $original_transaction_id = "";
                        $product_id = "";
                        $new_receipt_data = $decoded_json->latest_receipt;
                        $issubscribe = 0;


                        $expires_date = array();
                        foreach ($decoded_json->latest_receipt_info as $key => $row) {

                            // not null expire date and orgiginal transaction is equal to user original transaction id
                            if (false == empty($row->expires_date) && $row->original_transaction_id == $data['u_transaction_id']) {

                                $expires_date[$key] = $row->expires_date;
                            } else {
                                unset($decoded_json->latest_receipt_info[$key]);
                            }
                        }
                        //sort array descending order on expire Date
                        array_multisort($expires_date, SORT_DESC, $decoded_json->latest_receipt_info);

                        $gmt_date       = $decoded_json->latest_receipt_info[0]->expires_date;
                        //divide date and time
                        $date1 = explode(' ', $gmt_date);
                        $expiry_date_temp = $date1[0] . " " . $date1[1];

                        //latest expire date is greater than current date
                        if (strtotime($expiry_date_temp) > strtotime($current_date)) {

                            $original_transaction_id = $decoded_json->latest_receipt_info[0]->original_transaction_id;

                            $gmt_date       = $decoded_json->latest_receipt_info[0]->expires_date;
                            $date1 = explode(' ', $gmt_date);
                            $expiry_date_curr = $date1[0] . " " . $date1[1];
                            $product_id = $decoded_json->latest_receipt_info[0]->product_id;

                            $issubscribe = 1;
                            //break;
                        }

                        $auto_renewal = "0";
                        $auto_renew_product_id = "";
                        $expiration_intent = "";

                        //$decoded_json->pending_renewal_info checking for autoreneval
                        foreach ($decoded_json->pending_renewal_info as $key => $row) {

                            if ($row->original_transaction_id == $data['u_transaction_id'] && $row->auto_renew_status == 1) {
                                $auto_renew_product_id = $row->auto_renew_product_id;

                                $auto_renewal = 1;
                                break;
                            } else if ($row->original_transaction_id == $data['u_transaction_id'] && $row->auto_renew_status == 0) {
                                $expiration_intent = $row->expiration_intent;
                            }
                        }
                        //---/$decoded_json->pending_renewal_info checking for autoreneval


                        if ($issubscribe == 1) {

                            $array = array('dLatestExpiryDate' => $expiry_date_curr, 'lReceiptData' => $new_receipt_data, 'eAutoRenewal' => $auto_renewal, 'auto_renew_product_id' => $auto_renew_product_id,'vProductId' => $product_id);

                                $this->db->where('iUserId', $data['u_user_id']);
                                $this->db->where('vOrginalTransactionId', $data['u_transaction_id']);

                                $this->db->update('user_subscription', $array);
                            $return_arr['success'] = '1';
                        } else {

                            $array = array('lReceiptData' => $new_receipt_data, 'eAutoRenewal' => $auto_renewal, 'auto_renew_product_id' => $auto_renew_product_id, 'expiration_intent' => $expiration_intent,'vProductId' => $product_id);

                                $this->db->where('iUserId', $data['u_user_id']);
                                $this->db->where('vOrginalTransactionId', $data['u_transaction_id']);

                                $this->db->update('user_subscription', $array);
                            $return_arr['success'] = '1';
                        }
                    }
                    
                } else if ($data['u_receipt_type'] == 'android') {
                    $user_id        = $data['u_user_id'];
                    $expiry_date    = strtotime($data['u_expiry_date']);

                    // $is_subscribed  = $data['u_e_one_time_transaction'];
                    $packageName    = $this->config->item("PACKAGE_NAME"); //'com.appineers.WidsConnect';
                    $subscriptionId = $data['u_subscription_id'];
                    $purchase_token = $data['u_receipt_data'];

                    if ($purchase_token != '' && $purchase_token != null) {

                        // Including the third_party
                        require_once APPPATH . 'third_party/vendor/autoload.php';
                        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . FCPATH . "whitelabelapp-new.json");

                        // echo pageHeader("Service Account Access");
                        /************************************************
                              Make an API request authenticated with a service
                              account.
                         ************************************************/
                        $client = new Google_Client();

                        // set the location manually
                        $client->setAuthConfig(getenv('GOOGLE_APPLICATION_CREDENTIALS'));

                        $client->setApplicationName("Client_Library_Examples");
                        $client->setScopes(['https://www.googleapis.com/auth/androidpublisher']);

                        // Your redirect URI can be any registered URI, but in this example
                        // we redirect back to this same page
                        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
                        $client->setRedirectUri($redirect_uri);

                        // returns a Guzzle HTTP Client
                        $httpClient = $client->authorize();

                        $AndroidPublisher = new Google_Service_AndroidPublisher($client);

                        $getData = $AndroidPublisher->purchases_subscriptions->get($packageName, $subscriptionId, $purchase_token, $optParams = array());

                        // print_r($getData); exit();

                        if (!empty($getData)) {

                            if ($getData['paymentState'] > 0) // payment recived or free trial
                            {
                                $autorenewal = "0";

                                if (isset($getData['autoRenewing'])) {
                                    if ($getData['autoRenewing'] > 0) {
                                        $autorenewal = "1";
                                    } else {
                                        $autorenewal = "0";
                                    }
                                }

                                $seconds = $getData['expiryTimeMillis'] / 1000;
                                //convert date and time 
                                $expiryTimeMillis = date("Y-m-d h:i:s", $seconds);

                                if (strtotime($current_date) > strtotime($expiryTimeMillis) || $autorenewal == "0") {
                                    $data = array(
                                        'eAutoRenewal' => $autorenewal,
                                    );
                                } else {
                                    $data = array(
                                        'eAutoRenewal' => $autorenewal,
                                        'dLatestExpiryDate'   => $expiryTimeMillis,
                                    );
                                }


                                $this->db->where('iUserId', $user_id);
                                $this->db->where('lReceiptData', $purchase_token);
                                $this->db->update('user_subscription', $data);
                                $affected_rows = $this->db->affected_rows();
                                if ($affected_rows > 0) {
                                    $return_arr['success'] = '1';
                                } else {
                                    $return_arr['success'] = '0';
                                }
                            } else  if ($getData['paymentState'] == "") {
                                $autorenewal = "0";

                                if (isset($getData['autoRenewing'])) {
                                    if ($getData['autoRenewing'] > 0) {
                                        $autorenewal = "1";
                                    } else {
                                        $autorenewal = "0";
                                    }
                                }


                                $data = array(
                                    'eAutoRenewal' => $autorenewal,
                                );


                                $this->db->where('iUserId', $user_id);
                                $this->db->where('lReceiptData', $purchase_token);
                                $this->db->update('user_subscription', $data);
                                $affected_rows = $this->db->affected_rows();
                                if ($affected_rows > 0) {
                                    $return_arr['success'] = '1';
                                } else {
                                    $return_arr['success'] = '0';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $return_arr;
    }
}
