(function () {
    "use strict";

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        initialize : function () {
            this.subs.add('diary', new app.DiaryView({
                collection : app.days
            }))
        }

    });

})();