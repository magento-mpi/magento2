/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './collapsible',
    'Magento_Ui/js/lib/spinner'
], function(Collapsible, loader) {
    'use strict';
   
    var __super__ = Collapsible.prototype;

    return Collapsible.extend({

        /**
         * Invokes initElement method of parent class, calls 'initActivation' method
         * passing element to it.
         * @param {Object} elem
         * @returns {Object} - reference to instance
         */
        initElement: function(elem){
            __super__.initElement.apply(this, arguments);    

            this.initActivation(elem)
                .hideLoader();

            return this;
        },

        /**
         * Binds 'onValidate' method as handler for data storage's 'validate' event
         * 
         * @return {Object} - reference to instance
         */
        initListeners: function(){
            var data = this.provider.data;

            __super__.initListeners.apply(this, arguments); 

            data.on('validate', this.onValidate.bind(this));
            
            return this;
        },

        /**
         * Activates element if one is first or if one has 'active' propert
         * set to true.
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        initActivation: function(elem){
            var elems   = this.elems(),
                isFirst = !elems.indexOf(elem);

            if(isFirst || elem.active()){
                elem.activate();
            }

            return this;
        },

        hideLoader: function () {
            loader.get(this.name).hide();
        },

        /**
         * Delegates 'validate' method on element, then reads 'invalid' property
         * of params storage, and if defined, activates element, sets 
         * 'allValid' property of instance to false and sets invalid's
         * 'focused' property to true.
         * @param {Object} elem
         */
        validate: function(elem){
            var params = this.provider.params,
                invalid;

            elem.delegate('validate');

            invalid = params.get('invalid');

            if(this.allValid && invalid){
                this.allValid = false;

                elem.activate();
                invalid.focused(true);
            }
        },

        /**
         * Sets 'allValid' property of instance to true, then calls 'validate' method
         * of instance for each element 
         */
        onValidate: function(){
            var elems;

            this.allValid = true;

            elems = this.elems.sortBy(function(elem){
                return !elem.active();
            });            

            elems.each(this.validate, this);
        }
    });
});