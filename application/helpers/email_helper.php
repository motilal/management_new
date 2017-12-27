<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists("send_mail")) {

    /**
     * 
     * @param type $mail_key
     * @param type $replace_from
     * @param type $replace_to
     * @param type $to
     * @param type $from
     * @param type $from_name
     * @param type $debug
     * @return boolean
     */
    function send_mail($mail_key = "", $replace_from = array(), $replace_to = array(), $to = "", $from = "", $from_name = "", $debug = false) {
        $CI = & get_instance();
        $CI->load->model(array("email_template"));
        $mail_data = $CI->email_template
                ->get_email_template_by_slug($mail_key);
        $subject = str_replace($replace_from, $replace_to, $mail_data->subject);
        $view_data['mail_body'] = str_replace($replace_from, $replace_to, $mail_data->message);
        $view_data['title'] = $mail_data->title;
        $message = sanitize_output($CI->load->view("emails/email_layout", $view_data, true));
        $CI->load->library('email');
        $mail_smtp = $CI->config->item("mail_smtp");
        $CI->email->initialize($mail_smtp);
        $CI->email->clear();

        if ($from == "" || $from_name == "") {
            $CI->email->reply_to($CI->setting->item("site_email"), $this->setting->item("site_title"));
            $CI->email->from($mail_smtp['smtp_user'], $this->setting->item("site_title"));
        } else {
            $CI->email->reply_to($from, $from_name);
            $CI->email->from($mail_smtp['smtp_user'], $from_name);
        }
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);
        $CI->email->send();
        if ($debug == true) {
            echo $CI->email->print_debugger();
            die;
        }
    }

}
