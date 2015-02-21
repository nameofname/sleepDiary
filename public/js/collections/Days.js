(function () {
    "use strict";

    app.Days = Backbone.Collection.extend({
        url : '/rest/day',
        model : app.Day,
        parse : function (data) {
            return app.BaseModel.prototype.parse.apply(this, arguments);
        },
        /**
         * The comparator for the days collection is the date, forced to ordere descending by multiplying the time in
         * milliseconds by -1
         * @param model
         * @returns {number}
         */
        comparator : function (model) {
            return new Date(model.get('date')).getTime() * -1;
        }
    });

})();