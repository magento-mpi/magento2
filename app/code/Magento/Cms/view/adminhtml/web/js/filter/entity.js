define([
    'Magento_Ui/js/framework/ko/scope'
], function (Scope) {

    return Scope.extend({
        initialize: function () {
            this.def('query');
        };
    });
});