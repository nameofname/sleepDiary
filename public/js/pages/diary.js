(function () {
    "use strict";

    if (!app.currUserData) {
        throw new Error('You may not be on this page without current user data.');
    }

    var user = new app.User(app.currUserData);

    var d = new app.DiaryWrapperView({
        model : user
    });

})();