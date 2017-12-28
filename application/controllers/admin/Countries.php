<?php

/**
 * Description of Countries
 *
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Countries extends CI_Controller {

    var $viewData = array();

    public function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array());
        $this->load->model(array('country_model' => 'country'));
        $this->layout->set_layout("admin/layout/layout_admin");
        $this->viewData['pageModule'] = 'Country Manager';
    }

    public function index() {
        $condition = array();
        if ($this->input->get('download') == 'report') {
            $result = $this->country->get_list($condition);
            $csv_array[] = array('name' => 'Name', 'short_name' => 'Short Name', 'status' => 'Status');
            foreach ($result->result() as $row) {
                $this->load->helper('csv');
                $csv_array[] = array('name' => $row->name, 'short_name' => $row->short_name, 'status' => $row->status == 1 ? 'Active' : 'InActive');
            }
            $Today = date('dmY');
            array_to_csv($csv_array, "CountryListing_$Today.csv");
            exit();
        }
        $start = (int) $this->input->get('start');
        $result = $this->country->get_list($condition);
        $this->viewData['result'] = $result;
        $this->viewData['title'] = "Country Listing";
        $this->viewData['datatable_asset'] = true;
        $this->viewData['pageHeading'] = 'Country Listing';
        $this->viewData['breadcrumb'] = array('Country Manager' => 'admin/countries', $this->viewData['title'] => '');
        $this->layout->view("admin/country/index", $this->viewData);
    }

    public function manage() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('manage');
        $response = array();
        if ($this->form_validation->run() === TRUE) {
            $data = array(
                "name" => $this->input->post('name'),
                "short_name" => $this->input->post('short_name')
            );
            if ($this->input->post('id') > 0) {
                $this->db->update("countries", $data, array("id" => $this->input->post('id')));
                $resource_id = $this->input->post('id');
                $response['msg'] = getLangText('CountryUpdateSuccess');
                $response['mode'] = 'edit';
            } else {
                $data['status'] = 1;
                $this->db->insert("countries", $data);
                $resource_id = $this->db->insert_id();
                $response['msg'] = getLangText('CountryAddSuccess');
                $response['mode'] = 'add';
            }
            $detail = $this->country->getById($resource_id);
            $detail->statusButtons = $this->layout->element('admin/element/_module_status', array('status' => $detail->status, 'id' => $detail->id, 'url' => "admin/countries/changestatus"), true);
            $detail->actionButtons = $this->layout->element('admin/element/_module_action', array('id' => $detail->id, 'editUrl' => 'admin/countries/manage', 'deleteUrl' => 'admin/countries/delete'), true);
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
            if ($id > 0 && $this->db->where("id", $id)->delete("countries")) {
                $response['success'] = 'Country deleted successfully.';
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
                $this->db->where("id", $id)->update("countries", array("status" => 0));
                $pageaction = 'Inactive';
            } else if ($status == "0") {
                $this->db->where("id", $id)->update("countries", array("status" => 1));
                $pageaction = 'Active';
            }
            $response['success'] = "Country $pageaction Successfully.";
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

}
