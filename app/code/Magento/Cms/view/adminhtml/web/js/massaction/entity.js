define([
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function (Scope, _) {

    return Scope.extend({
        initialize: function (actions, listing) {
            
            this.observe({
                actions: actions
            });

            this.target = listing;
        },

        applyAction: function (action) {
            var target = this.target;

            if (target[action]) {
                target[action]();
            }
        },

        getCheckedQuantity: function () {
            return this.target.getCheckedQuantity();
        }
    });
});