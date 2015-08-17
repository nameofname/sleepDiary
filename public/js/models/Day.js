(function () {
    "use strict";

    app.Day = app.BaseModel.extend({
        url : '/rest/day'
    });

    app.Day.enums = {
        ASLEEP : 'ASLEEP',
        AWAKE : 'AWAKE',
        DOZING : 'DOZING'
    };

})();