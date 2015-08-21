(function () {
    "use strict";

    if (!app.currUserData) {
        throw new Error('You may not be on this page without current user data.');
    }

    var user = new app.User(app.currUserData);
    var id = document.location.pathname.split('/').pop();
    app.day = new app.Day();

    var _renderView = function () {
        app.view = new app.UpdateDayView({
            model : app.day
        }).render();
        $('#JsContent').append(app.view.$el);
    };

    var dfd = app.day.fetch({
        data : {
            id : id
        }
    });

    $.when(dfd)

        // On success, render the diary page :
        .done(function () {
            _renderView();
        })

        // On fail, show an error in the form of an alert view.
        .fail(function () {
            app.helpers.showError();
        });

})();
