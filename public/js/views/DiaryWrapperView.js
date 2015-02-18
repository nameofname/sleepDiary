(function () {
    "use strict";

    var user  = new app.User(app.currUserData);

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        events : {
            "click #add" : 'addDay'
        },

        render : function () {

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
            this.$('.datepicker').datepicker()
                .on('changeDate', function () {
                    var date  = this.$('.datepicker').data('date');
                    this.addDay(date);
                }.bind(this));
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
                this.render();
            }.bind(this));
        }

    });

})();