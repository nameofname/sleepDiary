(function () {
    "use strict";

    app.DiaryView = BBC.BaseView.extend({

        template : _.template($('#Diary-template').html(), null, {variable : 'data'}),

        render : function () {
            var self = this;

            this.collection.each(function (dayModel) {
                var newView = self.subViews.add(app.DiaryRowView, {
                    model : dayModel
                }).render();

                    self.$el.append(newView.$el);
            });

            return this;
        }

    });


    app.DiaryRowView = BBC.BaseView.extend({

        template : _.template($('#DiaryRow-template').html(), null, {variable : 'data'}),

        initialize : function () {
            this.on('BaseView:render', function () {
                this.renderTimes();
            });
        },

        renderTimes : function () {
            var self = this;
            var json = this.model.toJSON();

            _.each(json, function (val, key) {
                if (self._isTimeKey(key)) {
                    self._addTime(val, key);
                }
            });
        },

        _addTime : function (val, key) {
            var timeView = this.subViews.add(app.DiaryTimeView, {
                model : new Backbone.Model({
                    time : key,
                    val : val
                })
            }).render();

            this.$el.append(timeView.$el);
        },

        _isTimeKey : function (str) {
            return str.indexOf(':') !== -1;
        }
    });


    app.DiaryTimeView = BBC.BaseView.extend({
        template : _.template($('#DiaryRowTime-template').html(), null, {variable : 'data'})
    });

})();
