(function () {
    "use strict";

    if (!app.currUserData) {
        throw new Error('You may not be on this page without current user data.');
    }

    var user = new app.User(app.currUserData);
    var dfd;
    var _renderWrapper = function () {
        app.wrapper = new app.DiaryWrapperView({
            model : user,
            collection : app.days
        }).render();
        $('#JsContent').append(app.wrapper.$el);
    };

    app.days = new app.Days();

    dfd = app.days.fetch({
        data : {
            user_id : user.get('id')
        }
    });

    $.when(dfd)

        // On success, render the diary page :
        .done(function () {
            _renderWrapper();
        })

        // On fail, show an error in the form of an alert view.
        .fail(function () {
            _renderWrapper();
            app.wrapper.showError();
        });

})();
