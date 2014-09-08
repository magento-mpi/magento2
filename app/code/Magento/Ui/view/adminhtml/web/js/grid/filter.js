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

            this.initObservable()
                .extractFilterable()
                .initFields();
        },

        initObservable: function(config) {
            this.observe({
                isVisible: false
            });

            return this;
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

        getTemplateFor: function (filter) {
            return 'Magento_Ui.templates.controls.' + filter.type;
        },

        toggle: function () {
            this.isVisible(!this.isVisible());
        },

        close: function () {
            this.isVisible(false);
        },

        onLoad: function () {}
    });

    return Component({
        name: 'filter',
        constr: Filter
    });
});