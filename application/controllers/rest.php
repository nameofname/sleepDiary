<?php

require(__DIR__ . '/Auth_Controller.php');

/**
 * Class Rest - handles RESTFul requests.
 * REST URIs should look like this:
 * /rest/model/123/sub-list/456
 */
class Rest extends Auth_Controller {

    protected $method;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->_get_put_post();
        $this->_get_search_params();

        parent::__construct();
    }

    /**
     * For each request do the following :
     *      - Determine the model type from the URL
     *      - Load the model with a little CI magic
     *      - Determine the method type from the URL and the request type.
     *      - Invoke the model method and die with the output.
     */
    public function index () {
        $model = $this->get_model();

        $this->load->model($model);

        $method = strtolower($this->method);

        $data = ($method === 'get') ? $this->search_params : $this->put_post_params;

        $res = $this->$model->$method($data);

        die(json_encode($res));
    }

    /**
     * Gets the model name from the 2nd URL segment.
     * @return mixed
     */
    private function get_model () {
        return $this->uri->segments[2];
    }

    /**
     * Gets request data
     */
    private function _get_put_post() {
        // Get the PUT and POST parameters to pass to the model layer for data persistence requests.
        if ($this->method == 'PUT') {

            $params = json_decode(file_get_contents('php://input'));

            // TODO : is this correct in all cases??????!!!!!!!!!!
            if (!$params) {
                $this->invalid_request('invalid_json');
            } else {
                $params = get_object_vars($params);
            }

            $this->put_post_params = $params;

        } else if ($this->method == 'POST') {

            $params = json_decode(file_get_contents('php://input'));

            // Now assign the found params to $this->put_post_params
            if ($params) {
                $params = get_object_vars($params);
                $this->put_post_params = $_POST ? $_POST : $params;
            } else {
                $this->put_post_params = null;
            }
        }
    }

    private function _get_search_params() {
        $this->search_params = $_GET;
    }

}

