<?php
$js_files = array(
    '/public/js/libraries/bbc/BaseView.js',
    '/public/js/libraries/bbc/modules/AlertView.js',
    '/public/js/views/LoginRegister.js',
    '/public/js/models/User.js',
    '/public/js/pages/home.js'
);

// Include html templates :
include(dirname(__FILE__) . '/../html_templates/LoginRegister.html');
include(dirname(__FILE__) . '/../../../public/js/libraries/bbc/html/modal-alert.html');
