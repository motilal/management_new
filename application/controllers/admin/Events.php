<?php

/**
 * Description of event
 *
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Events extends CI_Controller {

    var $viewData = array();
    var $per_page = DEFAULT_PAGING;

    public function __construct() {
        parent::__construct();
        $this->site_santry->redirect = "admin";
        $this->site_santry->allow(array());
        $this->layout->set_layout("admin/layout/layout_admin");
        $this->load->model(array('event_model' => 'event'));
        $this->viewData['pageModule'] = 'Event Manager';
    }

    public function index() {
        $this->acl->has_permission('event-index');
        $condition = array();
        if ($this->input->get('download') == 'report') {
            $result = $this->event->get_list($condition);
            $csv_array[] = array('title' => 'Title', 'description' => 'Description', 'start_data' => 'Start Date', 'end_date' => 'End_date', 'status' => 'Status', 'created' => 'Created');
            foreach ($result->result() as $row) {
                $this->load->helper('csv');
                $csv_array[] = array('name' => $row->title, 'phone' => "$row->description", 'start_date' => $row->start_date, 'end_date' => $row->end_date, $row->status == 1 ? 'Active' : 'Inactive', 'created' => $row->created);
            }
            $Today = date('dmY');
            array_to_csv($csv_array, "EventListing_$Today.csv");
            exit();
        }
        if ($this->input->is_ajax_request()) {
            $orderColomn = array(1 => 'title', 2 => 'start_date', 3 => 'end_date', 4 => 'status');
            $params = dataTableGetRequest($this->input->get(), $orderColomn);
            if (!empty($params->search)) {
                $keyword = $this->db->escape_str($params->search);
                $condition["title like '%{$keyword}%' OR description like '%{$keyword}%'"] = null;
            }
            $result = $this->event->get_list($condition, $params->limit, $params->order, TRUE);
            if ($result->data->num_rows() > 0) {
                $response['data'] = $this->showTableData($result->data->result());
            } else {
                $response['data'] = array();
            }
            $response['recordsFiltered'] = $response['recordsTotal'] = $result->num_rows;
            $this->output->set_content_type('application/json')->set_output(json_encode($response))->_display();
            exit();
        }

        $result = $this->event->get_list($condition, array('start' => 0, 'limit' => $this->per_page), '', TRUE);
        if ($result->data->num_rows() > 0) {
            $this->viewData['result'] = $this->showTableData($result->data->result());
        }
        $this->viewData['title'] = "Events list";
        $this->viewData['datatable_asset'] = true;
        $this->viewData['pageHeading'] = 'Event Listing';
        $this->viewData['breadcrumb'] = array('Event Manager' => 'admin/events', $this->viewData['title'] => '');
        $this->layout->view("admin/event/index", $this->viewData);
    }

    public function showTableData($data) {
        $resultData = array();
        if ($data != "") {
            foreach ($data as $key => $row) {
                $rowData = array();
                $rowData[0] = getPageSerial($this->input->get('length'), $this->input->get('start'), $key);
                $rowData[1] = $row->title;
                $rowData[2] = date(DATE_FORMATE, strtotime($row->start_date));
                $rowData[3] = date(DATE_FORMATE, strtotime($row->end_date));
                $rowData[4] = $this->layout->element('admin/element/_module_status', array('status' => $row->status, 'id' => $row->id, 'url' => "admin/events/changestatus"), true);
                $editUrl = 'admin/events/manage/' . $row->id;
                $deleteUrl = 'admin/events/delete';
                $rowData[5] = $this->layout->element('admin/element/_module_action', array('id' => $row->id, 'editUrl' => $editUrl, 'deleteUrl' => $deleteUrl), true);
                $resultData[] = $rowData;
            }
        }
        return $resultData;
    }

    public function manage($id = null) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('manage');
        $this->viewData['title'] = "Add Event";
        if ($id > 0) {
            $this->viewData['data'] = $data = $this->event->getById($id);
            if (empty($data)) {
                $this->session->set_flashdata("error", getLangText('LinkExpired'));
                redirect('admin/events');
            }
            $this->viewData['title'] = "Edit Event";
        }
        if ($this->form_validation->run() === TRUE) {
            $start_date = date('Y-m-d H:i:s', strtotime($this->input->post('start_date')));
            $end_date = date('Y-m-d H:i:s', strtotime($this->input->post('end_date')));
            $data = array(
                "title" => $this->input->post('title'),
                "description" => $this->input->post('description', FALSE),
                "start_date" => $start_date,
                "end_date" => $end_date
            );
            if ($id > 0) {
                $data['slug'] = create_unique_slug($this->input->post('title'), 'events', 'slug', 'id', $id);
            } else {
                $data['slug'] = create_unique_slug($this->input->post('title'), 'events', 'slug');
            }
            if ($this->input->post('id') > 0) {
                $data['update'] = date("Y-m-d H:i:s");
                $this->db->update("events", $data, array("id" => $this->input->post('id')));
                $this->session->set_flashdata("success", getLangText('EventUpdateSuccess'));
            } else {
                $data['created'] = date("Y-m-d H:i:s");
                $this->db->insert("events", $data);
                $this->session->set_flashdata("success", getLangText('EventAddSuccess'));
            }
            redirect("admin/events");
        }
        $this->viewData['ckeditor_asset'] = true;
        $this->viewData['datetimepicker_asset'] = true;
        $this->viewData['pageHeading'] = $this->viewData['title'];
        $this->viewData['breadcrumb'] = array('Event Manager' => 'admin/events', $this->viewData['title'] => '');
        $this->layout->view("admin/event/manage", $this->viewData);
    }

    public function delete() {
        $response = array();
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            if ($id > 0 && $this->db->where("id", $id)->delete("events")) {
                $response['success'] = 'Event deleted successfully.';
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
                $this->db->where("id", $id)->update("events", array("status" => 0));
                $pageaction = 'Inactive';
            } else if ($status == "0") {
                $this->db->where("id", $id)->update("events", array("status" => 1));
                $pageaction = 'Active';
            }
            $response['success'] = "Event $pageaction Successfully.";
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    function _validate_daterange($str) {
        $start_date = strtotime($this->input->post('start_date'));
        $end_date = strtotime($this->input->post('end_date'));

        if ($end_date < $start_date) {
            $this->form_validation->set_message('_validate_daterange', '%s will not greater than start date.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function _validate_dateformate($str) {
        if (!empty($str) && validateDate($str, 'd-m-Y H:i') == false) {
            $this->form_validation->set_message('_validate_dateformate', 'The %s field is not in the correct format.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function calendar() {
        $this->viewData['title'] = "Event Calendar";
        $this->viewData['drop_menu_css'] = true;
        $this->layout->view("admin/event/calendar", $this->viewData);
    }

    function calendarEvent() {
        if ($this->input->is_ajax_request()) {
            $start_date = $this->input->get('start');
            $end_date = $this->input->get('end');
            $condition = array("start_date BETWEEN '$start_date' AND '$end_date'" => NULL);
            $blogpressrealease = $this->db->select('id,Title,start_date,end_date', false)
                    ->get_where('events', $condition);
            $response = array();
            if ($blogpressrealease->num_rows() > 0) {
                foreach ($blogpressrealease->result() as $key => $val) {
                    $response[] = array('title' => $val->Title, 'date' => $val->start_date, 'url' => site_url("admin/events/manage/$val->id"));
                }
                echo json_encode($response);
            }
            exit;
        }
    }

}

?>
