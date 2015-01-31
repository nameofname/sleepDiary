(function () {
    "use strict";

    app.DiaryView = BBC.BaseView.extend({

        template : _.template($('#Diary-template').html(), null, {variable : 'data'}),

        render : function () {
            var self = this;

            this.collection.each(function (dayModel) {
                var newView = self.subViews.add(app.DiaryRowView, {
                    model : dayModel
                }).render();

                    self.$el.append(newView.$el);
            });

            return this;
        }

    });


    app.DiaryRowView = BBC.BaseView.extend({

        template : _.template($('#DiaryRow-template').html(), null, {variable : 'data'}),

        initialize : function () {
            this.on('BaseView:render', function () {
                this.renderTimes();
            });

            this.on('changeSleepState', this.changeSleepState);
        },

        renderTimes : function () {
            var self = this;
            var json = this.model.toJSON();

            _.each(json, function (val, key) {
                if (self._isTimeKey(key)) {
                    self._addTime(val, key);
                }
            });
        },

        changeSleepState : function (time, state) {
            this.model.set(time, state);
            this.model.save();
        },

        _addTime : function (val, key) {
            var timeView = this.subViews.add(app.DiaryTimeView, {
                model : new Backbone.Model({
                    time : key,
                    state : val
                })
            }).render();

            this.$el.append(timeView.$el);
        },

        _isTimeKey : function (str) {
            return str.indexOf(':') !== -1;
        }
    });


    app.DiaryTimeView = BBC.BaseView.extend({

        template : _.template($('#DiaryRowTime-template').html(), null, {variable : 'data'}),

        events : {
            'click' : 'toggleSleep'
        },

        toggleSleep : function (e) {
            var self = this;
            var states = ['ASLEEP', 'AWAKE', 'DOZING'];
            var currState = this.model.get('state');
            var newState = currState === 'AWAKE' ? 'ASLEEP' : 'AWAKE';

            _.each(states, function (val) {
                self.$('.time-hour').removeClass(val);
            });

            self.$('.time-hour').addClass(newState);

            self.model.set('state', newState);

            this.parentView.trigger('changeSleepState', this.model.get('time'), newState);
        }
    });

})();
