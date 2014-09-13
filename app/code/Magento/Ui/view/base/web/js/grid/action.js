/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component'
], function(_, Scope, Component) {
    'use strict';
    
    var defaults = {
        templateExtender: 'actions',
        idAttribute: 'id_attribute',
        actions: [
            { value: 'selectAll',    label: 'Select all' },
            { value: 'selectPage',   label: 'Select all on this page' },
            { value: 'deselectAll',  label: 'Deselect all' },
            { value: 'deselectPage', label: 'Deselect all on this page' }
        ]
    };

    var Action = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates storage params with current state of instance. 
         * Attaches it's template extender to storage.dump.
         * Initialises this.indexField.
         * @param  {Object} config
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initIndexField()
                .attachTemplateExtender()
                .initProvider()
                .updateParams();
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function(){
            this.observe({
                selected:      this.selected     || [],
                isAllSelected: this.all_selected || false,
                isVisible:     false
            });

            return this;
        },

        /**
         * Looks up for field with 'id_attribute' set to true and set's it's 'index' prop to this.indexField
         * @return {Object} - reference to instance
         */
        initIndexField: function () {
            var fields = this.provider.meta.get('fields'),
                fieldsWithId;

            fieldsWithId = fields.filter(function (field) {
                return this.idAttribute in field;
            }, this);

            this.indexField = _.last(fieldsWithId).index;

            return this;
        },

        /**
         * Attaches it's template to provider.dump's extenders
         * @return {Object} - reference to instance
         */
        attachTemplateExtender: function () {
            var provider  = this.provider.dump,
                extenders = provider.get('extenders');
                
            extenders.push({
                path: this.templateExtender,
                name: this.name,
                as:   'actions'
            });

            provider.trigger('update:extenders', extenders);

            return this;
        },

        /**
         * Subscribes on provider's refresh event to call onRefresh callback
         * @return {Object} - reference to instance
         */
        initProvider: function(){
            this.provider.on('refresh', this.onRefresh.bind(this));

            return this;
        },

        /**
         * Updates state according to changes of provider.
         */
        onRefresh: function () {
            var isAllSelected = this.isAllSelected();

            if (isAllSelected) {
                this.selectPage();
            }
        },

        /**
         * Updates storage's params by the current state of instance
         * @return {Object} - reference to instance
         */
        updateParams: function() {
            this.provider.params.set(true, 'actions', this.buildParams());
            return this;
        },

        /**
         * Prepares params object, which represents the current state of instance.
         * @return {Object} - params object
         */
        buildParams: function () {
            var isAllSelected = this.isAllSelected(),
                selected      = this.selected(),
                result        = {};

            if (isAllSelected) {
                result['all_selected'] = true;
                result['excluded']     = this.getExcludedItems();
            } else {
                result['selected'] = selected;
            }

            return result;
        },

        /**
         * Compares all items to those selected and returnes the difference. 
         * @return {Array} - array of excluded ids.
         */
        getExcludedItems: function () {
            var provider = this.provider.data,
                haveToBeSelected,
                actuallySelected,
                excluded;        
            
            haveToBeSelected = _.pluck(provider.get('items'), this.indexField);
            actuallySelected = this.selected();
            excluded         = _.difference(haveToBeSelected, actuallySelected);

            return excluded;
        },

        /**
         * Toggles isVisible observable property
         */
        toggle: function () {
            this.isVisible(!this.isVisible());
        },

        /**
         * Creates handler for applying action (e.g. selectAll)
         * @param  {String} action
         * @return {Function} - click handler
         */
        apply: function (action) {
            var self = this;

            return function () {
                self[action]();    
            }
        },

        /**
         * Sets isAllSelected observable to true and selects all items on current page.
         */
        selectAll: function () {
            this.isAllSelected(true);
            this.selectPage();
        },

        /**
         * Sets isAllSelected observable to false and deselects all items on current page.
         */
        deselectAll: function () {
            this.isAllSelected(false);
            this.deselectPage();
        },

        /**
         * Selects all items on current page, adding their ids to selected observable array
         */
        selectPage: function () {
            var items = this.provider.data.get('items'),
                ids   = _.pluck(items, this.indexField);

            this.selected(ids);
        },

        /**
         * Deselects all items on current page, emptying selected observable array 
         */
        deselectPage: function () {
            this.selected([]);
        }
    });

    return Component({
        constr: Action
    });
});