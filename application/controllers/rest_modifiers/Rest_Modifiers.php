<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ronald
 * Date: 7/3/13
 * Time: 7:36 PM
 * To change this template use File | Settings | File Templates.
 *
 * The Rest_Modifiers class is used to create functionality that gets invoked before hitting a public method on a rest
 * formatted model. The most common usage should be to add validation to REST endpoints for a model before updating.
 */

use Models\User;
use Libraries\Session;
use Models\Comment;

// TODO ::: VALIDATION RULES SHOULD NOT JUST BE AN ARRAY -- IT SHOULD BE A CLASS.
// todo ::: perhaps I will no longer need to when I rebuild the whole back end in Node.js ???
/**
 * Class Rest_Modifiers
 *
 * Define validation rules in the adjacent directory.
 * TODO !!! DEFINE REQUIREMENTS FOR VALDATION RULES HERE!!! (they need to be refactored too so that they are classes
 *      and not just required arrays).
 *
 */
class Rest_Modifiers {

    // A reference to the model that is going to be
    public $model;

    // The method to be invoked on the model
    public $model_method;

    // The params passed from the client.
    public $params;

    // The result of validation, either bool true, or Validation_Error
    public $validation_result;

    // The name of the rest method (on the model) that we are validating for. Contains model name (ex. "user_update")
    private $method_name;

    // Private variable denoting I should use the custom validation code, not generic validation.
    private $use_custom = false;

    /**
     * Note that the Rest_Modifiers Class doesn't want to know about the http method, or anything else. The regular
     * REST controller figures out which method to invoke on the model already, all Rest Modifiers do is to do some
     * function before that method is called.
     *
     * The most common case for functions to be run before invoking a REST endpoint is validaiton. This class provides
     * an easy way to set up validation for the function in question, simply include a validation JSON file inside of
     * the "validation_json" folder in this directory. The rules will be parsed out and applied to the input
     * automatically. Check out the README inside of the validation_json folder to see how to write validation JSON.
     *
     * The second way to do this is to create a custom function to be run. The function name must be the name of the
     * model, concatonated with the method name on the model. For example if you want to do custom validation before
     * calling the "update" method on the "user" model, then create a function called "user_update". The custom function
     * must return an instance of class "Validation_Error" - or boolean true (if all is well).
     *
     * @param $params
     * @param $model
     * @param $model_method
     */
    public function __construct($params, $model, $model_method) {
        $this->params = $params;
        $this->model_method = $model_method;
        $this->model = $model;

        $this->method_name = $this->model . '_' . $this->model_method;

        // If there is a custom validation function to be run on the current endpoint, then run that before validation:
        // How to create a custom method?
        $custom_modifier_exists = method_exists($this, $this->method_name);
        if ($custom_modifier_exists) {

            // TODO : abstract methods away into individual files, like the validation rules.
            $this->use_custom = true;

        }

    }

    /**
     * Finds the validation file (if exists) - and runs through all the validation logic, returning either boolean
     * true, or an instance of class Validation_Error
     * @return bool|Validation_Error
     */
    public function validate() {

        // If the programmer wrote custom validation logic for this endpoint - then use that. Otherwise, the generic
        // regex/requirement based validation will be used:
        if ($this->use_custom) {
            return call_user_func(array($this, $this->method_name));
        }

        // This class will automatically find validation files associated with the particular endpoint, and run
        // validation including required fields and provided regular expressions:
        // Note: looks for a JSON file named with the model, and the method name, concatted with an underscore.
        $validation_file_name = __DIR__ . '/validation_rules/' . $this->method_name . '.php';

        if (file_exists($validation_file_name)) {

            // Not using auto-loading - because I just use arrays inside of the loaded files, not classes.
            include_once($validation_file_name);

            // If the validation variable is defined in the included PHP, then run the validation function.
            if (isset($validation_rules)) {
                $this->validation_result = $this->_run_validation($validation_rules);

            } else {
                // If no validation rules were required in the included PHP file, just set validation_result to true:
                $this->validation_result = true;
            }

        } else {
            $this->validation_result = true;
        }

        return $this->validation_result;
    }

    /**
     * Runs the validation using included PHP - matching each input value against the passed params.
     * @param $rules
     * @throws ErrorException
     * @return bool|Validation_Error
     */
    private function _run_validation($rules) {

        // To run the validation, compare the fields in the validation rules to the fields in the $params
        foreach ($rules as $field_name => $field_rules) {

            // If the current rule is a function, then run that function :
            if (isset($field_rules['custom']) && $field_rules['custom'] === true ) {
                if (gettype($field_rules['validate']) === 'object') {
                    $result = $field_rules['validate']();
                    if ($result instanceof Validation_Error) {
                        return $result;
                    }
                } else {
                    throw new ErrorException('Validations that are designated as "custom" must have a "validate" property with a closure to do validation. ');
                }
            }

            // Get the value of the field you are testing:
            $field_value = isset($this->params[$field_name]) ? $this->params[$field_name] : null;

            // First, let's check if the field is required:
            if (isset($field_rules['required']) && $field_rules['required']) {

                // If so, let's return some error:
                if (!$this->_check_field_is_set($field_value)) {
                    $error = new Validation_Error(array(
                        "status_text" => 'required_field',
                        "field_name" => $field_name
                    ));
                    return $error;
                }
            }

            // If no error was returned there, then let's check this field against each regex expected of it.
            if (isset($field_rules['rules'])) {
                foreach ($field_rules['rules'] as $name=>$regex) {

                    // Check the field value against the current regex rule:
                    if (!$this->_check_field_rule($field_value, $regex)) {

                        // In this case there was an error, so look up the correct error message from the JSON:
                        $message = $field_rules['messages'][$name];

                        $error = new Validation_Error(array(
                            "status_text" => 'regex_failed',
                            "field_name" => $field_name,
                            "message" => $message
                        ));

                        return $error;
                    }
                }
            }
        }

        // If none of the above cases get caught, then return boolean true:
        return true;
    }

    /**
     * Used for required fields to check and see if the specified field was set:
     * @param $field_value <string> - The value of the field found above.
     * @return bool
     */
    private function _check_field_is_set($field_value) {
        // Look at the passed params,
        if ($field_value) {

            // Trim string inputs :
            if (gettype($field_value) === 'string') {
                $field_value = trim($field_value);
                return $field_value !== '';

            } elseif (gettype($field_value) === 'array') {
                return count($field_value);
            }

        }
        return false;
    }

    /**
     * Tests a field value against a single rule using preg match.
     * @param $field_value
     * @param $rule
     * @return bool
     */
    private function _check_field_rule($field_value, $rule) {
        // Run regex based on regex string using preg_match:
        $test = preg_match($rule, $field_value);

        return $test ? true : false;
    }


    /**
     * When a comment is updated, first load the user model and make sure that the user in question is the currently
     * logged in user. First get the user by their session, then get the comment in question by the comment ID passed,
     * then validate the session user's ID is the same as the passed user_id on the comment.
     *
     * *** Note : I get the comment and validate against the user ID on the comment from the model, not the client,
     * because a hacker could modify the user_id before sending to the server via ajax. This way, they can modify the
     * comment ID - but they can only tamper with comments they posted.
     */
    public function comment_update() {
        // TODO ::: MOVE THIS METHOD ONTO THE COMMENT UPDATE VALIDATION RULES.
        $user_model = new User();
        $session = new Session();
        $comment_model = new Comment();

        $comment_id = $this->params['id'];
        $the_comment = $comment_model->get_one($comment_id);

        $session = $session->read_session();

        $the_user = $user_model->by_session($session);

        if ($the_user->id !== $the_comment->user_id) {

            $error = new Validation_Error(array(
                "status_text" => 'not_allowed',
                "http_code" => 401,
                "message" => "You cannot update someone else's comment"
            ));

            return $error;
        }

        return true;
    }

}

/**
 * Class Validation_Error
 * ... An error class to return to the
 */
class Validation_Error {

    // Status text is the status text that will be propagated to the client.
    public $status_text = 'error';

    // The field that validation bombed out on.
    public $field_name;

    // The field that validation bombed out on.
    public $message;

    // The default http code is 400.
    public $http_code = 400;

    public function __construct($options) {

        $this->status_text = isset($options['status_text']) ? $options['status_text'] : null;
        $this->field_name = isset($options['field_name']) ? $options['field_name'] : null;
        $this->http_code = isset($options['http_code']) ? $options['http_code'] : 400;

        if (!isset($options['message'])) {

            if ($this->field_name && $this->status_text == 'required_field') {
                $field_name = $this->field_name;
                $this->message = "The field $field_name is required";
            }

        } else {
            $this->message = $options['message'];
        }
    }
}

