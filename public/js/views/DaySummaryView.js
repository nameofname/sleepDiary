(function () {
    "use strict";

    var enums = app.Day.enums;

    app.DaySummaryView = BBC.BaseView.extend({

        template : _.template($('#Day-summary-template').html(), null, {variable : 'data'}),

        render : function () {
            this.$el.append(this.template(
                _.extend({
                    timeSlept : this.getTimeTotalForState(enums.ASLEEP),
                    timeDozing : this.getTimeTotalForState(enums.DOZING)
                }, this.model.toJSON())
            ));


            // rip through these bitchers : app.DiaryTimeView

            _.each(this.model.getAllTimes(), function (val, key) {
                var obj = {};
                obj[key] = val;
                var newView = new app.DiaryTimeView({
                    model : new app.Time(obj, {parse : true})
                });
                debugger;
                this.$('.time-container').append(newView.render().$el);
            }, this);

            debugger;


            return this;
        },

        /**
         * Gets the total time slept, dozing, or awake that day in hours and minutes according to the passed state.
         * @returns {string}
         */
        getTimeTotalForState : function (state) {
            var minutes = _.reduce(this.model.toJSON(), function (memo, val) {
                return memo += (val === state) ? enums.TIME_INCREMENT : 0;
            }, 0, this);
            var hours = this._formatNumber(Math.floor(minutes / 60), true);
            var mod = this._formatNumber(minutes % 60, false);
            return  hours + ' : ' +  mod;
        },

        /**
         * Adds zeros to the beginning or end of time string based on whether or not it is an hour or minute modulus.
         * @param num
         * @param isHour
         * @returns {string}
         * @private
         */
        _formatNumber : function (num, isHour) {
            var str = '' + num;
            if (str.length === 2) {
                return str;
            }
            return isHour ? '0' + str : str + '0';
        }

    });

})();
