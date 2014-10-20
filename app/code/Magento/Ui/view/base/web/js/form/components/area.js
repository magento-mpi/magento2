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
        template:   'ui/area',
        active:     false,
        changed:    false,
        loading:    false
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

            this.initListeners()
                .pushParams();
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe('active changed loading');

            return this;
        },

        /**
         * Assignes updateState callback to update:activeArea event.
         * @return {Object} - reference to instance
         */
        initListeners: function() {
            var params  = this.provider.params;

            params.on('update:activeArea', this.updateState.bind(this));

            return this;
        },

        /**
         * Calls parent's initElement method.
         * Assignes callbacks on various event of incoming element.
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        initElement: function(elem){
            __super__.initElement.apply(this, arguments);

            elem.on({
                update:     this.onChildrenUpdate.bind(this),
                loading:    this.onContentLoading.bind(this, true),
                loaded:     this.onContentLoading.bind(this, false)
            });

            return this;
        },

        /**
         * Checks active state of instance and if true, sets activeArea
         *     property of params storage to name of instance.
         */
        pushParams: function() {
            var params = this.provider.params;

            if(this.active()){
                params.set('activeArea', this.name);
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
        setActive: function(){
            this.active(true);

            this.pushParams();
        },

        /**
         * Is being invoked on children update.
         * Sets changed property to one incoming.
         * Invokes setActive method if settings contain makeVisible property
         *     set to true.
         * 
         * @param  {Boolean} changed
         * @param  {Object} element
         * @param  {Object} settings
         */
        onChildrenUpdate: function(changed, element, settings){
            var params  = this.provider.params;

            if (settings.makeVisible) {
                this.setActive();
            }

            this.changed(changed);
        },

        /**
         * Sets loading property to true of false based on finished parameter.
         * @param  {Boolean} finished
         */
        onContentLoading: function(finished){
            this.loading(finished);
        }
    });
});