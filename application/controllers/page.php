<?php

// TODO: use auto-loading!!!
require(__DIR__ . '/Auth_Controller.php');

class Page extends Auth_Controller {

    // Data to be passed to the view.
    public $data;

    // An associative array of titles for each of the pages I maintain:
    public $titles = array(
        'home' => "Home | Sleep Diary",
        'my_diary' => "My Diary | Sleep Diary",
        'not_found' => "Not found (404)"
    );

    public function __construct() {
        parent::__construct();
    }

    /**
     * This is just here to connect to the CI default controller. I don't want to do anything other than hit the index
     * function ever in here, so just do that.
     */
    public function home() {
        if ($this->curr_user) {
            die(header('Location: /my_diary'));
        }

        // Serve up the home page.
        return $this->_create_page('home');
    }

    public function my_diary() {
        // if not logged in go to the home page
        if ($this->curr_user === null) {
            die(header('Location: /home'));
        }

        // Serve up the diary page.
        return $this->_create_page('my_diary');
    }

    /**
     * Gets any page. I only have one route for serving pages and it's super simple.
     */
    public function _create_page($page=null) {

        $views_dir = APPPATH . 'views/';

        // Check if the reuqested file exists. If not, throw up the 404 page.
        $page_file = 'pages/'.$page.'.php';
        $parent_page = 'parent.php';

        if (file_exists($views_dir . $page_file)) {

            if (!isset($this->titles[$page])) {
                throw new ErrorException('Page title is required to configure a new page.');
            }

            $this->data['curr_user'] = $this->curr_user;
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

}
