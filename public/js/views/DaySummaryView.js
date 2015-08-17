(function () {
    "use strict";

    app.DaySummaryView = BBC.BaseView.extend({

        template : _.template($('#Day-summary-template').html(), null, {variable : 'data'}),

        render : function () {
            //this.template(this.model.toJSON());
            this.$el.append(this.template(this.model.toJSON()));
            return this;
        }

        //render : function () {
            //this.collection.each(function (dayModel) {
            //    var newView = self.subViews.add(app.DiaryRowView, {
            //        model : dayModel
            //    }).render();
            //
            //    self.$el.append(newView.$el);
            //});

            //return this;
        //}

    });

})();
