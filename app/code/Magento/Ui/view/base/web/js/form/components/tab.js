/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '../collapsible'
], function(Collapsible) {
    'use strict';
   
    var __super__ = Collapsible.prototype;

    return Collapsible.extend({
        initListeners: function(){
            this.provider.data.on('validate', this.onValidate.bind(this));
            
            return this;
        },

        onValidate: function(){
            var formValid   = true,
                params      = this.provider.params;

            this.elems().forEach(function(elem){
                elem.delegate('validate');

                if(formValid && !params.get('formValid')){
                    formValid = false;

                    elem.setActive();
                }
            }, this);
        }
    });
});