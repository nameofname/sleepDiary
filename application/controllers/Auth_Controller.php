<?php

use helpers\SessionInstance;
require(APPPATH . 'core/Sleep_Controller.php');

/**
 * Class Auth_Controller - Extends the Core Controller.
 */
class Auth_Controller extends Sleep_Controller {

    // A reference to the user. This is consumed by classes that inherit this base controller.
    public $curr_user;

    // The user's session. This will not exist if the user has not been to the site before, or if the user is a bot.
    public $session;

    public function __construct () {

        parent::__construct();

        // Load the DB class for all controllers under this one :
        $this->load->database();

        // Create a new instance of the Session class, then read the current session token (if there is one) :
        $session = SessionInstance::getInstance();
        $this->session = $session->read_session();

        // If there is no session, then we know there is no user, set the user to null.
        if (!$this->session) {
            $this->curr_user = null;

        // Otherwise, get the user from the user model based on the session.
        } else {
            $this->load->model('User_model', 'user_model');
            $this->curr_user = $this->user_model->by_session($this->session);
        }
    }
}
