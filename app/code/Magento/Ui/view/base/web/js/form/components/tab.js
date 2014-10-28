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

        validate: function(elem){
            var params = this.provider.params,
                invalid;

            elem.delegate('validate');

            invalid = params.get('invalidElement');

            if(this.formValid && invalid){
                this.formValid = false;

                elem.setActive();
                invalid.focused(true);
            }
        },

        onValidate: function(){
            this.formValid = true;
            
            this.elems().forEach(this.validate, this);
        }
    });
});