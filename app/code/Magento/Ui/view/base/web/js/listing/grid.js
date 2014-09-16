/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/component',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/spinner',
], function(_, Component, Scope, loader) {
    'use strict';

    var Listing =  Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates provider with current state of instance. 
         * @param  {Object} settings
         */
        initialize: function(settings) {
            _.extend(this, settings);

            this.initFields()
                .initObservable()
                .initProvider()
                .updateItems();

            this.unlock();
        },

        /**
         * Initializes raw properties
         * @return {Object} reference to instance
         */
        initFields: function(){
            this.meta = this.provider.meta;
            this.fields = this.meta.getVisible();

            return this;
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function() {
            this.observe({
                rows:               [],
                isLocked:           false,
                colspan:            this.meta.get('colspan'),
                extenders:          null,
                templateExtenders:  []
            });

            return this;
        },

        /**
         * Subscribes on provider's events.
         * @return {Object} - reference to instance
         */
        initProvider: function() {
            var provider    = this.provider,
                meta        = provider.meta,
                dump        = provider.dump;

            _.bindAll(this, 'lock', 'onRefresh', 'updateExtenders', 'updateColspan');

            provider.on({
                'beforeRefresh':    this.lock,
                'refresh':          this.onRefresh
            });

            dump.on('update:extenders', this.updateExtenders);
            meta.on('update:colspan', this.updateColspan);

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
            return this.getRootTemplatePath() + '/cell/' + field.data_type;
        },

        /**
         * Returns object which represents template bindings params
         * @return {Object} - template binding params
         */
        getTemplate: function() {
            return {
                name:      this.getRootTemplatePath(),
                extenders: this.templateExtenders()
            };
        },

        /**
         * Generates template path for extender.
         * @param  {Object} extender
         * @return {String} - extender's template path
         */
        adjustTemplateExtender: function (extender) {
            return this.getRootTemplatePath() + '/extender/' + extender.path;
        },

        /**
         * Returns root template path for grid
         * @return {String} - root template path
         */
        getRootTemplatePath: function() {
            return 'ui/listing/grid';
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
         * Updates colspan observable property
         * @param  {String} colspan
         */
        updateColspan: function(colspan){
            this.colspan(colspan);
        },

        /**
         * Returns handler for row click
         * @param  {String} url
         * @return {Function} click handler
         */
        redirectTo: function (url) {

            /**
             * Sets location.href to target url
             */
            return function () {
                window.location.href = url;
            }
        },

        /**
         * Indicates if rows observable array is empty
         * @return {Boolean} [description]
         */
        hasData: function(){
            return this.rows().length;
        },

        /**
         * Sets isLocked observable to true
         * @return {Object} reference to instance
         */
        lock: function() {
            loader.show();
            this.isLocked(true);

            return this;
        },

        /**
         * Sets isLocked observable to false
         * @return {Object} reference to instance
         */
        unlock: function() {
            loader.hide();
            this.isLocked(false);

            return this;
        }
    });

    return Component({
        constr: Listing
    });
});