(function () {
    "use strict";

    app.LoginRegister = BBC.BaseView.extend({

        events : {
            'click #login' : 'login',
            'click #register' : 'register'
        },

        template : _.template($('#LoginRegister-template').html(), null),

        login : function () {
            this.model.set(this._getData());
            this.model.url = '/login';
            this.model.save();
        },

        register : function () {
            this.model.set(this._getData());
            this.model.url = '/login/register';
            this.model.save();
        },

        _getData : function () {
            return {
                email : this.$('[name="email"]').val(),
                password : this.$('[name="password"]').val()
            };
        }
    });

})();