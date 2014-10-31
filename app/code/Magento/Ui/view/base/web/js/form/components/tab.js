/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    '../component'
], function(_, Component) {
    'use strict';

    var defaults = {
        active: false
    };

    var __super__ = Component.prototype;

    return Component.extend({

        /**
         * Extends instance with defaults. Invokes parent initialize method.
         * Calls initListeners and pushParams methods.
         */
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);

            this.pushParams();
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe('active');

            return this;
        },

        /**
         * Assignes updateState callback to update:activeArea event.
         * @return {Object} - reference to instance
         */
        initListeners: function() {
            var params  = this.provider.params,
                name    = 'update:' + this.storeAs;

            params.on(name, this.updateState.bind(this));

            return this;
        },

        /**
         * Checks active state of instance and if true, sets activeArea
         *     property of params storage to name of instance.
         */
        pushParams: function() {
            var params = this.provider.params;

            if(this.active()){
                params.set(this.storeAs, this.name);
            }
        },

        /**
         * Triggers 'active' event with current active state identifier.
         * @param  {String} area - area to compare instance's name to
         * @return {Object} - reference to instance
         */
        updateState: function(area) {
            var active = area === this.name;

            this.trigger('active', active)
                .active(active);
                
            return this;
        },
        
        /**
         * Sets active property to true, then invokes pushParams method.
         */
        activate: function(){
            this.active(true);

            this.pushParams();
        }
    });
});