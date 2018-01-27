<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth extends CI_Controller {

    public $viewData;

    function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array("login", "logout"));
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('language'));
    }

    public function login() {
        $this->layout->set_layout("admin/layout/layout_login");
        if ($this->ion_auth->is_admin()) {
            redirect('admin/dashboard');
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
            $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

            if ($this->form_validation->run() == TRUE) {
                if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'))) {
                    $this->getSubadminPermission();
                    redirect($this->input->post('request') ? $this->input->post('request') : "/admin/dashboard/?auth=verify");
                } else {
                    $this->session->set_flashdata('login_error', $this->ion_auth->errors());
                    redirect('admin', 'refresh');
                }
            }
        }
        $this->viewData['request'] = $this->input->get("request") ? base64_decode($this->input->get('request')) : "";
        $this->viewData['title'] = "Admin Login";
        $this->layout->view("admin/auth/login", $this->viewData);
    }

    public function logout() {
        $logout = $this->ion_auth->logout();
        redirect('admin', 'refresh');
    }

    function getSubadminPermission() {
        if ($this->ion_auth->is_subadmin() === TRUE) {
            $this->load->model(array('user_model' => 'user')); 
            $user_id = $this->ion_auth->get_user_id();
            $upkeys = $this->user->get_userpermission_keys(array('user_id' => $user_id));
            if ($upkeys) {
                $actions = array();
                $group = array();
                foreach ($upkeys as $ukey) {
                    $actions[] = $ukey->key;
                    $group[] = $ukey->group;
                }
                if (!empty($group)) {
                    $group = array_unique($group);
                    $group = array_map('strtolower', $group);
                } 
                $this->session->set_userdata('_subadmin_allow_actions', $actions);
                $this->session->set_userdata('_subadmin_allow_module', $group);
            }
        }
    }

}
