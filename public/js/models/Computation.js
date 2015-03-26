(function () {
    "use strict";

    /**
     * A computation model runs queries via the computation endpoint.
     * Each computation must have a "query" associated (ie. "average_sleep_time")
     * @type {void|*}
     */
    app.Computation = app.BaseModel.extend({

        url : '/rest/computation',

        fetch : function (options) {
            if (!options.data.query) {
                throw new Error('you must specify a query when attempting to fetch with computation model');
            }
            return app.Computation.__super__.fetch.apply(this, arguments);
        }
    });

})();