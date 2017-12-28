<?php

/**
 * Description of States
 *
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cities extends CI_Controller {

    var $viewData = array();
    var $segment = 4;
    var $per_page = DEFAULT_PAGING;

    public function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array());
        $this->load->model(array('city_model' => 'city'));
        $this->layout->set_layout("admin/layout/layout_admin");
        $this->viewData['pageModule'] = 'City Manager';
    }

    public function index() {
        $condition = array();
        if ($this->input->get('download') == 'report') {
            $result = $this->city->get_list($condition);
            $csv_array[] = array('name' => 'Name', 'state' => 'State', 'country' => 'Country', 'status' => 'Status');
            foreach ($result->result() as $row) {
                $this->load->helper('csv');
                $csv_array[] = array('name' => $row->name, 'state' => $row->state_name, 'country' => $row->country_name, 'status' => $row->status == 1 ? 'Active' : 'InActive');
            }
            $Today = date('dmY');
            array_to_csv($csv_array, "CityListing_$Today.csv");
            exit();
        }
        if ($this->input->is_ajax_request()) {
            $orderColomn = array(1 => 'name', 2 => 'state_name', 3 => 'country_name', 4 => 'status');
            $params = dataTableGetRequest($this->input->get(), $orderColomn);
            if (!empty($params->search)) {
                $keyword = $this->db->escape_str($params->search);
                $condition["cities.name like '%{$keyword}%' OR cities.name like '%{$keyword}%' OR countries.name like '%{$keyword}%'"] = null;
            }
            $result = $this->city->get_list($condition, $params->limit, $params->order, TRUE);
            if ($result->data->num_rows() > 0) {
                $response['data'] = $this->showTableData($result->data->result());
            } else {
                $response['data'] = array();
            }
            $response['recordsFiltered'] = $response['recordsTotal'] = $result->num_rows;
            $this->output->set_content_type('application/json')->set_output(json_encode($response))->_display();
            exit();
        }

        $result = $this->city->get_list($condition, array('start' => 0, 'limit' => $this->per_page), array('cities.name', 'ASC'), TRUE);
        if ($result->data->num_rows() > 0) {
            $this->viewData['result'] = $this->showTableData($result->data->result());
        }
        $this->viewData['title'] = "States list";
        $this->viewData['datatable_asset'] = true;
        $this->viewData['pageHeading'] = 'City Listing';

        $this->load->model('country_model', 'country');
        $this->viewData['country_dropdown'] = $this->country->get_list(array('countries.status' => '1'), array('countries.name', 'ASC'));

        $this->viewData['breadcrumb'] = array('City Manager' => 'admin/cities', $this->viewData['title'] => '');
        $this->layout->view("admin/city/index", $this->viewData);
    }

    private function showTableData($data) {
        $resultData = array();
        if ($data != "") {
            foreach ($data as $key => $row) {
                $rowData = array();
                $rowData[0] = getPageSerial($this->input->get('length'), $this->input->get('start'), $key);
                $rowData[1] = $row->name;
                $rowData[2] = "<span data-stateid='$row->state_id'>" . $row->state_name . "</span>";
                $rowData[3] = "<span data-countryid='$row->country_id'>" . $row->country_name . "</span>";
                $rowData[4] = $this->layout->element('admin/element/_module_status', array('status' => $row->status, 'id' => $row->id, 'url' => "admin/cities/changestatus"), true);
                $editUrl = 'admin/cities/manage';
                $deleteUrl = 'admin/cities/delete';
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
                "country_id" => $this->input->post('country_id'),
                'state_id' => $this->input->post('state_id')
            );
            if ($this->input->post('id') > 0) {
                $this->db->update("cities", $data, array("id" => $this->input->post('id')));
                $resource_id = $this->input->post('id');
                $response['msg'] = getLangText('CityUpdateSuccess');
                $response['mode'] = 'edit';
            } else {
                $data['status'] = 1;
                $this->db->insert("cities", $data);
                $resource_id = $this->db->insert_id();
                $response['msg'] = getLangText('CityAddSuccess');
                $response['mode'] = 'add';
            }
            $detail = $this->city->getById($resource_id, $join = true);
            $detail->statusButtons = $this->layout->element('admin/element/_module_status', array('status' => $detail->status, 'id' => $detail->id, 'url' => "admin/cities/changestatus"), true);
            $detail->actionButtons = $this->layout->element('admin/element/_module_action', array('id' => $detail->id, 'editUrl' => 'admin/cities/manage', 'deleteUrl' => 'admin/cities/delete'), true);
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
            if ($id > 0 && $this->db->where("id", $id)->delete("cities")) {
                $response['success'] = 'City deleted successfully.';
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
                $this->db->where("id", $id)->update("cities", array("status" => 0));
                $pageaction = 'Inactive';
            } else if ($status == "0") {
                $this->db->where("id", $id)->update("cities", array("status" => 1));
                $pageaction = 'Active';
            }
            $response['success'] = "State $pageaction Successfully.";
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    function _validate_city_name($str) {
        $id = $this->uri->segment(4);
        $condition = array('name' => $str, 'country_id' => $this->input->post('country_id'));
        if (!empty($id) && is_numeric($id)) {
            $condition['id !='] = $id;
        }
        $num_row = $this->db->where($condition)->count_all_results('cities');
        if ($num_row >= 1) {
            $this->form_validation->set_message('_validate_city_name', getLangText('CityAlreadyExist'));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function state_dropdown() {
        if ($this->input->post('id') > 0) {
            //State List Drop down
            $this->load->model(array('state_model' => 'state'));
            $condition = array('states.status' => '1', 'states.country_id' => $this->input->post('id'));
            $order = array('states.name', 'ASC');
            $state_dropdown = $this->state->get_list($condition, null, $order);
            $response = array("<option value=''>Select State</option>");
            if ($state_dropdown->num_rows() > 0):
                if ($state_dropdown->num_rows() > 0):
                    foreach ($state_dropdown->result() as $key => $value) :
                        $response[] = "<option value='{$value->id}'>{$value->name}</option>";
                    endforeach;
                endif;
            endif;
        }else {
            $response = array("<option value=''>Select State</option>");
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        return;
    }

}

?>
