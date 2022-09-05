<?php

   
/**
 * Description of Subscription Purchase Extended Controller
 *
 * @module Extended Subscription Purchase
 *
 * @class Cit_Subscription_purchase.php
 *
 * @path application\webservice\master\controllers\Cit_Subscription_purchase.php
 *
 * @author CIT Dev Team
 *
 * @date 09.05.2020
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Cit_Subscription_purchase extends Subscription_purchase
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
     * This method is used to check User Transaction Exit.
     * 
     * @param array $input_params input_params array to process loop flow.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function checkUserTransactionExit($input_params=array())
    {
        $user_id=$input_params['user_id'];
        $original_transaction_id=$input_params["original_transaction_id"];
  
        $return_arr['status']='0';

        if (!empty($user_id) && !empty($original_transaction_id)) {
            $this->db->select('iUserId,vOrginalTransactionId');
            $this->db->from('user_subscription');
            $this->db->where('iUserId', $user_id);
            $this->db->where('vOrginalTransactionId', $original_transaction_id);
            $transaction_data=$this->db->get()->result_array();
       
            if (!empty($transaction_data)) {
                $return_arr['status'] = "1";
                return  $return_arr;
            }
        }
  
        return  $return_arr;
    }

    /**
     * This method is used to check user android transaction_exists.
     * 
     * @param array $input_params input_params array to give input.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function check_user_android_transaction_exists($input_params=array())
    {
        $user_id=$input_params['user_id'];

        if (isset($input_params["purchase_token"])) {
            $purchase_token = $input_params["purchase_token"];
        }
  
        if (isset($input_params["subscription_id"])) {
            $ProductId = $input_params["subscription_id"];
        }

        $return_arr['status']='0';

        if (!empty($user_id) && !empty($purchase_token)) {
            $this->db->select('iUserId,lReceiptData');
            $this->db->from('user_subscription');
            $this->db->where('iUserId', $user_id);
            $this->db->where('lReceiptData', $purchase_token);
            $this->db->where('vProductId', $ProductId);

            $transaction_data=$this->db->get()->result_array();
       
            if (!empty($transaction_data)) {
                $return_arr['status'] = "1";
                return  $return_arr;
            }
        }
  
        return  $return_arr;
    }

    /**
     * This method is used to check user android transaction_exists.
     * 
     * @param array $input_params input_params array to give input.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function validateReceiptCheck($input_params=array())
    {

        //fetching the file contents
        $sample_json = file_get_contents($_FILES['receipt_data']['tmp_name']);
    
        $applesharedsecret = $this->config->item("SUBSCRIPTION_PASSWORD");

        /*  if(isset($_ENV['debug_action']) && TRUE == $_ENV['debug_action']){
             $appleurl="https://sandbox.itunes.apple.com/verifyReceipt";
         }else{
             $appleurl="https://buy.itunes.apple.com/verifyReceipt";
         } */

        $current_timezone = date_default_timezone_get();
        // convert the current timezone to UTC
        date_default_timezone_set('UTC');
        $current_date = date("Y-m-d H:i:s");
        // Again coverting into local timezone
        date_default_timezone_set($current_timezone);
        //$appleurl =$this->config->item("SUBSCRIPTION_ITUNES_URL");
        //https://buy.itunes.apple.com/verifyReceipt //for production


        // live Url
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
        $return_arr = array();


        if ($decoded_json->status == '21007') {
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

            $return_arr = array();
        }

     
        if ($err) {
            $return_arr[0]['original_transaction_id'] = '';
            $return_arr[0]['expiry_date']    = '';
            $return_arr[0]['success']        = 0;
            $return_arr[0]['message']        = $err;
            $return_arr[0]['receipt_data_v1']   ="";
        } else {
            $decoded_json = json_decode($jsonresult);
            $return_arr =array();

            if ($decoded_json->status == '0' && $decoded_json->receipt->bundle_id =="com.appineers.WidsConnect") {

          // sync_flag : 'buy','restore', 'sync'

                if (!empty($decoded_json->receipt->in_app) && $input_params['subscription_purchase_type'] == "buy") {
                    // if(!empty($decoded_json->latest_receipt_info))
                    // echo "calling buy flow---";
                    $expiry_date_curr = "";
                    $original_transaction_id = "";
                    $product_id = "";
                    $auto_renew_product_id = "";
            
                    $issubscribe=0;
                    $expires_date = array();


                    foreach ($decoded_json->receipt->in_app as $key => $row) {
                        // foreach ($decoded_json->latest_receipt_info as $key => $row)
                        if (false==empty($row->expires_date)) {
                            $expires_date[$key] = $row->expires_date;
                        } else {
                            unset($decoded_json->receipt->in_app[$key]);
                            //  unset($decoded_json->latest_receipt_info[$key]);
                        }
                    }

                    //  print_r($decoded_json->latest_receipt_info);

                    //sort array descending order on expire Date
                    array_multisort($expires_date, SORT_DESC, $decoded_json->receipt->in_app);
                    //array_multisort($expires_date, SORT_DESC, $decoded_json->latest_receipt_info);

                    $gmt_date       = $decoded_json->receipt->in_app[0]->expires_date;
                    //   $gmt_date       = $decoded_json->latest_receipt_info[0]->expires_date;
                    //divide date and time
                    $date1 = explode(' ', $gmt_date);
                    $expiry_date_temp= $date1[0]." ".$date1[1];

                    //     echo "latest expiry date--".  $expiry_date_temp; //exit;
                    //latest expire date is greater than current date
                    if (strtotime($expiry_date_temp) > strtotime($current_date)) {
                        $issubscribe=1;
                    }

                    $original_transaction_id = $decoded_json->receipt->in_app[0]->original_transaction_id;
                        
                    $auto_renew_product_id = $decoded_json->receipt->in_app[0]->auto_renew_product_id;

                    $gmt_date       = $decoded_json->receipt->in_app[0]->expires_date;
                    $date1 = explode(' ', $gmt_date);
                    $expiry_date_curr = $date1[0]." ".$date1[1];
                    $product_id = $decoded_json->receipt->in_app[0]->product_id;

                    if ($issubscribe==1) {
                        $return_arr[0]['success']        = 1;
                        $return_arr[0]['message']        = 'Subscription purchased successfully.';
                    } else {
                        if (isset($input_params["user_id"])) {
                            $where_arr["user_id"] = $input_params["user_id"];
                        }

                        $where_arr["original_transaction_id"] = $original_transaction_id;

                        $params_arr["expiry_date"] = $expiry_date_curr;
              
                        $params_arr["_ereceipttype"] = "ios";
                        $params_arr["receipt_data_v1"] = $sample_json;
                        $params_arr["autorenewal"] = 1;
                        $params_arr["product_id"] = $product_id;
              

                        $this->block_result = $this->subscription_purchase_model->update_subscription_purchase($params_arr, $where_arr);
                        //  echo "latest expiry date--".  $expiry_date_temp;
                        $return_arr[0]['success']        = 0;
                        $return_arr[0]['message']        = 'Your subscription is expired. Please subscribe again to use app features.';
                    }
            
            
                    $return_arr[0]['original_transaction_id'] = $original_transaction_id;
                    $return_arr[0]['auto_renew_product_id'] = $auto_renew_product_id;
                    $return_arr[0]['product_id'] = $product_id;
                    $return_arr[0]['status'] = "";
                    $return_arr[0]['expiry_date']    = $expiry_date_curr;
                    $return_arr[0]['receipt_data_v1']   =$sample_json;

                // compare date and send isSubscribed flag
                } elseif (!empty($decoded_json->latest_receipt_info) && ($input_params['subscription_purchase_type'] == "restore" || $input_params['subscription_purchase_type'] == "sync")) {

                // echo "calling restore flow---";
 
                    $expiry_date_curr = "";
                    $original_transaction_id = "";
                    $product_id = "";
                    $auto_renew_product_id = "";
           
                    $issubscribe=0;
                    $expires_date = array();

                    foreach ($decoded_json->latest_receipt_info as $key => $row) {
                        if (false==empty($row->expires_date)) {
                            $expires_date[$key] = $row->expires_date;
                        } else {
                            unset($decoded_json->latest_receipt_info[$key]);
                        }
                    }

                    //   print_r($decoded_json->latest_receipt_info);

                    //sort array descending order on expire Date
                    array_multisort($expires_date, SORT_DESC, $decoded_json->latest_receipt_info);

                    $gmt_date       = $decoded_json->latest_receipt_info[0]->expires_date;
                    //divide date and time
                    $date1 = explode(' ', $gmt_date);
                    $expiry_date_temp= $date1[0]." ".$date1[1];

                    //  echo "latest expiry date--".  $expiry_date_temp; //exit;
                    //latest expire date is greater than current date
                    if (strtotime($expiry_date_temp) > strtotime($current_date)) {
                        $issubscribe=1;
                    }

                    $original_transaction_id = $decoded_json->latest_receipt_info[0]->original_transaction_id;
                       
                    $auto_renew_product_id = $decoded_json->latest_receipt_info[0]->auto_renew_product_id;

                    $gmt_date       = $decoded_json->latest_receipt_info[0]->expires_date;
                    $date1 = explode(' ', $gmt_date);
                    $expiry_date_curr = $date1[0]." ".$date1[1];
                    $product_id = $decoded_json->latest_receipt_info[0]->product_id;

                    if ($issubscribe==1) {
                        $return_arr[0]['success']        = 1;
                        $return_arr[0]['message']        = 'Subscription purchased successfully.';
                    } else {
                        if (isset($input_params["user_id"])) {
                            $where_arr["user_id"] = $input_params["user_id"];
                        }

                        $where_arr["original_transaction_id"] = $original_transaction_id;

                        $params_arr["expiry_date"] = $expiry_date_curr;
                
                        $params_arr["_ereceipttype"] = "ios";
                        $params_arr["receipt_data_v1"] = $sample_json;
                        $params_arr["autorenewal"] = 1;
                        $params_arr["product_id"] = $product_id;
                

                        $this->block_result = $this->subscription_purchase_model->update_subscription_purchase($params_arr, $where_arr);
                        //  echo "latest expiry date--".  $expiry_date_temp;
                        $return_arr[0]['success']        = 0;
                        $return_arr[0]['message']        = 'Your subscription is expired. Please subscribe again to use app features.';
                    }
           
           
                    $return_arr[0]['original_transaction_id'] = $original_transaction_id;
                    $return_arr[0]['auto_renew_product_id'] = $auto_renew_product_id;
                    $return_arr[0]['product_id'] = $product_id;
                    $return_arr[0]['status'] = "";
                    $return_arr[0]['expiry_date']    = $expiry_date_curr;
                    $return_arr[0]['receipt_data_v1']   =$sample_json;
                // compare date and send isSubscribed flag
                } else {
                    // if getting array  if(!empty($decoded_json->receipt->in_app))
                    $return_arr[0]['success']        = 0;
                    $return_arr[0]['message']        = 'No transaction to process.';
                }
            } else {
                //if($decoded_json->status == '0' && $decoded_json->receipt->bundle_id =="com.ab.MyMarketManager")
                $return_arr[0]['success']        = 0;
                $return_arr[0]['message']        = "this request is invalid";
            }
        }
        
        return $return_arr;
    }

    /**
     * This method is used to subscription details.
     * 
     * @param array $input_params input_params array to give input.
     * 
     * @return array $input_params returns modfied input_params array.
     */
    public function subscriptionDetails($input_params = array())
    {
        $result_arr['success_v1'] = 0;
        $result_arr['expiry_date_v1'] = '';
  
        $user_id        = $input_params['user_id'];
        $packageName    = $this->config->item("PACKAGE_NAME"); //'com.appineers.WidsConnect';
        $subscriptionId = $input_params['subscription_id'];
        $purchase_token = $input_params['purchase_token'];
   
        // Including the third_party
        require_once APPPATH.'third_party/vendor/autoload.php';
        putenv("GOOGLE_APPLICATION_CREDENTIALS=".FCPATH."whitelabelapp-new.json");
   
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
   
        try {
            $getData = $AndroidPublisher->purchases_subscriptions->get($packageName, $subscriptionId, $purchase_token, $optParams = array());

        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $getData = array();
        }
    
   
        if (!empty($getData)) {
            $seconds = $getData['expiryTimeMillis'] / 1000;
            $expiryTimeMillis = date("Y-m-d H:i:s", $seconds);
            $result_arr['success_v1'] = "1";
            $result_arr['expiry_date_v1'] = $expiryTimeMillis;
            $result_arr['autoRenewing'] = $getData['autoRenewing'];
        }
    
        return $result_arr;
    }
}
