<?php

$js_files = array(
    '/public/js/libraries/bbc/BaseView.js',
    '/public/js/libraries/bbc/modules/AlertView.js',
    '/public/js/libraries/bootstrap-datepicker.js',
    '/public/js/helpers/ShowError.js',
    '/public/js/models/User.js',
    '/public/js/models/Time.js',
    '/public/js/models/Day.js',
    '/public/js/collections/Days.js',
    '/public/js/views/TimeView.js',
    '/public/js/views/UpdateDayView.js',
    '/public/js/pages/edit.js',
);

$css_files = array(
    '/public/css/diary.css',
    '/public/css/datepicker.css',
);

include(dirname(__FILE__) . '/../html_templates/MyDiary.html');
include(dirname(__FILE__) . '/../../../public/js/libraries/bbc/html/modal-alert.html')  ;
