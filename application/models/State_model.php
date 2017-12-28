<?php

/*
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class State_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_list($condition = array(), $limit = array(), $order = array(), $with_num_rows = false) {
        $this->db->select("states.*,countries.name as country_name");
        if (!empty($condition) || $condition != "") {
            $this->db->where($condition);
        }
        if (!empty($limit)) {
            $this->db->limit($limit['limit'], $limit['start']);
        }
        if (!empty($order)) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by('states.id', 'DESC');
        }
        $this->db->join("countries", "countries.id = states.country_id", "left");
        $data = $this->db->get("states");
        if ($with_num_rows == true) {
            $num_rows = $this->db->select('id')->join("countries", "countries.id = states.country_id", "left")
                    ->where(!empty($condition) ? $condition : 1, TRUE)
                    ->count_all_results("states");
            return (object) array("data" => $data, "num_rows" => $num_rows);
        }
        return $data;
    }

    public function getById($id, $join = false) {
        if (is_numeric($id) && $id > 0) {
            if ($join) {
                $this->db->select("states.*,countries.name as country_name");
            } else {
                $this->db->select("states.*");
            }
            $this->db->where(array("states.id" => $id));
            if ($join) {
                $this->db->join("countries", "countries.id = states.country_id", "left");
            }
            $result = $this->db->get('states');
            return $result->num_rows() > 0 ? $result->row() : null;
        }
        return false;
    }

}

?>
