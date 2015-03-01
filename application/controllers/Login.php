<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ronald
 * Date: 6/17/13
 * Time: 8:00 PM
 * To change this template use File | Settings | File Templates.
 */

require(__DIR__ . '/Auth_Controller.php');
use Helpers\SessionInstance;

class Login extends Auth_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model', 'user_model');
        $this->session = SessionInstance::getInstance();
    }

    /**
     * Login is done over ajax (unsecure I know) and returns the user info on success.
     */
    public function login () {
        $data = $this->_get_data();
        $existing_user = $this->user_model->by_login($data);

        if ($existing_user) {
            // Update the user's session token to a new (unused) token :
            $new_token = $this->user_model->get_unused_token();
            $existing_user->token = $new_token;
            $this->user_model->put($existing_user);

            // Set the session on the client machine :
            $this->session->set_session($new_token);
            return $this->_send_output($existing_user);

        } else {
            $error = new stdClass();
            $error->status = 'login_failed';
            $error->message = 'Your login failed. Please try again with different credentials.';
            return $this->_send_output($error, 400);
        }
    }

    /**
     * Register a new user. First check to see if an existing user with the same email exists :
     */
    public function register () {
        $data = $this->_get_data();

        if ($data === 'missing_data') {
            $error = new stdClass();
            $error->status = $data;
            $error->message = 'An email address and a password are required to create an account.';
            return $this->_send_output($error, 400);
        }

        $existing_user = $this->user_model->by_email($data['email']);
        if ($existing_user)
        {
            $error = new stdClass();
            $error->status = 'existing_user';
            $error->message = 'A user with this email already exists. Please try again with another email or login.';
            return $this->_send_output($error, 400);
        }

        // The use model "post" method will generate a user with a new (unused) token.
        $new_user = $this->user_model->post($data);

        // Set the session on the client machine :
        $this->session->set_session($new_user->token);

        return $this->_send_output($new_user);
    }

    /**
     * Logs the user out by deleting their session token and redirecting them to the login page.
     */
    public function logout () {
        $this->load->helper('url');

        // Set the session on the client machine :
        $this->session->destroy_session();

        redirect('/');
    }

    private function _get_data () {
        header('Content-type: application/json');
        $data = json_decode(file_get_contents('php://input'),true);

        if (empty($data['email']) || empty($data['password'])) {
            return 'missing_data';
        }

        return $data ? $data : null;
    }

}
