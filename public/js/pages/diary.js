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

            app.wrapper = new app.DiaryWrapperView({
                model : user,
                collection : app.days
            }).render();

            $('#JsContent').append(d.$el);
        })
        .fail(function () {
            alert('its sooooo bad!');
        });

})();