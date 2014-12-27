(function () {
    "use strict";

    app.LoginRegister = BBC.BaseView.extend({

        events : {
            'click #login' : 'login',
            'click #register' : 'register'
        },

        template : _.template($('#LoginRegister-template').html(), null),

        /**
         * Login and register access the same private method to save the model, however they user different URLs.
         * @param e
         * @returns {*}
         */
        login : function (e) {
            this.model.url = '/login';
            return this._save(e);
        },

        /**
         * Same as login but uses the registration URL
         * @param e
         * @returns {*}
         */
        register : function (e) {
            this.model.url = '/login/register';
            return this._save(e);
        },

        _getData : function () {
            return {
                email : this.$('[name="email"]').val(),
                password : this.$('[name="password"]').val()
            };
        },

        /**
         * Private helper to (login & register) and show an error or success message.
         * @private
         */
        _save : function (e) {
            var self = this;
            e.preventDefault();

            // Set data on the model :
            this.model.set(this._getData());

            // Save the model, using the correct success and error handlers.
            this.model.save(null, {
                success : function (model, resp, options) {
                    document.location = '/my_diary';
                },
                error : function (model, response, options) {
                    self._showError(response);
                }
            });
        },

        /**
         * Helper function to show a server error.
         * @param response
         * @returns {*}
         * @private
         */
        _showError : function (response) {
            var error = 'There was an error with the login form. Please try again. ';
            if (response.hasOwnProperty('responseJSON')) {
                if (response.responseJSON.hasOwnProperty('result')) {
                    if (response.responseJSON.result.hasOwnProperty('message')) {
                        error = response.responseJSON.result.message;
                    }
                }
            }

            var err = new BBC.AlertView({
                type : 'danger',
                place : this.$('.error-container'),
                message : error,
                autoClose : false
            }).render();

            return err;
        }
    });

})();