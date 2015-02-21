(function () {
    "use strict";

    app.BaseModel = Backbone.Model.extend({
        parse : function (data) {
            return data.result ? data.result : data;
        }
    });

})();