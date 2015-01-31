(function () {
    "use strict";

    var user  = new app.User(app.currUserData);

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        events : {
            "click #add" : 'addDay'
        },

        render : function () {

            // TODO ::: HERE add the add day view
            // Add other views? What other views would there be?????? NOTHING!!!! MUA HA HA HA HA!

            var diaryView = this.subViews.add(app.DiaryView, {
                collection : this.collection
            }).render();

            this.$el.append(diaryView.el);
            return this;
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