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

<!--    --><?php //include('includes/nav.php') ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1><?= $title; ?></h1>
                <p>

                    <?php include($page_file) ?>

                </p>
            </div>
        </div>
    </div>

</div>

<?php //include('includes/footer.php') ?>

<!--Include JS files! -->
<?php //include('includes/common_js.php') ?>

<?php
if (isset($js_files)) {
    foreach ($js_files as $file) {
        echo "<script type='text/javascript' src='$file'></script>";
    }
}
?>

</body>
</html>
