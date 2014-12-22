(function () {
    "use strict";

    var user = new app.User();
    var l = app.loginRegister = new app.LoginRegister({
        model : user
    }).render();

    $('#JsContent').append(l.el);

})();