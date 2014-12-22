<?php

use Helpers\SessionInstance;

class User_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new user based on some passed data. Should include :
     *      - name
     *      - email
     *      - pw
     * *** The 'token' field will be automatically generated.
     * @param $data
     * @return mixed
     */
    public function create ($data) {
        $token = $this->_new_token();
        $default = array(
            'token' => $token
        );
        $data = array_merge($data, $default);
        $this->db->insert('user', $data);
        $id = $this->db->insert_id();
        $query = $this->db->query("Select * from user where id = $id");
        return $query->row();
    }

    public function read ($data) {
        //
    }

    public function update ($data) {
        //
    }

    /**
     * Generates a new token, excludes any token that already exists in the DB :
     * @return string
     */
    private function _new_token () {
        $session = SessionInstance::getInstance();
        $token = $session->create_session();
        $query = $this->db->query("Select * from user where token = $token");

        while ($query->num_rows() > 0) {
            $token = $session->create_session();
            $query = $this->db->query("Select * from user where token = $token");
        }

        return $token;
    }

}