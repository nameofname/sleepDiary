<nav class="navbar navbar-inverse">
    <div class="container-fluid">

        <div class="navbar-header">
            <a class="navbar-brand" href="/">My Sleep Diary</a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <?= $curr_user ? '<a href="/login/logout">Logout</a>' : ''?>
                </li>
            </ul>
        </div>

    </div>

</nav>

<!--<div class="row">-->
<!--    <div id="navigash" class="col-xs-12" style="background-color: #ccc">-->
<!--            <a class="btn btn-link" href="/">-->
<!--                <h3>-->
<!--                My Sleep Diary-->
<!--                </h3>-->
<!--            </a>-->
<!--        <a class="pull-right" href="/login/logout">Logout</a>-->
<!--    </div>-->
<!--</div>-->
