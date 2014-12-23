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
        $q = "Select * from user where id = $id";
        $query = $this->db->query($q);
        return $query->row();
    }

    public function by_email ($email) {
        $query = $this->db->query("Select * from user where email = '$email'");
        return $query->row();
    }

    public function by_login ($data) {
        $email = $data['email'];
        $pw = $data['password'];
        $query = $this->db->query("Select * from user where email = '$email' and password = '$pw'");
        return $query->row();
    }

    public function by_session ($token) {
        $query = $this->db->query("Select * from user where token = '$token'");
        return $query->row();
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
        $q = "Select * from user where token = '$token'";
        $query = $this->db->query($q);

        while ($query->num_rows() > 0) {
            $token = $session->create_session();
            $query = $this->db->query("Select * from user where token = $token");
        }

        return $token;
    }

}