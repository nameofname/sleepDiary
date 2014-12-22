<?php

// TODO: use auto-loading!!!
require(__DIR__ . '/Auth_Controller.php');

class Page extends Auth_Controller {

    // Data to be passed to the view.
    public $data;

    // An associative array of titles for each of the pages I maintain:
    public $titles = array(
        'home' => "Home | Sleep Diary",
        'diary' => "My Diary | Sleep Diary",
        'not_found' => "Not found (404)"
    );

    public function __construct() {
        parent::__construct();

        // Next, attempt to create the user.
//        if (!$this->curr_user) {
//            // First if $this->user does not exist then load the model
//            if (!isset($this->user)) {
//                $this->user = new \Models\User();
//            }
//
//            // Now create the token and the user.
//            $token = $this->session->create_session();
//            $this->curr_user = $this->user->create($token);
//
//        }
//
//        // Strip the PW out of the user sent to the client. Because that would be really stupid.
//        unset($this->curr_user->pw);
//        $this->data['user'] = $this->curr_user;
    }

    /**
     * This is just here to connect to the CI default controller. I don't want to do anything other than hit the index
     * function ever in here, so just do that.
     */
    public function home() {
        return $this->index('home');
    }

    /**
     * Gets any page. I only have one route for serving pages and it's super simple.
     */
    public function index($page=null) {

        $views_dir = APPPATH . 'views/';

        // Check if the reuqested file exists. If not, throw up the 404 page.
        $page_file = 'pages/'.$page.'.php';
        $parent_page = 'parent.php';

        if (file_exists($views_dir . $page_file)) {

            if (!isset($this->titles[$page])) {
                throw new ErrorException('Page title is required to configure a new page.');
            }
            $this->data['title'] = $this->titles[$page];
            $this->data['page_file'] = $page_file;
            $this->load->view($parent_page, $this->data);

        } else {
            $this->output->set_status_header('404');
            $this->data['title'] = $this->titles['not_found'];
            $this->data['page_file'] = 'pages/not_found.php';
            $this->load->view($parent_page, $this->data);
        }
	}

//    public function css ($file) {
//        $this->load->view("css/$file");
//    }

}
