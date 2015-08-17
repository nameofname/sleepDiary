(function () {
    "use strict";

    var enums;

    app.Day = app.BaseModel.extend({

        url : '/rest/day',

        /**
         * Gets a list of all the keys in the model that are times. This is done by comparing the values for those
         * keys to the sleep state enums, [ ASLEEP, AWAKE, DOZING ].
         */
        getAllTimes : function () {
            return  _.pick(this.toJSON(), function (val) {
                return _.indexOf([enums.ASLEEP, enums.AWAKE, enums.DOZING], val) !== -1;
            });
        }
    });

    app.Day.enums = enums = {
        ASLEEP : 'ASLEEP',
        AWAKE : 'AWAKE',
        DOZING : 'DOZING',
        TIME_INCREMENT : 15
    };

})();