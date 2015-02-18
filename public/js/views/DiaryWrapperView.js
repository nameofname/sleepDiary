(function () {
    "use strict";

    var user  = new app.User(app.currUserData);

    app.DiaryWrapperView = BBC.BaseView.extend({

        template : _.template($('#DiaryWrapper-template').html(), null, {variable : 'data'}),

        events : {
            "click #add" : 'addDay'
        },

        render : function () {

            this.subViews.empty();
            this.$el.empty();

            var diaryView = this.subViews.add(app.DiaryView, {
                collection : this.collection
            }).render();

            this.$el.append(this.template());
            this.$('#diary').append(diaryView.el);
            return this;
        },

        initialize : function () {
            this.subViews.add('diary', app.DiaryView, {
                collection : app.days
            });
        },

        addDay : function () {
            var dfd;
            var day = new app.Day({
                user_id : user.get('id')
            });
            dfd = day.save();
            $.when(dfd).done(function () {
                debugger;
                this.render();
            }.bind(this));
        }

    });

})();