(function () {
    "use strict";

    var Day = app.Day;
    // Globally (to this module) listen for mouse down so we can check later if the mouse is depressed :
    var _down = false;
    $(document).mousedown(function(e) {
        _down = true;
    }).mouseup(function(e) {
        _down = false;
    }).mouseleave(function(e) {
        _down = false;
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

        render : function (options) {
            this.$el.empty();
            var tmpVars = this.model.toJSON();
            tmpVars.date = this._formateDate();
            this.$el.html(this.template(tmpVars));
            this.renderTimes();
            this.showTotalTime();
            if (options && options.showSaved) {
                this.showSaved();
            }

            return this;
        },

        renderTimes : function () {
            var noon = this._getTimeRange(false, 12, 12);
            var night = this._getTimeRange(false, 1, 11);
            var midnight = this._getTimeRange(true, 12, 12);
            var morning = this._getTimeRange(true, 1, 11);

            this._currContainer = $('<div>').addClass('time-container');
            this.$('.cell-container').append(this._currContainer);

            this._addTimes(noon);
            this._addTimes(night);
            this._addTimes(midnight);
            this._addTimes(morning);
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
                    var mins = j * Day.enums.TIME_INCREMENT;
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

        _formateDate : function () {
            var d = new Date(this.model.get('date'));
            var arr = d.toUTCString().split(' ');
            arr.pop();
            arr.pop();
            return arr.join(' ');
        },

        /**
         * Helper function to add another time sub-views. Takes an array of times, for each 4 sub-views that are added
         * this method adds a time marker to the view.
         * @param timeArr
         * @private
         */
        _addTimes : function (timeArr) {
            _.each(timeArr, function (fullTime) {

                // TODO !!!! rely on the time model!!!!!!!!
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
                this.model.save(null, {
                    success : function () {
                        return this.render({showSaved : true});
                    }.bind(this)
                });
            }.bind(this), 1000);
        },

        showTotalTime : function () {
            var hours;
            var minutes;
            var sleepArr = _.filter(_.keys(this.model.toJSON()), function (timeKey) {
                if (this._isTimeKey(timeKey)) {
                    if (this.model.get(timeKey) === Day.enums.ASLEEP) {
                        return timeKey;
                    }
                }
            }, this);

            hours = Math.floor(sleepArr.length / 4);
            minutes = ((sleepArr.length % 4) * Day.enums.TIME_INCREMENT).toString();
            minutes = minutes.length === 1 ? '00' : minutes;

            this.$('.total-sleep').html('Time Slept ' + hours + ':' + minutes);
        },

        showSaved : function () {
            this.$('.fa-check-circle').removeClass('hidden');
            setTimeout(function () {
                this.$('.fa-check-circle').addClass('hidden');
            }.bind(this), 3000);

            this.publish('getAverage');
        }

    });


    /**
     * The diary time view is a single time cell. To use pass in an object with the following properties :
     *      displayTime - the time to be displayed in the cell
     *      time - the actual time key from the day model
     *      state - the value for that time in the model, ie. [ ASLEEP, AWAKE, DOZING ].
     * ***NOTE : These keys are the contents of the time model.
     *
     * @type {void|*}
     */
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
            var newState = currState === Day.enums.AWAKE ? Day.enums.ASLEEP : Day.enums.AWAKE;
            return this._setState(newState);
        },

        checkToggleDozing : function (e) {
            if (e.shiftKey || e.ctrlKey) {
                return this._setState(Day.enums.DOZING);
            } else {
                return this.toggleSleep();
            }
        },

        _setState : function (newState) {
            var states = [Day.enums.ASLEEP, Day.enums.AWAKE, Day.enums.DOZING];
            _.each(states, function (val) {
                this.$('.time-hour').removeClass(val);
            }, this);

            this.$('.time-hour').addClass(newState);

            this.model.set('state', newState);

            this.parentView.trigger('changeSleepState', this.model.get('time'), newState);
        }
    });

})();
