(function () {
    "use strict";

    app.DiaryView = BBC.BaseView.extend({

        template : _.template($('#Diary-template').html(), null, {variable : 'data'}),

        render : function () {
            var self = this;

            this.collection.each(function (dayModel) {
                var newView = self.subViews.add(app.DiaryRowView, {
                    model : dayModel
                });

                newView.render();
                self.$el.append(newView.$el);
            });
        }

    });


    app.DiaryRowView = BBC.BaseView.extend({

        template : _.template('#DiaryRow-template', null, {variable : 'data'})

    });

})();