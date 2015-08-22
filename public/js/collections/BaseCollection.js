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
            this.limit = data.limit || null;
            this.offset = data.offset || null;
            this.totalSize  = data.totalSize || null;
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
            this.limit += this.pageSize;
            this.offset += this.pageSize;

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