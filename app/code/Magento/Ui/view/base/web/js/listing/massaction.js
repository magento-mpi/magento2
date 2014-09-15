/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/component'
], function (_, Scope, Component) {
    'use strict';

    function capitaliseFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    var defaults = {
        actions: [],
        selects: [
            { value: 'selectAll',    label: 'Select all'                },
            { value: 'deselectAll',  label: 'Deselect all'              },
            { value: 'selectPage',   label: 'Select all on this page'   },
            { value: 'deselectPage', label: 'Deselect all on this page' }
        ],
        selectableTemplate: 'selectable'
    };

    var MassActions = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates storage with current state of instance.
         * @param  {Object} config
         */
        initialize: function (config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initIndexField()
                .formatActions()
                .attachTemplateExtender()
                .initProvider()
                .countPages();
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this.observe({
                selected:           this.selected || [],
                excluded:           [],
                allSelected:        this.allSelected || false,
                actionsVisible:     false,
                menuVisible:        false,
                multiplePages:      ''
            });

            this.selected.subscribe(this.onSelectionsChange.bind(this));

            return this;
        },

        /**
         * Looks up for field with 'id_attribute' set to true and set's
         * it's 'index' prop to this.indexField
         * @return {Object} - reference to instance
         */
        initIndexField: function () {
            var provider = this.provider.meta;

            this.indexField = provider.get('indexField');

            return this;
        },

        /**
         * Convertes incoming optins to compatible format
         * @return {Object} reference to instance
         */
        formatActions: function(){
            var actions = this.actions;

            if(!Array.isArray(actions)){

                this.actions = _.map(actions, function(action, name){
                    action.value = name;

                    return action;
                });
            }

            return this;
        },

        /**
         * Attaches it's template to provider.dump's extenders
         * @return {Object} - reference to instance
         */
        attachTemplateExtender: function () {
            var provider    = this.provider,
                dump        = provider.dump,
                meta        = provider.meta,
                colspan     = meta.get('colspan'),
                extenders   = dump.get('extenders');

            if(!this.selectableTemplate) {
                return this;
            }

            extenders.push({
                path:   this.selectableTemplate,
                name:   this.name,
                as:     'massaction'
            });

            dump.trigger('update:extenders', extenders);
            meta.set('colspan', colspan + 1);

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
         * Prepares params object, which represents the current state of instance.
         * @return {Object} - params object
         */
        buildParams: function () {            
            if (this.allSelected()) {

                return {
                    all_selected: true,
                    excluded: this.excluded()
                };
            }

            return {
                selected: this.selected()
            };
        },
        
        /**
         * Toggles observable property based on area argument
         * @param  {area} area
         */
        toggle: function(area){
            var visible = this[area];

            visible(!visible());
        },

        /**
         * Updates storage's params by the current state of instance
         * and hides dropdowns.
         * @param {String} action
         */
        setAction: function (action) {
            return this.submit.bind(this, action);
        },

        /**
         * Updates storage's params and reloads it.
         */
        submit: function (action) {
            var client = this.provider.client;

            client.submit({
                method: 'post',
                action: action.url,
                data: {
                    massaction: this.buildParams()
                }
            });
            
            return this;
        },

        getIds: function(exclude){
            var items   = this.provider.data.get('items'),
                ids     = _.pluck(items, this.indexField);

            return exclude ?
                _.difference(ids, this.excluded()) :
                ids;    
        },

        /**
         * Sets isAllSelected observable to true and selects all items on current page.
         */
        selectAll: function () {
            this.allSelected(true);
            
            this.clearExcluded()
                .selectPage();
        },

        /**
         * Sets isAllSelected observable to false and deselects all items on current page.
         */
        deselectAll: function () {
            this.allSelected(false);
            this.deselectPage();
        },

        /**
         * Selects all items on current page, adding their ids to selected observable array
         */
        selectPage: function () {
            this.selected(this.getIds());
        },

        /**
         * Deselects all items on current page, emptying selected observable array
         */
        deselectPage: function () {
            this.selected.removeAll();
        },
        
        updateExcluded: function(selected) {
            var all         = this.getIds(),
                excluded    = this.excluded();

            excluded = _.union(excluded, _.difference(all, selected));

            this.excluded(excluded);

            return this;
        },

        clearExcluded: function(){
            this.excluded.removeAll();

            return this;
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

        countPages: function() {
            var provider = this.provider.data;

            this.pages = provider.get('pages');

            this.multiplePages(this.pages > 1);

            return this;
        },

        countSelect: function() {
            var provider = this.provider,
                total,
                count;

            if (this.allSelected()) {
                total = provider.data.get('totalCount');

                count = total - this.excluded().length;
            } else {
                count = this.selected().length;
            }

            provider.meta.set('selected', count);

            return this;
        },

        /**
         * If isAllSelected is true, deselects all, else selects all
         */
        toggleSelectAll: function () {
            var isAllSelected = this.allSelected();

            isAllSelected ? this.deselectAll() : this.selectAll();
        },

        /**
         * Looks up for corresponding to passed action checker method,
         * and returnes it's result. If method not found, returnes true;
         * @param  {String} action - e.g. selectAll, deselectAll
         * @return {Boolean} should action be visible
         */
        shouldBeVisible: function (action) {
            var checker = this['should' + capitaliseFirstLetter(action) + 'BeVisible'];

            return checker ? checker.call(this) : true;
        },

        /**
         * Checkes if selectAll action supposed to be visible
         * @return {Boolean}
         */
        shouldSelectAllBeVisible: function () {
            return !this.allSelected() && this.multiplePages();
        },

        /**
         * Checkes if deselectAll action supposed to be visible
         * @return {Boolean}
         */
        shouldDeselectAllBeVisible: function () {
            return this.allSelected() && this.multiplePages();
        },

        onToggle: function(area){
            return this.toggle.bind(this, area);
        },

        /**
         * Creates handler for applying action (e.g. selectAll)
         * @param  {String} action
         * @return {Function} - click handler
         */
        onApplySelect: function (action) {
            return function(){
                this.menuVisible(false);
                this[action]();
            }.bind(this);
        },

        /**
         * Updates state according to changes of provider.
         */
        onRefresh: function () {
            if( this.allSelected() ){
                this.selected(this.getIds(true));
            }

            this.countPages();
        },

        onSelectionsChange: function(selected){
            this.updateExcluded(selected)
                .countSelect();
        }
    });

    return Component({
        constr: MassActions
    });
});