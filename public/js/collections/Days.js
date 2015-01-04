(function () {
    "use strict";

    app.Days = new Backbone.Collection.extend({
        url : '/soa/days',
        model : app.Day
    });

})();