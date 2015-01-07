(function () {
    "use strict";

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        initialize : function () {
            this.subViews.add('diary', app.DiaryView, {
                collection : app.days
            });
        }

    });

})();