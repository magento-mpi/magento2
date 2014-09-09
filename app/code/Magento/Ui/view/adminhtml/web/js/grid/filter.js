define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component',
    './core/controls'
], function(_, Scope, Component, controls) {
    'use strict';

    var DEFAULT_FILTER_TYPE = 'input';

    var Filter = Scope.extend({
        initialize: function(config) {
            this.storage = config.storage;

            this.observe('isVisible', false);

            this.extractFilterable()
                .initFields();
        },            

        extractFilterable: function (fields) {
            var fields = this.storage.getMeta().fields,
                filterable;

            this.fields = fields.filter(function (field) {
                filterable = field.filterable;

                return typeof filterable === 'undefined' || filterable;
            });

            return this;
        },

        initFields: function () {
            var type,
                Control;

            this.filters = this.fields.map(function (field) {
                type = field.type = (field.filter_type || field.input_type || DEFAULT_FILTER_TYPE);
                Control = controls[type];

                return new Control(field);
            });

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