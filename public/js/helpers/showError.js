(function () {

    "use strict";

    app.helpers = app.helpers || {};

    var messages = {
        default : "We're sorry, There was an error when trying to retrieve your data. Please try again.",
        cannot_create_duplicates : "You have already created a record for this date. You may not create 2 records for the same day."
    };

    app.helpers.showError = function (code) {

        new BBC.AlertView({
            type : 'danger',
            place : $('.error-container'),
            message : messages[code] || messages.default
        }).render();
    }

})();


