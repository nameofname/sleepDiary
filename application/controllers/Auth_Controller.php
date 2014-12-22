<?php

//use Libraries\Session;
use helpers\SessionInstance;
//use User_Model;
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

        $session = SessionInstance::getInstance();
        $this->session = $session->read_session();

        // If there is no session, then we know there is no user, set the user to null.
        if (!$this->session) {
            $this->curr_user = null;

            // Otherwise, get the user from the user model based on the session.
        } else {
            // TODO !!!!! if no token the user is null.
            $this->load->model('User_Model');
            $this->curr_user = $this->User_Model->by_session($this->session);
        }
    }

}

