define([
    'Magento_Ui/js/lib/ko/scope',
    '_'
], function (Scope, _) {
    
    return Scope.extend({
        initialize: function (data) {
            _.extend(this, data);

            this.type = this.filter_type || this.input_type;
        }
    });
});