<?php

/**
 * Description of States
 *
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class States extends CI_Controller {

    var $viewData = array();
    var $segment = 4;
    var $per_page = DEFAULT_PAGING;

    public function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array());
        $this->load->model(array('state_model' => 'state'));
        $this->layout->set_layout("admin/layout/layout_admin");
        $this->viewData['pageModule'] = 'State Manager';
    }

    public function index() {
        $condition = array();
        if ($this->input->get('download') == 'report') {
            $result = $this->state->get_list($condition);
            $csv_array[] = array('name' => 'Name', 'short_name' => 'Short Name', 'country' => 'Country', 'status' => 'Status');
            foreach ($result->result() as $row) { 
                $this->load->helper('csv');
                $csv_array[] = array('name' => $row->name, 'short_name' => $row->short_name,'country' => $row->country_name, 'status' => $row->status == 1 ? 'Active' : 'InActive');
            }
            $Today = date('dmY');
            array_to_csv($csv_array, "StateListing_$Today.csv");
            exit();
        }
        if ($this->input->is_ajax_request()) {
            $orderColomn = array(1 => 'name', 2 => 'short_name', 3 => 'country_name', 4 => 'status');
            $params = dataTableGetRequest($this->input->get(), $orderColomn);
            if (!empty($params->search)) {
                $keyword = $this->db->escape_str($params->search);
                $condition["states.name like '%{$keyword}%' OR states.short_name like '%{$keyword}%' OR countries.name like '%{$keyword}%'"] = null;
            }
            $result = $this->state->get_list($condition, $params->limit, $params->order, TRUE);
            if ($result->data->num_rows() > 0) {
                $response['data'] = $this->showTableData($result->data->result());
            } else {
                $response['data'] = array();
            }
            $response['recordsFiltered'] = $response['recordsTotal'] = $result->num_rows;
            $this->output->set_content_type('application/json')->set_output(json_encode($response))->_display();
            exit();
        }

        $result = $this->state->get_list($condition, array('start' => 0, 'limit' => $this->per_page), array('states.name', 'ASC'), TRUE);
        if ($result->data->num_rows() > 0) {
            $this->viewData['result'] = $this->showTableData($result->data->result());
        }
        $this->viewData['title'] = "States list";
        $this->viewData['datatable_asset'] = true;
        $this->viewData['pageHeading'] = 'State Listing';

        $this->load->model('country_model', 'country');
        $this->viewData['country_dropdown'] = $this->country->get_list(array('countries.status' => '1'), array('countries.name', 'ASC'));

        $this->viewData['breadcrumb'] = array('State Manager' => 'admin/states', $this->viewData['title'] => '');
        $this->layout->view("admin/state/index", $this->viewData);
    }

    private function showTableData($data) {
        $resultData = array();
        if ($data != "") {
            foreach ($data as $key => $row) {
                $rowData = array();
                $rowData[0] = getPageSerial($this->input->get('length'), $this->input->get('start'), $key);
                $rowData[1] = $row->name;
                $rowData[2] = $row->short_name;
                $rowData[3] = "<span data-countryid='$row->country_id'>" . $row->country_name . "</span>";
                $rowData[4] = $this->layout->element('admin/element/_module_status', array('status' => $row->status, 'id' => $row->id, 'url' => "admin/states/changestatus"), true);
                $editUrl = 'admin/states/manage';
                $deleteUrl = 'admin/states/delete';
                $rowData[5] = $this->layout->element('admin/element/_module_action', array('id' => $row->id, 'editUrl' => $editUrl, 'deleteUrl' => $deleteUrl), true);
                $resultData[] = $rowData;
            }
        }
        return $resultData;
    }

    public function manage($id = null) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('manage');
        if ($this->form_validation->run() === TRUE) {
            $data = array(
                "name" => $this->input->post('name'),
                "short_name" => $this->input->post('short_name'),
                'country_id' => $this->input->post('country_id')
            );

            if ($this->input->post('id') > 0) {
                $this->db->update("states", $data, array("id" => $this->input->post('id')));
                $resource_id = $this->input->post('id');
                $response['msg'] = getLangText('StateUpdateSuccess');
                $response['mode'] = 'edit';
            } else {
                $data['status'] = 1;
                $this->db->insert("states", $data);
                $resource_id = $this->db->insert_id();
                $response['msg'] = getLangText('StateAddSuccess');
                $response['mode'] = 'add';
            }
            $detail = $this->state->getById((int) $resource_id, $join = true);
            $detail->statusButtons = $this->layout->element('admin/element/_module_status', array('status' => $detail->status, 'id' => $detail->id, 'url' => "admin/states/changestatus"), true);
            $detail->actionButtons = $this->layout->element('admin/element/_module_action', array('id' => $detail->id, 'editUrl' => 'admin/states/manage', 'deleteUrl' => 'admin/states/delete'), true);
            $response['data'] = $detail;
            $response['success'] = true;
        } else {
            $response['validation_error'] = $this->form_validation->error_array();
        }
        $this->output->set_content_type('application/json')
                ->set_output(json_encode($response))->_display();
        exit();
    }

    public function delete() {
        $response = array();
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            if ($id > 0 && $this->db->where("id", $id)->delete("states")) {
                $response['success'] = 'State deleted successfully.';
            } else {
                $response['error'] = 'Invalid request';
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function changestatus() {
        $response = array();
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $pageaction = '';
            if ($status == "1") {
                $this->db->where("id", $id)->update("states", array("status" => 0));
                $pageaction = 'Inactive';
            } else if ($status == "0") {
                $this->db->where("id", $id)->update("states", array("status" => 1));
                $pageaction = 'Active';
            }
            $response['success'] = "State $pageaction Successfully.";
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    function _validate_state_name($str) {
        $id = $this->input->post('id');
        $condition = array('name' => $str, 'country_id' => $this->input->post('country_id'));
        if (!empty($id) && is_numeric($id)) {
            $condition['id !='] = $id;
        }
        $num_row = $this->db->where($condition)->count_all_results('states');
        if ($num_row >= 1) {
            $this->form_validation->set_message('_validate_state_name', getLangText('StateAlreadyExist'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>
