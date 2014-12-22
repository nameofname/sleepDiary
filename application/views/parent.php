<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title; ?></title>
    <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="/public/css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="/public/css/font-awesome/font-awesome.min.css" />

</head>

<body>

<div id="wrapper" class='container'>

    <div class="container">
        <div class="row">
            <div id="JsContent" class="col-xs-12">
                <?php include($page_file) ?>
            </div>
        </div>
    </div>

</div>

<!--Set up main app object : -->
<script type='text/javascript'>
    var app = {};
</script>

<?php
// Now include all the JS :
// Default JS is here.
$default_js_files = array(
    'https://code.jquery.com/jquery-2.1.3.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/backbone-associations/0.6.2/backbone-associations-min.js'
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
