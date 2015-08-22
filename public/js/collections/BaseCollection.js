(function () {
    "use strict";

    /**
     * Base collection provides parse method and basic pagination logic.
     * @type {void|*}
     */
    app.BaseCollection = Backbone.Collection.extend({

        pageSize : 10,
        limit : 0,
        totalSize : 0,
        offset : -10, // starting at negative number because it will be incremented every time getNextPage is called.

        parse : function (data) {
            this.limit = parseInt(data.limit, 10) || 0;
            this.offset = parseInt(data.offset, 10) || 0;
            this.totalSize  = parseInt(data.totalSize, 10) || 0;
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
        },

        /**
         * Makes GET request for the next page. Data passed as the data argument will be extended with pagination
         * variables.
         * @param data - object to be extended onto fetch object.
         * @returns {*}
         */
        getNextPage : function (data) {
            this.limit = parseInt(this.limit, 10) + parseInt(this.pageSize, 10);
            this.offset = parseInt(this.offset) + parseInt(this.pageSize, 10);

            debugger;
            data = _.extend(data || {}, {
                limit : this.limit,
                offset : this.offset
            });

            return this.fetch({
                data : data
            });
        },

        getCurrentPageNum : function () {
            return (this.offset + this.pageSize) / this.pageSize;
        },

        getTotalPageNum : function () {
            return Math.ceil(this.totalSize / this.pageSize);
        }

    });

})();