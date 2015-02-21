<?php

require(__DIR__ . '/rest.php');

class Day_Controller extends Rest {

    public function __construct() {
        parent::__construct();
    }

    public function write () {
//        return parent::index();

        $this->load->model('day');

        $method = strtolower($this->method);

        $data = ($method === 'get') ? $this->search_params : $this->put_post_params;

        $has_duplicates = $this->day->find_duplicates($data);

        if ($has_duplicates) {
            return $this->_send_output('Cannot create duplicate Day record', 503);
        }

        return $this->index();
    }

}
