(function () {
    "use strict";

    // Globally (to this module) listen for mouse down so we can check later if the mouse is depressed :
    var Day = app.Day;
    var _down = false;
    $(document).mousedown(function(e) {
        _down = true;
    }).mouseup(function(e) {
        _down = false;
    }).mouseleave(function(e) {
        _down = false;
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
    app.TimeView = BBC.BaseView.extend({

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
