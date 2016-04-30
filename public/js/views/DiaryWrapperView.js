(function () {
    "use strict";

    var user  = new app.User(app.currUserData);
    var _avgString = '<p class="text-success pull-right">Average Time Slept : <span class="badge"><%= obj.average %></span> Hours per night</p>';
    var _avgTemplate = _.template(_avgString, null, {variable : 'obj'});

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        viewEvents : {
            'getAverage' : 'getAverageSleepTime'
        },

        events : {
            'click .next-page' : 'nextPage',
            'click .prev-page' : 'prevPage'
        },

        /**
         * Render empties everything out, then rips through the collection adding a day summary view for each day
         * record returned, then set up some events.
         * @returns {app.DiaryWrapperView}
         */
        render : function () {

            this._hideDatePicker();
            this.subViews.empty();
            this.$el.empty();
            this.$el.append(this.template({
                currPage : this.collection.getCurrentPageNum(),
                totalPages :  this.collection.getTotalPageNum()
            }));

            this.collection.each(function (dayModel) {
                var newView = this.subViews.add(app.DaySummaryView, {
                    model : dayModel
                }).render();

                this.$('#diary').append(newView.$el);
            }, this);

            this._initDatePicker();
            this.getAverageSleepTime();
            return this;
        },

        /**
         * Inits instance of date picker. Sets it to start at today. Hooks up change event to create a new day model.
         * @private
         */
        _initDatePicker : function () {
            var date = this._getDate();
            this.$('.datepicker').data('date', date);
            this.datepicker = this.$('.datepicker').datepicker({
                autoclose : true
            })
                .on('changeDate', function () {
                    var date  = this.$('.datepicker').data('date');
                    this.addDay(date);
                }.bind(this));
        },

        _hideDatePicker : function () {
            if (this.datepicker) {
                this.datepicker.datepicker('hide');
            }
        },

        /**
         * Gets the current date from client settings. Used to init the date picker to the correct date.
         * @returns {string}
         * @private
         */
        _getDate : function () {
            var d = new Date();
            var arr = [d.getMonth()+1, d.getDate()];
            arr.forEach(function (val, index) {
                arr[index] = (val.toString().length === 1) ? '0' + val : val;
            });
            arr.unshift(d.getFullYear());
            return arr.join('-');
        },

        /**
         * Creates a new Day record with the given date. Invoked by date-picker callback.
         * @param date
         */
        addDay : function (date) {
            var dfd;
            var day = new app.Day({
                user_id : user.get('id'),
                date : date
            });
            dfd = day.save();

            $.when(dfd).done(function () {
                // Add the day model to the collection :
                this.collection.add(day);
                this.render();
                document.location = '/edit/' + day.get('id');

            }.bind(this)).fail(function (jqxhr) {
                var json = jqxhr.responseJSON;
                var errCode = json && (typeof json === 'string') ? json : null;
                this.showError(errCode);
            }.bind(this));
        },

        /**
         * Displays an error message at the top of the page
         * @param code
         */
        showError : function (code) {
            this._hideDatePicker();
            app.helpers.showError(code);
        },

        /**
         * Makes a request to the computation endpoint to get the user's average sleep time :
         */
        getAverageSleepTime : function () {
            var comp = new app.Computation();
            var data = {
                query : 'average_sleep_time',
                user_id : user.get('id')
            };
            comp.fetch({
                data : data,
                success : function (model) {
                    var average = model.get('average');
                    model.set('average', average.toString().slice(0,4));
                    if (!average && average !== 0) {
                        return;
                    }
                    return this.showAverageSleepTime(model.toJSON());
                }.bind(this)
            });
        },

        /**
         * Displays the average sleep time as a badge.
         * @param json
         */
        showAverageSleepTime : function (json) {
            this.$('.stats').empty();
            this.$('.stats').append(_avgTemplate(json));
        },

        nextPage : function () {
            this.trigger('nextPage');
        },

        prevPage : function () {
            this.trigger('prevPage');
        }

    });

})();
