<?php


/**
 * Class Base_Model - contains logic for returning basic queries, pagination.
 */
class Base_Model extends \CI_Model {

    function __construct() {
        parent::__construct();
    }

    protected function return_one ($string_query) {
        $query = $this->db->query($string_query);
        $data = $query->row();
        $out = new stdClass();
        $out->result = $data;
        return $out;
    }

    protected function return_all ($string_query) {
        $query = $this->db->query($string_query);
        $data = $query->result_array();
        $out = new stdClass();
        $out->result = $data;

        return $out;
    }

    /**
     * @param $string_query - the query to be run in string form - DOES NOT INCLUDE pagination vars
     * @param $offset - current offset
     * @param $limit - current limit
     * @return stdClass
     */
    protected function return_page ($string_query, $offset, $limit) {

        $count_query = $this->db->query($string_query);
        $query = $this->db->query($string_query . " limit $limit offset $offset");

        $count = count($count_query->result_array());
        $data = $query->result_array();
        $out = new stdClass();
        $out->result = $data;
        $size = count($out->result);

        $out->offset = $offset;
        $out->limit = $limit;
        $out->size = $size;
        $out->totalSize = $count;

        return $out;
    }

}
