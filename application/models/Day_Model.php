<?php

//use Helpers\SessionInstance;

class Day_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Create a new DAY model. Note that on create, no sleep time information is filled out, only the row is created
     * and the user id associated with the new row.
     * All sleep information will be set to the default of 'awake'
     */
    public function create ($data) {
        $insert_data = array(
            'user_id' => $data->user_id
        );

        $this->db->insert('day', $insert_data);
        $id = $this->db->insert_id();
        $q = "Select * from user where id = $id";
        $query = $this->db->query($q);
        return $query->row();
    }

}