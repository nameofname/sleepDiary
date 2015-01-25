<?php

require(__DIR__ . '/Auth_Controller.php');

/**
 * Class Rest - handles RESTFul requests.
 * REST URIs should look like this:
 * /rest/model/123/sub-list/456
 */
class Rest extends Auth_Controller {

    private $method;

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

        $method = $this->method;

        $data = ($this->method === 'get') ? $this->search_params : $this->put_post_params;

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





//    // An array of URI segments.
//    private $segments = array();
//    // The number of URI segments:
//    private $num_of_segments;
//    // An internal pointer to the type of model being operated on.
//    private $model;
//    // The request type determines the method that will be executed on the model.
//    // It can be things like create, read, update, delete, create_related, get_related.
//    private $request_type;
//    // IMPPORTANT! The max limit for get requests is 100 records. Requests for more than 100 records will be restricted
//    private $max_request_limit = 100;
//    // Function name is used for times when the function name is not either create, update, get, get_one, or delete.
//    // This includes calls for "create_related" requests, because when you are creating relationships, the funcion
//    // must be specific to the relationship being created
//    private $method_name;
//    // The ID of the entity being modified or searched for. Taken from the request URI.
//    private $entity_id;
//    // Related entity ID is the last segment in the URL for endpoints used to update/delete entity relationships
//    private $related_entity_id;
//    // PUT or POST params :
//    public $put_post_params = null;
//
//    public function __construct(){
//        parent::__construct();
//
//        // If there are filters, AND the filters are not limit or offset, then set the filter_name value, this will
//        // inform certain types of GET requests that a special query is required, not just a normal get() or get_one().
//        $this->search_params = $_GET;
//
//        // Determine method type :
//        $this->method = $_SERVER['REQUEST_METHOD'];
//
//        // Get the put or post params :
//        $this->_get_put_post();
//
//        if (count($this->search_params)) {
//
//            // Make sure the limit and offset do not exceed the maximum.
//            foreach ($this->search_params as $key=>$filter) {
//                if ($key == 'limit' || $key == 'offset') {
//
//                    if ((int)$filter > $this->max_request_limit) {
//                        // If the user is searching for a list with a 'limit' that is greater than the max allowed, kick
//                        // the request out with an error message.
//                        $this->invalid_request('max_limit_exceeded');
//                    }
//                }
//            }
//
//        } else {
//            // If there are no filters, assign filters to an empty array.
//            $this->search_params = array();
//        }
//
//        // If the GET filters did not include a limit and offset, then put it in here (note this will have no effect
//        // for requests to single model endpoints)
//        if (!isset($this->search_params->limit)) {
//            $this->search_params['limit'] = 20;
//        }
//        if (!isset($this->search_params->offset)) {
//            $this->search_params['offset'] = 0;
//        }
//    }
//
//    /**
//     * Entry to the whole controller. Switch on the HTTP method to determine the crud endpoint.
//     */
//    public function request() {
//        $this->num_of_segments = $this->uri->total_segments();
//
//        // If the number of URI segments is greater than 4, kick the request out.
//        if ($this->num_of_segments > 5 || $this->num_of_segments < 1) {
//            $this->invalid_request();
//        }
//
//        // Populate the URI segments.
//        for ($i=1; $i<=$this->num_of_segments; $i++) {
//            $this->segments[] = $this->uri->segment($i);
//        }
//
//        // Then determine the type of model that you are trying to operate on. Switch on the segment number and name.
//        $request_type = $this->determine_request_type();
//
//        // Now that you have the request type, invoke it on the model type:
//        $model_name = $this->determine_model_type();
//
//        // First, run rest modifiers. Usually this is validation, but it can be other custom functions that run before
//        // accessing public methods on the model:
//        $rest_modifiers = new Rest_Modifiers($this->put_post_params, $model_name, $request_type);
//        $validation_result = $rest_modifiers->validate();
//
//        // Check the validation result. If it is an error class, then we are going to return to the client early:
//        if ($validation_result instanceof Validation_Error) {
//
//            // In the case of the validation error, we are going to send a response with an error HTTP code (defaults
//            // to 400 in Validation_Error class) pass along a custom message to the client.
//            $http_code = $validation_result->http_code;
//            $this->invalid_request($validation_result->status_text, $http_code, $validation_result->message, $validation_result->field_name);
//            return;
//        }
//
//        $this->_invoke_model_method($model_name, $request_type);
//
//        $this->send_output();
//    }
//
//    /**
//     * This function takes the model name and the request type, and invokes the corresponding method on the model.
//     * @param $model_name
//     * @param $request_type
//     */
//    private function _invoke_model_method($model_name, $request_type) {
//
//        // Define a new instance of the model dynamically:
//        $model_name = 'Models\\' . ucfirst($model_name);
//        $model = new $model_name();
//
//        // Set the filters on the model:
//        call_user_func(array($model, 'set_params'), $this->search_params, $this->put_post_params);
//
//        // If this is a request to create related (create a relationship between 2 entities) - then we are going to
//        // look for a function name that was the last url segment on the model.
//        if ($request_type == 'create_related' || $request_type == 'update_related') {
//
//            if (method_exists($model, $this->method_name)) {
//
//                // Do the actual request by invoking the "create_related" method:
//                // IMPORTANT! pass the entity ID as the parameter ($this->entity_id) to the create related function!
//                $this->model_response = call_user_func(array($model, $this->method_name), $this->entity_id);
//
//            } else {
//                $this->invalid_request('method_doesnt_exist');
//            }
//        }
//
//        // For "delete_related" requests, follow a logic similar to the "create_related" requests (the method name is
//        // determined below)
//        elseif ($request_type == 'delete_related'){
//
//            if (method_exists($model, $this->method_name)) {
//                $this->model_response = call_user_func(array($model, $this->method_name), $this->entity_id, $this->related_entity_id);
//
//            } else {
//                $this->invalid_request('method_doesnt_exist');
//            }
//        }
//
//        // Check whether a method exists on the model with the same name as the request type. Invoke the method after
//        // calling set_params on the model (passes filters and persistence data to the model for get type requests).
//        elseif (method_exists($model, $request_type)) {
//
//            // TODO: IF THE REQUEST TYPE IS "get_related" -- FIGURE OUT A WAY TO PUT THE RELATED ENTITY NAME ON THE METHOD!!!
//            // Do the actual request by invoking the related method on the model:
//            $this->model_response = call_user_func(array($model, $request_type));
//        } else {
//            $this->invalid_request('method_doesnt_exist');
//        }
//
//    }
//
//    /**
//     * Determines the request type. Should save something like get, create, update... blah blah blah.
//     * TODO: FOR REQUESTS THAT ARE THE WRONG METHOD, USE THE HTTP CODE 405 - "Method Not Allowed" !!!!!
//     */
//    private function determine_request_type() {
//        $n = $this->num_of_segments;
//        $m = $this->method;
//        $last_segment = $this->segments[$n-1];
//        $segment_type = is_numeric($last_segment) ? 'number' : 'string';
//
//        // If the request type is not a GET, then validate the user:
//        // Now that you have the user, do a security check:
//        if ($m != 'GET') {
//            $this->validate_user();
//        }
//
//        // Assign the request type!
//        if ($m == 'GET') {
//            // All endpoint types support GET.
//            switch ($n) {
//                case 2:
//                    $this->request_type = 'get'; break;
//                case 3:
//                    $this->request_type = 'get_one';
//                    // Add the ID to the request filters!
//                    $this->search_params['id'] = $last_segment;
//                    break;
//                case 4:
//                    // Can be getting either a related sub-list or sub-model
//                    $this->request_type = 'get_related'; break;
//                case 5:
//                    // In this case, you are getting a sub-model from a sub-list
//                    $this->request_type = 'get_related'; break;
//            }
//
//        } elseif ($m == 'POST') {
//            // You can only do a create to a list endpoint.
//            if ($segment_type == 'number') {
//                $this->invalid_request('cannot_create');
//            }
//            switch ($n) {
//                case 2:
//                    $this->request_type = 'create'; break;
//                case 3:
//                    // You cannot send a create to a single model
//                    $this->invalid_request('cannot_create'); break;
//                case 4:
//                    // In this case, the request type is "create_related" -
//                    // Segment 2, segment 3 is the entity ID, and segment 4 is the related entity to create.
//                    // The method that will be called on the model will be "create_related_"+RELATED
//                    // eg. "create_related_ingredient"
//                    // The method will be passed the entity ID
//                    // IMPORTANT! This format does not take into account creating many types of relationships between
//                    // 2 entities. TODO: will this change in the future?
//                    $this->method_name = 'create_related_' . $last_segment;
//                    $this->entity_id = $this->segments[$n-2];
//                    $this->request_type = 'create_related'; break;
//                case 5:
//                    // Can't create sub-model
//                    $this->invalid_request('cannot_create'); break;
//            }
//        }
//
//        elseif ($m == 'PUT') {
//            // This is an update, the last uri segment has to be a number... cause you can't update a list.
////            if ($segment_type == 'string') {
////                $this->invalid_request('cannot_update');
////            }
//            switch ($n) {
//                case 2:
//                    // Can't update a list
//                    $this->invalid_request('cannot_update'); break;
//                case 3:
//                    // Updating a single model.
//                    $this->request_type = 'update'; break;
//                case 4:
//                    // Both cases 4 and 5 are updating a related model.
//                    $this->method_name = 'update_related_' . $last_segment;
//                    $this->entity_id = $this->segments[$n-2];
//                    $this->request_type = 'update_related'; break;
//                case 5:
//                    // Removed for the time being, unless later I fidn a use for such long URLs.
//                    // $this->request_type = 'update_related'; break;
//                    $this->invalid_case('cannot_update');
//            }
//        }
//
//        elseif ($m == 'DELETE') {
//            // You can only delete a single model at a time... Kick out requests to list endpoints.
//            if ($segment_type == 'string') {
//                $this->invalid_request('cannot_delete');
//            }
//            switch ($n) {
//                case 2:
//                    $this->invalid_request('cannot_delete'); break;
//                case 3:
//                    $this->request_type = 'delete'; break;
//                case 4:
//                    // TODO: SHOULD THIS CASE EXIST? SOUNDS RISKY! - perhaps if I enforce admin users only?
//                    $this->request_type = 'delete_all_related'; break;
//                case 5:
//                // This method is formatted a lot like the "create_related" method above. The method name is a
//                // composite, and the function is passed the entity ID being operated on - AND it is also passed
//                // the ID of the entity to be deleted from the model
//                $this->method_name = 'delete_related_' . $this->segments[$n-2];
//                $this->entity_id = $this->segments[$n-3];
//                $this->related_entity_id = $last_segment;
//                $this->request_type = 'delete_related'; break;
//            }
//        }
//        return $this->request_type;
//    }
//
//    /**
//     * Determines the model type being acted on. This is the last segment of the URI that is non-numeric.
//     */
//    private function determine_model_type() {
//        for ($i=0; $i <= $this->num_of_segments-1; $i++) {
//            if (!is_numeric($this->segments[$i]) && $this->segments[$i] != 'rest') {
//                $this->model = $this->segments[$i];
//                return $this->model;
//            }
//        }
//        return null;
//    }
//
//    /**
//     * Base route (/rest/) -- just gives info. In the future this might give more info about the API.
//     */
//    public function info(){
//        $this->send_output('hey, what up.');
//    }

}

