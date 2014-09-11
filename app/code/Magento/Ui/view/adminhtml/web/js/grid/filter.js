/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component',
    './core/controls'
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

            this.observe('isVisible', false);

            this.extractFilterable()
                .initFilters();
        },

        /**
         * Filters filterable fields and stores them to this.fields 
         * @param {Object} this - Reference to instance
         */
        extractFilterable: function (fields) {
            var fields = this.provider.meta.get('fields'),
                filterable;

            this.fields = fields.filter(function (field) {
                filterable = field.filterable;

                return typeof filterable === 'undefined' || filterable;
            });

            return this;
        },

        /**
         * Initializes filters by creating instances of corresponding classes found in controls by filter type
         * @param {Object} this - Reference to instance
         */
        initFilters: function () {
            var type,
                Control,
                config       = this.config,
                typeConfigs  = config.types;

            this.filters = this.fields.map(function (field) {
                type    = field.type = (field.filter_type || field.input_type);
                config  = typeConfigs && typeConfigs[type];
                Control = controls[type];

                return new Control(field, config);
            }, this);

            return this;
        },

        /**
         * Created handler for reset and apply actions.
         * @param {String} action - 'reset' or 'apply'.
         * @returns {Function} Function, which maps all filters with corresponding action of those and reloads storage
         */
        apply: function (action) {
            var params = [];

            this.filters.forEach(function (filter) {
                if( !filter.isEmpty() ){
                    params.push( filter.dump() );
                } 
            });

            console.log(params);

            this.updateParams(params).provider.refresh();    
        },

        reset: function(){
            this.filters.forEach(function (filter) {
                filter.reset();
            });

            this.updateParams([]).provider.refresh();   
        },

        /**
         * Sets set of params to storage.
         * @param {*} action - data to set to storage params
         * @returns {Object} - reference to instance
         */
        updateParams: function (filters) {
            this.provider.params.set('filter', filters);

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

        /**
         * Returns path to filter's template splited by dots.
         * @param {Object} - instance of one of controls classes
         * @returns {String} - path to template based on type of filter
         */
        getTemplateFor: function (filter) {
            return 'Magento_Ui.templates.controls.' + filter.type;
        }
    });

    return Component({
        constr: Filter
    });
});