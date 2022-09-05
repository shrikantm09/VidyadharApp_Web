<?php

defined('BASEPATH') || exit('No direct script access allowed');

#####GENERATED_CONFIG_SETTINGS_START#####
$config["check_subscription_status_v1"] = array(
    "title" => "Check Subscription Status",
    "folder" => "master",
    "type" => "Time",
    "start_date" => "",
    "end_date" => "",
    "cron_format" => "0 0 * * *",
    "status" => "Active"
);
##### send log error report to #####
$config["error_log_report_minutes"] = array(
    "title" => "Send error log report (minutes)",
    "folder" => "master",
    "type" => "Time",
    "start_date" => "",
    "end_date" => "",
    "cron_format" => "*/$notifyRunTime->vValue * * * *",
    "status" => "Active"
);


##### This function send email error report end of the day #####
$config["error_log_report_full_day"] = array(
    "title" => "Error Reporting End Of The Day",
    "folder" => "master",
    "type" => "Time",
    "start_date" => "",
    "end_date" => "",
    "cron_format" => "0 0 * * *",
    "status" => "Active"
);


#####GENERATED_CONFIG_SETTINGS_END#####

/* End of file cit_notifications.php */
/* Location: ./application/config/cit_notifications.php */
    