<?php

//namespace Models;
require(__DIR__ . '/Base_Model.php');

class Day extends Base_Model {

    function __construct() {
        parent::__construct();
    }

    public function get ($data) {
        if ($data['user_id']) {
            return $this->get_by_uid($data['user_id']);
        }
    }

    /**
     * Create a new DAY model. Note that on create, no sleep time information is filled out, only the row is created
     * and the user id associated with the new row.
     * All sleep information will be set to the default of 'awake'
     */
    public function post ($data) {
        $insert_data = array(
            'user_id' => $data['user_id']
        );

        $this->db->insert('day', $insert_data);
        $id = $this->db->insert_id();
        $q = "Select * from day where id = $id";
        $query = $this->db->query($q);
        return $query->row();
    }

    private function get_by_uid ($id) {
        $query = $this->db->query("Select * from day where user_id = '$id'");
        return $query->result_array();
    }

}