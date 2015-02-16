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
            var morning = this._filterTimes(true, 9, 11);
            var noon = this._filterTimes(false, 12, 12);
            var afternoon = this._filterTimes(false, 1, 8);
            var evening = this._filterTimes(false, 9, 11);
            var midnight = this._filterTimes(true, 12, 12);
            var night = this._filterTimes(true, 1, 8);

            // First add the divider with the "Day" label
            // Next loop over the day values and add each cell :
            // Now add another divider with the "Night" label
            // Finally add each of the night cells :
            this._addDivider('Night :');

            this._addTimes(evening);
            this._addTimes(midnight);
            this._addTimes(night);

            this._addDivider('Day : : ');

            this._addTimes(morning);
            this._addTimes(noon);
            this._addTimes(afternoon);
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

        changeSleepState : function (time, state) {
            this.model.set(time, state);
            this.model.save();
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
