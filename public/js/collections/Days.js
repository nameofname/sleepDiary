(function () {
    "use strict";

    app.Days = Backbone.Collection.extend({
        url : '/rest/day',
        model : app.Day,
        parse : function (data) {
            return app.BaseModel.prototype.parse.apply(this, arguments);
        }
    });

})();