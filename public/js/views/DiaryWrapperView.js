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

            return this;
        },

        initialize : function () {
            this.subViews.add('diary', app.DiaryView, {
                collection : app.days
            });
        },

        _initDatePicker : function () {
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
        }

    });

})();