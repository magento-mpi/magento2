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
                params      = this.provider.params,
                invalid;

            params.set('invalidElement', null);

            this.elems().forEach(function(elem){
                elem.delegate('validate');
                invalid = params.get('invalidElement');

                if(formValid && invalid){
                    formValid = false;

                    elem.setActive();
                    invalid.focused(true);
                }
            }, this);
        }
    });
});