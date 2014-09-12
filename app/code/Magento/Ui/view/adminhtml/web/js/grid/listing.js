/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    './core/component',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/mixin/loader',
], function(_, Component, Scope, Loader) {
    'use strict';

    var Listing =  Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates provider with current state of instance. 
         * @param  {Object} settings
         */
        initialize: function(settings) {
            _.extend(this, settings);

            this.initObservable()
                .initProvider()
                .updateItems();
            
            this.fields = this.provider.meta.get('fields');
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function() {
            this.observe({
                rows:       [],
                view:       'grid',
                isLocked:   false,
                templateExtenders: [],
                extenders: null
            });

            return this;
        },

        /**
         * Subscribes on provider's events.
         * @return {Object} - reference to instance
         */
        initProvider: function() {
            var provider    = this.provider,
                dump        = provider.dump;

            provider.on({
                'beforeRefresh':    this.lock.bind(this),
                'refresh':          this.onRefresh.bind(this)
            });

            dump.on('update:extenders', this.updateExtenders.bind(this));

            return this;
        },

        /**
         * Is being called when some component pushed it's extender to global storage.
         * Preprocesses incoming array of extenders and sets the results into extenders
         * and templateExtenders observable arrays
         * @param  {Array} extenders
         */
        updateExtenders: function (extenders) {
            var adjusted = extenders.reduce(function (adjusted, extender) {

                adjusted[extender.as] = extender.name;
                return adjusted;

            }, {});
            
            this.extenders(adjusted);

            this.templateExtenders(extenders.map(this.adjustTemplateExtender, this));
        },

        /**
         * Fetches items from storage and stores it into rows observable array
         * @return {Object} - reference to instance
         */
        updateItems: function() {
            var items = this.provider.data.get('items');

            this.rows(items);

            return this;
        },

        /**
         * Returns extender by name of component which set it.
         * @param  {String} name
         * @return {String} - Namespace string by which target component is registered in storage.
         */
        getExtender: function(name) {
            var extenders = this.extenders();

            return extenders ? (this.parent_name + ':' + extenders[name]) : null;
        },

        /**
         * Returns path to template for arbitrary field
         * @param  {String} field
         * @return {String} - path to template
         */
        getCellTemplateFor: function(field) {
            return this.getRootTemplatePath() + '.cell.' + field.data_type;
        },

        /**
         * Returns object which represents template bindings params
         * @return {Object} - template binding params
         */
        getTemplate: function() {
            return {
                name:      'Magento_Ui.templates.listing.' + this.view(),
                extenders: this.templateExtenders()
            };
        },

        /**
         * Generates template path for extender.
         * @param  {Object} extender
         * @return {String} - extender's template path
         */
        adjustTemplateExtender: function (extender) {
            return this.getRootTemplatePath() + '.' + extender.path;
        },

        /**
         * Returns root template path for grid, based on view observable
         * @return {String} - root template path
         */
        getRootTemplatePath: function() {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        /**
         * Provider's refresh event's handler.
         * Locks grid and updates items.
         */
        onRefresh: function() {
            this.unlock()
                .updateItems();
        },

        /**
         * Returns handler for row click
         * @param  {String} url
         * @return {Function} click handler
         */
        redirectTo: function (url) {

            /**
             * Sets location href to target url
             */
            return function () {
                location.href = url;
            }
        }
    }, Loader);

    return Component({
        constr: Listing
    });
});