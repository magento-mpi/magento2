define([
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function (Scope, _) {

    return Scope.extend({
        initialize: function (actions, listing) {
            
            this.observe({
                actions:       actions,
                currentAction: null
            });

            this.target = listing;
        },

        applyAction: function () {
            var action = this.currentAction(),
                target = this.target;

            if (action) {
                action = action.type;
                
                if (target[action]) {
                    target[action]();
                }
            }
        },

        getCheckedQuantity: function () {
            return this.target.getCheckedQuantity();
        }
    });
});