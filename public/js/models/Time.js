(function () {
    "use strict";

    app.Time = app.BaseModel.extend({

        url : '', // This should never be used.

        /**
         * Parses the passed time object into a usable time model.
         * Obj param for the init should be the key : val pair of time to sleep state value from the day model.
         * @param obj
         */
        parse : function (obj) {
            var fullTime = _.keys(obj)[0];
            var val = obj[fullTime];
            var hour = fullTime.split(':')[0];
            var minuteMod = parseInt(
                fullTime
                    .split(':')[1]
                    .split('')
                    .slice(0,2)
                    .join(''),
                10);

            return {
                displayTime : (minuteMod === 0) ? hour : '',
                time : fullTime,
                state : val
            };
        }

    });

})();