define([
    'Magento_Ui/js/lib/ko/scope',
    '_'
], function (Scope, _) {
    'use strict';
    
    return Scope.extend({
        initialize: function (data) {
            _.extend(this, data);
        }
    });
});