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
         * Updates storage with current state of instance. 
         * @param  {Object} config
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initIndexField()
                .attachTemplateExtender()
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
         * Updates storage's params and reloads it.
         */
        reload: function() {
            this.updateParams()
                .provider.refresh();
        },

        /**
         * Updates storage's params by the current state of instance
         * @return {Object} - reference to instance
         */
        updateParams: function() {
            this.provider.params.set('actions', this.buildParams());

            return this;
        },

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
            console.log(result)
            return result;
        },

        getExcludedItems: function () {
            var provider = this.provider.data,
                haveToBeSelected,
                actuallySelected,
                difference;        
            
            haveToBeSelected = _.pluck(provider.get('items'), this.indexField),
            actuallySelected = this.selected(),
            difference       = _.difference(haveToBeSelected, actuallySelected);

            return difference;
        },

        toggle: function () {
            this.isVisible(!this.isVisible());
        },

        apply: function (action) {
            var self = this;

            return function () {
                self[action]();    
            }
        },

        selectAll: function () {
            this.isAllSelected(true);
            this.selectPage();
        },

        deselectAll: function () {
            this.isAllSelected(false);
            this.deselectPage();
        },

        selectPage: function () {
            var items = this.provider.data.get('items'),
                ids   = _.pluck(items, this.indexField);

            this.selected(ids);
        },

        deselectPage: function () {
            this.selected([]);
        },

        isSelectedIndicatorChecked: function () {
            return this.isAllSelected();
        }
    });

    return Component({
        constr: Action
    });
});