(function () {
    "use strict";

    if (!app.currUserData) {
        throw new Error('You may not be on this page without current user data.');
    }

    var rootUrl = '/my_diary';
    var user = new app.User(app.currUserData);
    var dfd;

    var _fetchNextPage = function () {
        dfd = app.days.getNextPage({
            user_id : user.get('id')
        });

        $.when(dfd)
            // On success, render the diary page :
            .done(function () {
                _renderWrapper();
            })

            // On fail, show an error in the form of an alert view.
            .fail(function () {
                _renderWrapper();
                app.wrapper.showError();
            });
    };

    var _initEvents = function () {
        app.wrapper.on('nextPage', function () {
            this.remove();
            _fetchNextPage();
        });

        app.wrapper.on('prevPage', function () {
            this.remove();
            _fetchNextPage() // TODO !!!!!! make a prev page function here !!!!!
        });
    };
    var _renderWrapper = function () {

        // render the wrapper and append to the body :
        app.wrapper = new app.DiaryWrapperView({
            model : user,
            collection : app.days
        }).render();
        $('#JsContent').append(app.wrapper.$el);

        // set up event bindings on new wrapper view :
        _initEvents();
    };

    app.days = new app.Days();

    var Router = Backbone.Router.extend({

        routes : {
            '*splat' : 'getPage'
        },

        getPage : function () {

            _fetchNextPage();



            //this.navigate({trigger: false, replace: true})
        }

    });

    app.router = new Router();

    Backbone.history.start({
        root: rootUrl,
        pushState: true
    });

    //app.router.getPage();


})();
