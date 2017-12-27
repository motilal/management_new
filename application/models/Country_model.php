<?php 
/* 
 * @author Motilal Soni
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Country_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_list($condition = array(), $order = array()) {
        $this->db->select("countries.*");
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        if (!empty($order)) {
            $this->db->order_by($order[0], $order[1]);
        }
        $data = $this->db->get("countries");
        return $data;
    }

    public function getById($id) {
        if (is_integer($id) && $id > 0) {
            $result = $this->db->select("countries.*")
                    ->get_where("countries", array("id" => $id));
            return $result->num_rows() > 0 ? $result->row() : null;
        }
        return false;
    }

}

?>
