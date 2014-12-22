<div class="page-header">
    <h1>
        Welcome to the free online sleep diary
    </h1>
    <h1>
        <small>
            A <mark>simple free</mark> place to keep track of your <mark>sleep habits</mark>.
        </small>
    </h1>
</div>

<p class="lead">
    Enter your user name and password to login, or create a new account.
</p>
<p>
    Don't worry, your information will never be shared, it's simply used to keep track of your data in our system.
    From here you can fill out your sleep diary in our easy to use online format.
    It's handier than using a peice of paper, and we'll store the info for you to look up or modify later.
</p>

<form role="form" action="/rest/user" method="POST">
    <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
    </div>
    <button type="submit" class="btn btn-default">Login</button>
</form>


<?php
$js_files = array(
    '/public/js/pages/home.js'
);