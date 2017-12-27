<?php

/**
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Event_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_list($condition = array(), $limit = array(), $order = array(), $with_num_rows = false) {
        $this->db->select("events.*");
        if (!empty($condition) || $condition != "") {
            $this->db->where($condition);
        }
        if (!empty($limit)) {
            $this->db->limit($limit['limit'], $limit['start']);
        }
        if (!empty($order)) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by('created', 'DESC');
        }
        $data = $this->db->get("events");
        if ($with_num_rows == true) {
            $num_rows = $this->db->select('id')->where(!empty($condition) ? $condition : 1, TRUE)->count_all_results("events");
            return (object) array("data" => $data, "num_rows" => $num_rows);
        }
        return $data;
    }

    public function getById($id) {
        if (is_integer($id) && $id > 0) {
            $result = $this->db->select("events.*")
                    ->get_where("events", array("id" => $id));
            return $result->num_rows() > 0 ? $result->row() : null;
        }
        return false;
    }

    public function getBySlag($type = "") {
        if ($type != "") {
            $result = $this->db->select("events.*")
                    ->get_where("events", array("slug" => $type, "status" => 1));
            return $result->num_rows() > 0 ? $result->row() : null;
        }
        return false;
    }

}

?>
