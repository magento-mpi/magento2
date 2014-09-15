/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/component',
    './filter/filters'
], function(_, Scope, Component, controls) {
    'use strict';

    var Filter = Scope.extend({
        /**
         * Initializes instance properties 
         * @param {Object} config - Filter component configuration
         */
        initialize: function(config) {
            this.config  = config;
            this.provider = this.config.provider;

            this.initObservable()
                .extractFields()
                .initFilters();
        },

        initObservable: function(){
            this.observe({
                isVisible:  false,
                active:     []
            });

            return this; 
        },

        /**
         * Filters filterable fields and stores them to this.fields 
         * @param {Object} this - Reference to instance
         */
        extractFields: function () {
            var provider    = this.provider.meta,
                fields      = provider.getVisible();

            this.fields = fields.filter(function (field) {
                return field.filterable;
            });

            return this;
        },

        /**
         * Initializes filters by creating instances of corresponding classes found in controls by filter type
         * @param {Object} this - Reference to instance
         */
        initFilters: function () {
            var configs = this.config.types,
                config,
                type,
                control;

            this.filters = this.fields.map(function (field) {
                type    = (field.filter_type || field.input_type);
                config  = configs && configs[type];
                control = controls[type];

                field.type = type;

                return new control(field, config);
            }, this);

            return this;
        },

        getNotEmpty: function(){
            return this.filters.filter(function(filter){
                return !filter.isEmpty();
            });
        },

        findActive: function(){
            this.active( this.getNotEmpty() );

            return this;
        },

        getData: function(all){
            var filters;

            filters = all ? this.filters : this.active();

            return filters.map(function(filter){
                return filter.dump();
            });
        },

        clearData: function(filter){
            var active = this.active;

            if(filter){
                filter.reset();

                active.remove(filter);
            }
            else{
                active().forEach(function (filter) {
                    filter.reset();
                });

                active.removeAll();
            }

            return this;
        },

        /**
         * Created handler for reset and apply actions.
         * @param {String} action - 'reset' or 'apply'.
         * @returns {Function} Function, which maps all filters with corresponding action of those and reloads storage
         */
        apply: function () {
            this.findActive()
                .reload();

            return this;
        },

        reset: function(filter){
            this.clearData(filter)           
                .reload();

            return this;
        },

        /**
         * Sets set of params to storage.
         * @param {*} action - data to set to storage params
         * @returns {Object} - reference to instance
         */
        pushParams: function() {
            var params = this.provider.params;

            params.set('filter', this.getData());

            return this;
        },

        /**
         * @description Toggles isVisible observable property
         */
        toggle: function () {
            this.isVisible(!this.isVisible());
        },

        /**
         * @description Sets isVisible observable property to false
         */
        close: function () {
            this.isVisible(false);
        },

        onClear: function(filter) {
            return this.reset.bind(this, filter);
        },

        /**
         * Returns path to filter's template splited by dots.
         * @param {Object} - instance of one of controls classes
         * @returns {String} - path to template based on type of filter
         */
        getTemplateFor: function (filter) {
            return 'Magento_Ui.templates.filter.item.' + filter.type;
        }
    });

    return Component({
        constr: Filter
    });
});