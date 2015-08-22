(function () {
    "use strict";

    app.Days = app.BaseCollection.extend({

        url : '/rest/day',
        model : app.Day,

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