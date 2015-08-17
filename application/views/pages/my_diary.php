a<?php
$js_files = array(
    '/public/js/libraries/bbc/BaseView.js',
    '/public/js/libraries/bbc/modules/AlertView.js',
    '/public/js/libraries/bootstrap-datepicker.js',
    '/public/js/models/User.js',
    '/public/js/models/Day.js',
    '/public/js/models/Computation.js',
    '/public/js/collections/Days.js',
    '/public/js/views/DiaryRowView.js', // TODO !!! comment this oout!!!!!!
    '/public/js/views/DaySummaryView.js',
    '/public/js/views/DiaryWrapperView.js',
    '/public/js/pages/diary.js',
);

$css_files = array(
    '/public/css/diary.css',
    '/public/css/datepicker.css',
);

include(dirname(__FILE__) . '/../html_templates/MyDiary.html');
include(dirname(__FILE__) . '/../../../public/js/libraries/bbc/html/modal-alert.html');
