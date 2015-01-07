(function () {
    "use strict";

    app.Days = Backbone.Collection.extend({
        url : '/rest/day',
        model : app.Day
    });

})();