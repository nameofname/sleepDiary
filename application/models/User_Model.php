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
        $token = $this->get_unused_token();
        $default = array(
            'token' => $token
        );
        $data = array_merge($data, $default);
        $this->db->insert('user', $data);
        $id = $this->db->insert_id();
        $q = "Select * from user where id = $id";
        $query = $this->db->query($q);
        return $query->row();




        // TODO ::: USe the standard insert?
        // TODO ::: Use db->escape on all inserted data.
//        $sql = "INSERT INTO mytable (title, name)
//        VALUES (".$this->db->escape($title).", ".$this->db->escape($name).")";
//
//        $this->db->query($sql);
//
//        echo $this->db->affected_rows();
    }

    public function by_id ($id) {
        $query = $this->db->query("Select * from user where id = '$id'");
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


    /**
     * Simple update method will update all the fields of the user.
     * NOTE* You must pass all fields included in the user or else they may be omitted from the update.
     *
     * @param $user_data
     * @return mixed
     * @throws ErrorException
     */
    public function update ($user_data) {
        // Assert that the passed user data contains an ID (updates must be to existing records)
        if (!$user_data->id || !$user_data->token) {
            throw new ErrorException('Cannot update a user record without and ID or token.');
        }

        // EMAIL cannot be updated :
        $this->db->set('name', $user_data->name);
        $this->db->set('password', $user_data->password);
        $this->db->set('token', $user_data->token);

        $query = $this->db->update('user');

        if(!$query) {
            throw new ErrorException('User update failed.');
        } else {
            return $this->by_id($user_data->id);
        }
    }

    /**
     * Generates a new session token, excludes any token that already exists in the DB :
     * @return string
     */
    public function get_unused_token () {
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