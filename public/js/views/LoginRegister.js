(function () {
    "use strict";

    app.LoginRegister = BBC.BaseView.extend({

        events : {
            'click button' : 'doit'
        },

        template : _.template($('#LoginRegister-template').html(), null),

        doit : function () {
            alert('everthing works now!!!!!!!');
        }
    });

})();