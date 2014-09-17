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

        /**
         * Initializes observable properties of instance.
         * @returns {Filter} Chainbale.
         */
        initObservable: function(){
            this.observe({
                isVisible:  false,
                active:     []
            });

            return this; 
        },

        /**
         * Filters filterable fields and stores them to this.fields 
         * @returns {Filter} Chainbale.
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
         * Initializes filters by creating instances of
         * corresponding classes found in controls by filter type.
         * @returns {Filter} Chainbale.
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

        /**
         * Extracts an array of non-empty filters.
         * @returns {Array} Array of non-empty filters
         */
        getNotEmpty: function(){
            return this.filters.filter(function(filter){
                return !filter.isEmpty();
            });
        },

        /**
         * Writes the result of getNotEmpty to active observable.
         * @returns {Filter} Chainbale.
         */
        findActive: function(){
            this.active(this.getNotEmpty());

            return this;
        },

        /**
         * Returns an array filters' data.
         * @param {Boolean} [all=false] -
                Whether to extract data from all of the filters
                or from only the active ones.
         * @returns {Array} Array of filters' data.
         */
        getData: function(all){
            var filters;

            filters = all ? this.filters : this.active();

            return filters.map(function(filter){
                return filter.dump();
            });
        },

        /**
         * Clears data of all filters or of specified one.
         * @param {Object} [filter] - If specified, clears data only of this filter.
         * @returns {Filter} Chainbale.
         */
        clearData: function(filter){
            var active = this.active;

            if(filter){
                filter.reset();

                active.remove(filter);
            }
            else{
                this.filters.forEach(function (filter) {
                    filter.reset();
                });

                active.removeAll();
            }

            return this;
        },

        /**
         * Updates an array of active filters
         * and reloads data provider with new filtering parameters.
         * @returns {Filter} Chainbale.
         */
        apply: function () {
            this.findActive()
                .reload();

            return this;
        },

        /**
         * Clears filters and updates data provider with new filtering parameters.
         * @param {Object} [filter] - If specified then clears only this filter. 
         * @returns {Filter} Chainbale.
         */
        reset: function(filter){
            this.clearData(filter)           
                .reload();

            return this;
        },

        /**
         * Sets set of params to storage.
         * @param {*} action - data to set to storage params
         * @returns {Filter} Chainbale.
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

        /**
         * Resets specified filter using reset method
         * @param  {Object} filter - filter to reset
         */
        onClear: function(filter) {
            return this.reset.bind(this, filter);
        },

        /**
         * Returns path to filter's template splited by dots.
         * @param {Object} - instance of one of controls classes
         * @returns {String} - path to template based on type of filter
         */
        getTemplateFor: function (filter) {
            return 'ui/filter/' + filter.type;
        }
    });

    return Component({
        constr: Filter
    });
});