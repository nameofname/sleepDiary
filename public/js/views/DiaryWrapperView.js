(function () {
    "use strict";

    var user  = new app.User(app.currUserData);

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        events : {
            "click #add" : 'addDay'
        },

        initialize : function () {
            this.subViews.add('diary', app.DiaryView, {
                collection : app.days
            });
        },

        addDay : function () {
            var self = this;
            var dfd;
            var day = new app.Day({
                user_id : user.get('id')
            });
            dfd = day.save();
            $.when(dfd).done(function () {
                self.render();
            });
        }

    });

})();