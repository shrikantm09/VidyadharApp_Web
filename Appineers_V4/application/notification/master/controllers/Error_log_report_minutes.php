<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Manage Likes Count Controller
 *
 * @category notification
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Send Error Logs
 *
 * @class Error_log_report_minutes.php
 *
 * @path application\notifications\user\controllers\Error_log_report_minutes.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */

class Error_log_report_minutes extends Cit_Controller
{
    /** @var array $settings_params contains settings parameters */
    public $settings_params;

    /** @var array $output_params contains output parameters */
    public $output_params;

    /** @var array $single_keys contains single array */
    public $single_keys;

    /** @var array $multiple_keys contains multiple array */
    public $multiple_keys;

    /** @var array $block_result contains query returns result array*/
    public $block_result;

    /**
     * To initialize class objects/variables.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->multiple_keys  = array(
            "get_error_logs",
        );
        $this->block_result = array();
        
        $this->load->library('lib_log');
        $this->load->library('notifyresponse');
        $this->load->model('error_log_report_minutes_model');
    }

    /**
     * This method is used to initiate api execution flow.
     *
     * @param array $request_arr request_arr array is used for api input.
     *
     * @return array $output_response returns output response of API.
     */
    public function start_error_log_report_minutes($request_arr = array())
    {
        try {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = array();

            $input_params = $this->get_error_logs($input_params);

            if (!empty($input_params['log_records']['data'])) {
                if (count($input_params['log_records']['data']) >= $this->config->item("LOG_REPORT_ERROR_COUNT")) {

                    $recArray = array_column($input_params['log_records']['data'], 'access_log_id');
                    
                    if ($this->config->item('EOD_LOG_REPORT') == 'Y') {
                        $input_params = $this->email_notification($input_params['log_records']);
                    }

                    $input_params = $this->update_error_logs($recArray);
                    
                    $output_response = $this->finish_success($input_params);
                } else {
                    $output_response = $this->finish_success_1($input_params);
                }
            } else {
                $output_response = $this->finish_fail($input_params);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $output_response;
    }

    /**
     * This method is used to process query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function get_error_logs($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->error_log_report_minutes_model->get_today_error_logs();
            
            if (!$this->block_result["success"]) {
                throw new Exception("No api access log records found.");
            }
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["log_records"] = $this->block_result["data"];

        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * This method is used to process query block.
     *
     * @param array $input_params input_params array to process loop flow.
     *
     * @return array $input_params returns modfied input_params array.
     */
    public function update_error_logs($recArray = array())
    {
        $this->block_result = array();
      
        try {
            $recArray = array_values($recArray);

            $this->block_result = $this->error_log_report_minutes_model->update_error_logs($recArray);
            // if (!$this->block_result["success"])
            // {
            //     throw new Exception("Not updated api access log records.");
            // }
        } catch (Exception $e) {
            $this->general->apiLogger($recArray, $e);
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["affected_rows"] = $this->block_result["data"];

        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
    
    /**
    * email_notification method is used to process email notification.
    *
    * @param array $input_params input_params array to process loop flow.
    *
    * @return array $input_params returns modfied input_params array.
    */
    public function email_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $email_arr["error_count"] = count($input_params['data']);

            $email_arr["error_from"] = $input_params['fromDate'];
                
            $email_arr["error_to"] = $input_params['toDate'];

            $email_body = '<table border="1">
                                    <tbody>
                                    <tr>
                                    <td>
                                        Sr. No
                                    </td>
                                    <td>
                                        Log id
                                    </td>
                                    <td>
                                        Module Name
                                    </td>
                                    <td>
                                         Error Report Time
                                    </td>
                                    <td>
                                        Error Type
                                    </td>
                                    
                                    </tr> ';
            $i=1;
            foreach ($input_params['data'] as $line) {
                $email_body .= '<tr>
                                        <td>
                                            '.$i.'
                                        </td>
                                        <td>
                                            '.$line["access_log_id"].'
                                        </td>
                                        <td>
                                            '.$line["api_name"].'
                                        </td>
                                        <td>
                                            '.$line["access_date"].'
                                        </td>
                                        <td>
                                            '.$line["error_type"].'
                                        </td>
                                    </tr>';
                $i++;
            }

            $email_body .= '
                </tbody>
                </table>';
 
            $email_arr["error_report"] = $email_body;
            $email_arr["vEmail"] = $this->config->item('LOG_REPORT_TO_EMAIL');
            ;
            $email_arr["vCcEmail"] = $this->config->item('LOG_REPORT_TO_EMAIL_CC');
            $mailarr = $this->general->getSystemEmailData("ERROR_LOG_REPORT");
            $success = $this->general->sendMail($email_arr, "ERROR_LOG_REPORT", $input_params);

            $log_arr = array();
            $log_arr['eEntityType'] = 'General';
            $log_arr['vReceiver'] = is_array($email_arr["vEmail"]) ? implode(",", $email_arr["vEmail"]) : $email_arr["vEmail"];
            $log_arr['eNotificationType'] = "EmailNotify";
            $log_arr['vSubject'] = $this->general->getEmailOutput("subject");
            $log_arr['tContent'] = $this->general->getEmailOutput("content");
            if (!$success) {
                $log_arr['tError'] = $this->general->getNotifyErrorOutput();
            }
            $log_arr['dtSendDateTime'] = date('Y-m-d H:i:s');
            $log_arr['eStatus'] = ($success) ? "Executed" : "Failed";
            $this->general->insertExecutedNotify($log_arr);
            if (!$success) {
                throw new Exception("Failure in sending mail.");
            }
            $success = 1;
            $message = "Email notification send successfully.";
        } catch (Exception $e) {
            $this->general->apiLogger($input_params, $e);
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["email_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * finish_success method is used to process finish flow.
     *
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "Error report send successfully.",
        );
        $output_fields = array();
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);
        return $responce_arr;
    }




    public function finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "Limit not reached",
        );

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }




    public function finish_fail($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "No Data.",
        );

        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
}
