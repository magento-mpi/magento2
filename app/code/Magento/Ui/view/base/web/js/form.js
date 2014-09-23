define([
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/component'
], function (Scope, Component) {

    var Form = Scope.extend({
        initialize: function () {
            console.log('yo')
            this.observe('hello', 'Hello world');
        }
    });

    return Component({
        constr: Form
    });
});