(function () {
    "use strict";

    var l = app.loginRegister = new app.LoginRegister().render();

    $('#JsContent').append(l.el);

})();