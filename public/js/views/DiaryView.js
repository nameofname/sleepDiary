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

        _timeout : null,
        _currContainer : null,
        _amPm : 'AM : ',

        initialize : function () {
            this.on('changeSleepState', this.changeSleepState);
        },

        render : function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.renderTimes();
            return this;
        },

        renderTimes : function () {
            //console.log(1, new Date().getSeconds(), new Date().getMilliseconds());
            var morning = this._getTimeRange(true, 9, 11);
            var noon = this._getTimeRange(false, 12, 12);
            var afternoon = this._getTimeRange(false, 1, 8);
            var evening = this._getTimeRange(false, 9, 11);
            var midnight = this._getTimeRange(true, 12, 12);
            var night = this._getTimeRange(true, 1, 8);

            // First add the divider with the "Day" label
            // Next loop over the day values and add each cell :
            // Now add another divider with the "Night" label
            // Finally add each of the night cells :
            //console.log(2, new Date().getSeconds(), new Date().getMilliseconds());
            this._addDivider('Night :');

            this._addTimes(evening);
            this._addTimes(midnight);
            this._addTimes(night);

            this._addDivider('Day : : ');

            this._addTimes(morning);
            this._addTimes(noon);
            this._addTimes(afternoon);
            //console.log(3, new Date().getSeconds(), new Date().getMilliseconds());
        },

        /**
         * Helper function to get keys in a given time range.
         * @param am
         * @param lowerBound
         * @param upperBound
         * @returns {Array}
         * @private
         */
        _getTimeRange : function (am, lowerBound, upperBound) {

            var out = [];
            for (var i=lowerBound; i<=upperBound; i++) {
                for (var j=0; j<=3; j++) {
                    var mins = j * 15;
                    mins = mins == 0 ? '00' : mins;
                    out.push(i + ':' + mins + (am ? 'am' : 'pm'));
                }
            }
            return out;
        },

        /**
         * Helper function to get times in a given range.
         * Takes the upper bound, lower bound, and am boolean.
         * @param am <bool>
         * @param lowerBound <number>
         * @param upperBound <number>
         * @private
         */
        _filterTimes : function (am, lowerBound, upperBound) {
            return _.filter(_.keys(this.model.toJSON()), function (timeKey) {
                if (!this._isTimeKey(timeKey)) {
                    return false;
                }

                var hour = parseInt(timeKey.split(':')[0], 10);
                var isAm = timeKey.match(/[a-z]{2}/)[0] === 'am';
                var inRange = ((lowerBound <= hour) && ( hour <= upperBound)) && (isAm === am);

                return inRange;

            }, this);
        },

        /**
         * Helper function to add a divider between the sub-views. Divides the rows into day / night.
         * @param label
         * @private
         */
        _addDivider : function(label) {
            this._currContainer = $('<div>').addClass('time-container');
            this._currContainer.prepend($('<div>').addClass('time-ampm').html(label));
            this.$el.append(this._currContainer);
        },

        /**
         * Helper function to add another time sub-views. Takes an array of times, for each 4 sub-views that are added
         * this method adds a time marker to the view.
         * @param timeArr
         * @private
         */
        _addTimes : function (timeArr) {
            _.each(timeArr, function (fullTime) {

                var val = this.model.get(fullTime);
                var currLen = this.subViews.length;
                var hour = fullTime.split(':')[0];

                var timeView = this.subViews.add(app.DiaryTimeView, {
                    model : new Backbone.Model({
                        displayTime : (currLen % 4 === 0) ? hour : '',
                        time : fullTime,
                        state : val
                    })
                }).render();

                this._currContainer.append(timeView.$el);

            }, this);
        },

        _isTimeKey : function (str) {
            return str.indexOf(':') !== -1;
        },

        /**
         * Saves the day model with the udpated sleep state. Waits for a timeout in case the user is multi-selecting
         * many sleep "squares"
         * @param time
         * @param state
         */
        changeSleepState : function (time, state) {
            this.model.set(time, state);
            clearTimeout(this._timeout);
            this._timeout = setTimeout(function () {
                this.model.save();
            }.bind(this), 300);
        }

    });


    app.DiaryTimeView = BBC.BaseView.extend({

        className : 'time',

        template : _.template($('#DiaryRowTime-template').html(), null, {variable : 'data'}),

        events : {
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
