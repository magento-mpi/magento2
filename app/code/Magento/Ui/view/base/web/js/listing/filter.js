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
], function(_, Scope, Component, filterControls) {
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
                .extractFilterable()
                .initFilters();
        },

        initObservable: function(){
            this.observe({
                isVisible: false,
                active: []
            });

            return this; 
        },

        /**
         * Filters filterable fields and stores them to this.fields 
         * @param {Object} this - Reference to instance
         */
        extractFilterable: function (fields) {
            var fields = this.provider.meta.getVisible(),
                filterable;

            this.fields = fields.filter(function (field) {
                return field.filterable;
            });

            return this;
        },

        /**
         * Initializes filters by creating instances of corresponding classes found in filterControls by filter type
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
                Control = filterControls[type];

                return new Control(field, config);
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

        clearActive: function(){
            var active = this.active;

            active().forEach(function (filter) {
                filter.reset();
            });

            active([]);

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

        reset: function(){
            this.clearActive()           
                .reload();

            return this;
        },

        /**
         * Sets set of params to storage.
         * @param {*} action - data to set to storage params
         * @returns {Object} - reference to instance
         */
        pushParams: function() {
            var active  = this.active(),
                params  = this.provider.params,
                filters;

            filters = active.map(function(filter) {
                return filter.dump();
            });

            params.set('filter', filters);

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
            return function() {
                filter.reset();

                this.apply();
            }.bind(this);
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