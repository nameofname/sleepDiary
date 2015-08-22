<?php


/**
 * Class Base_Model - contains logic for returning basic queries, pagination.
 */
class Base_Model extends \CI_Model {

    function __construct() {
        parent::__construct();
    }

    protected function return_one ($query) {
        $data = $query->row();
        $out = new stdClass();
        $out->result = $data;
        return $out;
    }

    protected function return_all ($query) {
        $data = $query->result_array();
        $out = new stdClass();
        $out->result = $data;

        return $out;
    }

    protected function return_page ($query, $offset, $limit) {
        $data = $query->result_array();
        $out = new stdClass();
        $out->result = $data;
        $size = count($out->result);

        $out->offset = $offset;
        $out->limit = $limit;
        $out->size = $size;

        return $out;
    }

}
