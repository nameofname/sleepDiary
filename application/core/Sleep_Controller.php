<?php

class Sleep_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Sends JSON output to the client.
     * @param $data
     * @param int $http_code
     */
    protected function _send_output ($data, $http_code=200) {
        $status_msg = ($http_code === 200) ? 'success' : 'error';

        $this->output
            ->set_status_header($http_code, $status_msg)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

}
