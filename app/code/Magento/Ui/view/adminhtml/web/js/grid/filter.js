<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
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
            this.storage = this.config.storage;

            this.observe('isVisible', false);

            this.extractFilterable()
                .initFilters();
        },

        /**
         * Filters filterable fields and stores them to this.fields 
         * @param {Object} this - Reference to instance
         */
        extractFilterable: function (fields) {
            var fields = this.storage.getMeta().fields,
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

        apply: function () {
            this.updateParams(this._dump()).storage.load();
        },

        reset: function () {
            this.updateParams(this._reset()).storage.load();
        },

        _dump: function () {
            return this.filters.map(function (filter) {
                return filter.dump();
            });
        },

        _reset: function () {
            return this.filters.map(function (filter) {
                return filter.reset();
            });
        },

        updateParams: function (filters) {
            this.storage.setParams({ filter: filters });

            return this;
        },

        toggle: function () {
            this.isVisible(!this.isVisible());
        },

        close: function () {
            this.isVisible(false);
        },

        getTemplateFor: function (filter) {
            return 'Magento_Ui.templates.controls.' + filter.type;
        },

        onLoad: function () {}
    });

    return Component({
        name: 'filter',
        constr: Filter
    });
});