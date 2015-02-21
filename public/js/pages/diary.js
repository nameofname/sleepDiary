(function () {
    "use strict";

    if (!app.currUserData) {
        throw new Error('You may not be on this page without current user data.');
    }

    var user = new app.User(app.currUserData);
    var dfd;
    app.wrapper;

    app.days = new app.Days();

    dfd = app.days.fetch({
        data : {
            user_id : user.get('id')
        }
    });

    $.when(dfd)
        .done(function () {

            // Timer functions commented out because the render time is half a fucking second.
            //console.log(new Date().getSeconds(), new Date().getMilliseconds());
            app.wrapper = new app.DiaryWrapperView({
                model : user,
                collection : app.days
            }).render();

            $('#JsContent').append(app.wrapper.$el);
            //console.log(new Date().getSeconds(),  new Date().getMilliseconds());
        })

        // On fail, show an error in the form of an alert view.
            .fail(function () {
            new BBC.AlertView({
                type : 'danger',
                place : $('#JsContent'),
                message : "We're sorry, There was an error when trying to retrieve your data. Please try again."
            }).render();
        });

})();