(function () {
    "use strict";

    var user  = new app.User(app.currUserData);
    var messages = {
        default : "We're sorry, There was an error when trying to retrieve your data. Please try again.",
        cannot_create_duplicates : "You have already created a record for this date. You may not create 2 records for the same day."
    };

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        render : function () {

            this._hideDatePicker();
            this.subViews.empty();
            this.$el.empty();

            var diaryView = this.subViews.add(app.DiaryView, {
                collection : this.collection
            }).render();

            this.$el.append(this.template());
            this.$('#diary').append(diaryView.el);

            this._initDatePicker();

            this.getAverageSleepTime();

            return this;
        },

        initialize : function () {
            this.subViews.add('diary', app.DiaryView, {
                collection : app.days
            });
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
         * Adds a new Day record, with the given date.
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

            }.bind(this)).fail(function (jqxhr) {
                var json = jqxhr.responseJSON;
                var errCode = json && json.result ? json.result : null;
                this.showError(errCode);
            }.bind(this));
        },

        /**
         *
         * @param code
         */
        showError : function (code) {
            this._hideDatePicker();
            new BBC.AlertView({
                type : 'danger',
                place : $('.error-container'),
                message : messages[code] || messages.default
            }).render();
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
                    if (!average && average !== 0) {
                        return;
                    }
                    return this.showAverageSleepTime(average);
                }.bind(this)
            });
        },

        showAverageSleepTime : function (average) {
            debugger;
        }

    });

})();