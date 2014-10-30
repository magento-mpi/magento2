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
        initElement: function(elem){
            var params = this.provider.params;

            __super__.initElement.apply(this, arguments);
            
            if(!params.get('activeArea')){
                elem.activate();
            }

            return this;
        },

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

                elem.activate();
                invalid.focused(true);
            }
        },

        onValidate: function(){
            this.formValid = true;
            
            this.elems().forEach(this.validate, this);
        }
    });
});