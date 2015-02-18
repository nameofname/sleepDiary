<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title; ?></title>
    <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css" />
<!--    <link rel="stylesheet" type="text/css" href="/public/css/bootstrap-responsive.css" />-->
    <link rel="stylesheet" type="text/css" href="/public/css/font-awesome/font-awesome.min.css" />
    <?php include($page_file); ?>

    <?php
    // Include CSS files specified in page file :
    if (isset($css_files)) {
        foreach ($css_files as $file) {
            echo "<link rel='stylesheet' type='text/css' href='$file' />";
        }
    }
    ?>

</head>

<body>

<?php include(__DIR__ . '/header.php'); ?>

<div id="wrapper" class='container'>

    <div class="row">
        <div id="JsContent" class="col-xs-12">
        </div>
    </div>

</div>

<?php include(__DIR__ . '/footer.php'); ?>

<!--Set up main app object : -->
<script type='text/javascript'>
    var app = {};
    app.currUserData = <?= json_encode($curr_user); ?>;
</script>

<?php
// Now include all the JS :
// Default JS is here.
$default_js_files = array(
//    'https://code.jquery.com/jquery-2.1.3.min.js',
//    'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js',
//    'https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js',
//    'https://cdnjs.cloudflare.com/ajax/libs/backbone-associations/0.6.2/backbone-associations-min.js'
    '/public/js/libraries/jquery-2.1.3.min.js',
    '/public/js/libraries/underscore-min.js',
    '/public/js/libraries/backbone-min.js',
    '/public/js/libraries/backbone-associations-min.js'
);

if (isset($js_files)) {
    $files = array_merge($default_js_files, $js_files);
} else {
    $files = $default_js_files;
}

foreach ($files as $file) {
    echo "<script type='text/javascript' src='$file'></script>";
}

?>

</body>
</html>
