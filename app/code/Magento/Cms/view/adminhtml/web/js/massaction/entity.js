define([
    'Magento_Ui/js/framework/ko/scope',
    '_'
], function (Scope, _) {

    return Scope.extend({
        initialize: function (massActions, actions, listing) {

            this
                .defArray('actions', actions)
                .defArray('massActions', massActions)
                .def('currentAction')
                .def('currentMassAction');

            this.target = listing;
            this._bind()._listen();
        },

        _bind: function () {
            _.bindAll(this, '_applyMassAction');
            
            return this;
        },

        _listen: function () {
            this.currentMassAction.subscribe(this._applyMassAction, 'change');
        },

        _applyMassAction: function (action) {
            var target = this.target;

            if (action) {
                action = action.type;
                if (target[action]) {
                    target[action]();
                }    
            }
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