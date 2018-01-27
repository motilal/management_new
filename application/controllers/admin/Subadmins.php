<?php

/**
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subadmins extends CI_Controller {

    var $viewData = array();
    var $per_page = DEFAULT_PAGING;

    public function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array());
        is_allow_admin(); 
        $this->layout->set_layout("admin/layout/layout_admin");
        $this->load->model(array('user_model' => 'user'));
        $this->viewData['pageModule'] = 'SubAdmin Manager';
    }

    public function index() {
        $condition = array('grp.id' => '3');
        if ($this->input->is_ajax_request()) {
            $orderColomn = array(1 => 'first_name', 2 => 'email', 3 => 'phone', 4 => 'created_on', 5 => 'active');
            $params = dataTableGetRequest($this->input->get(), $orderColomn);
            if (!empty($params->search)) {
                $keyword = $this->db->escape_str($params->search);
                $condition["first_name like '%{$keyword}%' OR last_name like '%{$keyword}%' OR email = '{$keyword}' OR phone = '{$keyword}'"] = null;
            }
            $result = $this->user->get_list($condition, $params->limit, $params->order, TRUE);
            if ($result->data->num_rows() > 0) {
                $response['data'] = $this->showTableData($result->data->result());
            } else {
                $response['data'] = array();
            }
            $response['recordsFiltered'] = $response['recordsTotal'] = $result->num_rows;
            $this->output->set_content_type('application/json')->set_output(json_encode($response))->_display();
            exit();
        }

        $result = $this->user->get_list($condition, array('start' => 0, 'limit' => $this->per_page), '', TRUE);
        if ($result->data->num_rows() > 0) {
            $this->viewData['result'] = $this->showTableData($result->data->result());
        }
        $this->viewData['title'] = "Sub Admin list";
        $this->viewData['datatable_asset'] = true;
        $this->viewData['pageHeading'] = 'SubAdmin Listing';
        $this->viewData['breadcrumb'] = array('SubAdmin Manager' => 'admin/subadmins', $this->viewData['title'] => '');
        $this->layout->view("admin/subadmin/index", $this->viewData);
    }

    public function showTableData($data) {
        $resultData = array();
        if ($data != "") {
            foreach ($data as $key => $row) {
                $rowData = array();
                $rowData[0] = getPageSerial($this->input->get('length'), $this->input->get('start'), $key);
                $rowData[1] = $row->first_name . ' ' . $row->last_name;
                $rowData[2] = $row->email;
                $rowData[3] = $row->phone;
                $rowData[4] = date(DATE_FORMATE, $row->created_on);
                $rowData[5] = $this->layout->element('admin/element/_module_status', array('status' => $row->active, 'id' => $row->id, 'url' => "admin/subadmins/changestatus"), true);
                $editUrl = 'admin/subadmins/edit/' . $row->id;
                $deleteUrl = 'admin/subadmins/delete';
                $permissionUrl = 'admin/subadmins/permissions/' . $row->id;
                $rowData[6] = $this->layout->element('admin/element/_module_action', array('id' => $row->id, 'editUrl' => $editUrl, 'deleteUrl' => $deleteUrl, 'permissionUrl' => $permissionUrl), true);
                $resultData[] = $rowData;
            }
        }
        return $resultData;
    }

    public function add() {
        $this->load->library('form_validation');
        if ($this->form_validation->run('add_subadmins') === TRUE) {
            $username = NULL;
            $password = $this->input->post('password');
            $email = $this->input->post('email');
            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone')
            );
            $group = array('3');
            $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $this->session->set_flashdata("success", getLangText('EventAddSuccess'));
            redirect("admin/subadmins");
        }
        $this->viewData['title'] = "Add SubAdmin";
        $this->viewData['pageHeading'] = $this->viewData['title'];
        $this->viewData['breadcrumb'] = array('Event Manager' => 'admin/subadmins', $this->viewData['title'] => '');
        $this->layout->view("admin/subadmin/add", $this->viewData);
    }

    public function edit($id = null) {
        $this->load->library('form_validation');
        if ($this->form_validation->run('edit_subadmins') === TRUE) {
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'phone' => $this->input->post('phone')
            );
            if ($this->input->post('password') != "") {
                $data['password'] = $this->input->post('password');
            }
            $this->ion_auth->update($id, $data);
            $this->session->set_flashdata("success", getLangText('EventAddSuccess'));
            redirect("admin/subadmins");
        }
        $this->viewData['data'] = $data = $this->ion_auth->user($id)->row();
        if (empty($data)) {
            show_404();
        }
        $this->viewData['title'] = "Update Profile";
        $this->viewData['pageHeading'] = $this->viewData['title'];
        $this->viewData['breadcrumb'] = array('SubAdmin Manager' => 'admin/subadmins', $this->viewData['title'] => '');
        $this->layout->view("admin/subadmin/edit", $this->viewData);
    }

    public function delete() {
        $response = array();
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            if ($id > 0 && $this->ion_auth->delete_user($id)) {
                $response['success'] = 'SubAdmin deleted successfully.';
            } else {
                $response['error'] = 'Invalid request';
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function permissions($id = null) {
        $this->load->library('form_validation');
        if ($this->input->post()) {
            $permission_array = $this->input->post('permission');
            if (!empty($permission_array)) {
                $user_id = $id;
                $this->db->where('user_id', $user_id)->where_not_in('permission_id', $permission_array)->delete('user_permissions');

                $exist_user_permissions = $this->db->select('permission_id')->where('user_id', $user_id)->where_in('permission_id', $permission_array)->get('user_permissions')->result_array();
                $exist_permission = array();
                if (!empty($exist_user_permissions)) {
                    foreach ($exist_user_permissions as $value) {
                        $exist_permission[] = $value['permission_id'];
                    }
                    $permission_array = array_diff($permission_array, $exist_permission);
                }
                foreach ($permission_array as $value) {
                    $data[] = array(
                        'user_id' => $user_id,
                        'permission_id' => $value
                    );
                }
                if (!empty($data)) {
                    $this->db->insert_batch('user_permissions', $data);
                }
                $this->session->set_flashdata("success", 'Permission Update Success.');
                redirect("admin/subadmins");
            } else {
                $this->db->where('user_id', $id)->delete('user_permissions');
            }
        }
        $this->viewData['data'] = $data = $this->ion_auth->user($id)->row();
        if (empty($data)) {
            show_404();
        }
        $permission_data = $this->user->get_permissions();
        $this->viewData['permission_data'] = $permission_group = arrayGrouping($permission_data, 'group');
        $this->viewData['title'] = "Manage Permissions";

        $u_per = $this->user->get_userpermissions(array('user_id' => $id));
        $user_permissions = array();
        if (!empty($u_per)) {
            foreach ($u_per as $row) {
                $user_permissions[] = $row->permission_id;
            }
        }
        $this->viewData['user_permissions'] = $user_permissions;
        $this->viewData['pageHeading'] = $this->viewData['title'];
        $this->viewData['breadcrumb'] = array('SubAdmin Manager' => 'admin/subadmins', $this->viewData['title'] => '');
        $this->layout->view("admin/subadmin/permissions", $this->viewData);
    }

    public function changestatus() {
        $response = array();
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $pageaction = '';
            if ($status == "1") {
                $this->db->where("id", $id)->update("users", array("active" => 0));
                $pageaction = 'Inactive';
            } else if ($status == "0") {
                $this->db->where("id", $id)->update("users", array("active" => 1));
                $pageaction = 'Active';
            }
            $response['success'] = "SubAdmin account $pageaction Successfully.";
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    function _validate_email($email) {
        if ($email != "" && $this->ion_auth->email_check($email)) {
            $this->form_validation->set_message('_validate_email', 'The User %s already exist.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>
