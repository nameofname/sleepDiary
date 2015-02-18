<?php

//namespace Models;
require(__DIR__ . '/Base_Model.php');

class Day extends Base_Model {

    function __construct() {
        parent::__construct();
    }

    public function get ($data) {
        if (isset($data['id'])) {
            return $this->get_by_id($data['id']);
        } elseif (isset($data['user_id'])) {
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
            'user_id' => $data['user_id'],
            'date' => $data['date']
        );

        $this->db->insert('day', $insert_data);

        $id = $this->db->insert_id();
        $q = "Select * from day where id = $id";
        $query = $this->db->query($q);
        return $query->row();
    }

    public function put ($data) {
        // Assert that the passed user data contains an ID (updates must be to existing records)
        if (!$data['id']) {
            throw new ErrorException('Cannot update a day record without and ID or token.');
        }

        // Update all the fields except ID :
        $id = $data['id'];
        unset($data['id']);

        foreach ($data as $key=>$val) {
            $this->db->set($key, $val);
        }

        $this->db->where('id', $id);

        $query = $this->db->update('day');

        if(!$query) {
            throw new ErrorException('Day model update failed.');
        } else {
            return $this->get_by_id($id);
        }
    }

    private function get_by_uid ($id) {
        $query = $this->db->query("Select * from day where user_id = '$id'");
        return $query->result_array();
    }

    private function get_by_id ($id) {
        $query = $this->db->query("Select * from day where id = '$id'");
        return $query->row();
    }

    /**
     * Private method to validate a new Day posting. 2 Days of the same date cannot be created for the same user :
     * @param $data
     * @return int
     */
    public function find_duplicates ($data) {
        $id = $data['user_id'];
        $date = $data['date'];
        $str = "Select * from day where user_id = '$id' and date = '$date'";
        $query = $this->db->query($str);

        return count($query->result_array());
    }
}