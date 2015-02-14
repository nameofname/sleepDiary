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

    /**
     * Row view represents each day model. For each day you can click on the time slots to indicate your sleep patterns.
     * This view has as it's sub views a collection of DiaryTimeViews (below)
     * @type {void|*}
     */
    app.DiaryRowView = BBC.BaseView.extend({

        className : 'diary-row',

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
            var json = this.model.toJSON();
            var day = [];
            var night = [];

            // TODO ::: THIS LOGIC GETS ME THE VALUES OUT OF ORDER AND SLIGHTLY WRONG... FIX IT.

            var morning = this._filterTimes(true, 8, 12);
            var noon = this._filterTimes(false, 1, 8);
            // TODO : !!!!!!!

            // First add the divider with the "Day" label
            // Next loop over the day values and add each cell :
            // Now add another divider with the "Night" label
            // Finally add each of the night cells :
            this._addDivider('Day :');

            _.each(day, function (time) {
                this._addTime(this.model.get(time), time);
            }, this);

            this._addDivider('Night :');

            _.each(night, function (time) {
                this._addTime(this.model.get(time), time);
            }, this);

        },

        changeSleepState : function (time, state) {
            this.model.set(time, state);
            this.model.save();
        },

        _addDivider : function(label) {
            this._currContainer = $('<div>').addClass('time-container');
            this._currContainer.prepend($('<div>').addClass('time-ampm').html(label));
            this.$el.append(this._currContainer);
        },

        _addTime : function (val, fullTime) {
            var currLen = this.subViews.length;
            var time = '';

            if (currLen % 4 === 0) {
                time = fullTime.split(':')[0];
            }

            var timeView = this.subViews.add(app.DiaryTimeView, {
                model : new Backbone.Model({
                    displayTime : time,
                    time : fullTime,
                    state : val
                })
            }).render();

            this._currContainer.append(timeView.$el);
        },

        /**
         * TODO: Make this function take the upper, lower, and am distinction and apply .
         * @param am
         * @param lowerBound
         * @param upperBound
         * @private
         */
        _filterTimes : function (am, lowerBound, upperBound) {
            var json = this.model.toJSON();
            _.each(json, function (val, timeKey) {

                if (this._isTimeKey(timeKey)) {
                    var hour = parseInt(timeKey.split(':')[0], 10);
                    var am = timeKey.match(/[a-z]{2}/)[0] === 'am';
                    var morning = ((8 <= hour) && (hour <= 12)) && am;
                    var noon = (hour === 12 || ((1 <= hour) && ( hour <= 8))) && !am;

                    if (morning || noon) {
                        day.push(timeKey);
                    } else {
                        night.push(timeKey);
                    }
                }

            }, this);
        },

        _isTimeKey : function (str) {
            return str.indexOf(':') !== -1;
        }
    });


    app.DiaryTimeView = BBC.BaseView.extend({

        className : 'time',

        template : _.template($('#DiaryRowTime-template').html(), null, {variable : 'data'}),

        events : {
            //'click' : 'checkToggleDozing',
            'mousedown' : 'checkToggleDozing',
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
            var currState = this.model.get('state');
            var newState = currState === 'AWAKE' ? 'ASLEEP' : 'AWAKE';
            return this._setState(newState);
        },

        checkToggleDozing : function (e) {
            if (e.shiftKey || e.ctrlKey) {
                return this._setState('DOZING');
            } else {
                return this.toggleSleep();
            }
        },

        _setState : function (newState) {
            var states = ['ASLEEP', 'AWAKE', 'DOZING'];
            _.each(states, function (val) {
                this.$('.time-hour').removeClass(val);
            }, this);

            this.$('.time-hour').addClass(newState);

            this.model.set('state', newState);

            this.parentView.trigger('changeSleepState', this.model.get('time'), newState);
        }
    });

})();
