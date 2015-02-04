(function () {
    "use strict";

    // Globally (to this module) listen for mouse down so we can check later if the mouse is depressed :
    var _down = false;
    $(document).mousedown(function(e) {
        _down = true;
    }).mouseup(function(e) {
        _down = false;
    }).mouseleave(function(e) {
        _down = false;
    });

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

        _currContainer : null,
        _amPm : 'AM : ',

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
            var currLen = this.subViews.length;
            var time = '';

            if (!this._currContainer || currLen === 48) {
                this._currContainer = $('<div>').addClass('time-container');
                this._currContainer.prepend($('<div>').addClass('time-ampm').html(this._amPm));
                this._amPm = 'PM : ';
                this.$el.append(this._currContainer);
            }

            if (currLen % 4 === 0) {
                time = key.split(':')[0];
            }

            var timeView = this.subViews.add(app.DiaryTimeView, {
                model : new Backbone.Model({
                    time : time,
                    state : val
                })
            }).render();

            this._currContainer.append(timeView.$el);
        },

        _isTimeKey : function (str) {
            return str.indexOf(':') !== -1;
        }
    });


    app.DiaryTimeView = BBC.BaseView.extend({

        className : 'time',

        template : _.template($('#DiaryRowTime-template').html(), null, {variable : 'data'}),

        events : {
            'mousedown' : 'toggleSleep',
            'mouseenter' : 'checkToggleSleep'
        },

        /**
         * On mouse enter, check if the mouse is depressed. If so then toggle the sleep position :
         * @param e
         */
        checkToggleSleep : function () {
            if (_down) {
                return this.toggleSleep();
            }
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
