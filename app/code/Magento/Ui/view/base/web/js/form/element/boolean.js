/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract'
], function (Abstract) {
    'use strict';

    var __super__ = Abstract.prototype;

    return Abstract.extend({

        /**
         * Calls 'initListeners' of parent, if instance as 'unique' property
         *     set to true, binds 'onUniqueUpdate' method to handle param's storage
         *     'update.unique.{index}' event
         *      
         * @return {Object} - reference to instance
         */
        initListeners: function () {
            var onUniqueUpdate  = this.onUniqueUpdate.bind(this),
                params          = this.provider.params;
            
            __super__.initListeners.apply(this, arguments);

            if (this.unique) {
                params.on('update:unique.' + this.index, onUniqueUpdate);
            }

            return this;
        },

        /**
         * Converts the result of parent 'getInitialValue' call to boolean
         * 
         * @return {Boolean}
         */
        getInititalValue: function(){
            var value = __super__.getInititalValue.apply(this, arguments);

            return !!+value;
        },

        /**
         * Calls 'store' method of parent, if value is defined and instance's
         *     'unique' property set to true, calls 'setUnique' method
         *     
         * @param  {*} value
         * @return {Object} - reference to instance
         */
        store: function (value) {
            __super__.store.apply(this, arguments);

            if (this.unique && !_.isUndefined(value)) {
                this.setUnique();
            }

            return this;
        },

        /**
         * If instance's value is set to true, set's instance's name under
         *     params storage's 'unique.{index}' namespace
         *
         *  @return {Object} - reference to instance
         */
        setUnique: function () {
            var params  = this.provider.params,
                checked = this.value();

            if (checked) {
                params.set('unique.' + this.index, this.name);    
            }

            return this;
        },

        /**
         * If incoming 'name' does not equal to instance's name, sets 'value'
         *     property of instance to undefined
         * 
         * @param  {String} name
         */
        onUniqueUpdate: function (name) {
            this.value(this.name === name);
        }
    });
});