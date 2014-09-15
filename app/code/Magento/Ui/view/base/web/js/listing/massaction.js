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

    var defaults = {
        actions: [],
        selects: [
            { value: 'selectAll',    label: 'Select all'                },
            { value: 'selectPage',   label: 'Select all on this page'   },
            { value: 'deselectAll',  label: 'Deselect all'              },
            { value: 'deselectPage', label: 'Deselect all on this page' }
        ],
        indexField: '',
        idAttribute: 'id_attribute',
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
                .initProvider();
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
                menuVisible:        false
            });

            this.selected.subscribe(this.exclude.bind(this));

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
            var client = this.provider.client,
                config,
                data;

            config = {
                method: 'post',
                action: action.url
            };

            data = {
                massaction: this.buildParams()
            };

            client.submit(config, data);
            
            return this;
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
            this.selected([]);
        },

        exclude: function(selected){
            var all         = this.getIds(),
                excluded    = this.excluded();

            excluded = _.union( excluded, _.difference(all, selected) ) );

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

        getIds: function(){
            var items = this.provider.data.get('items');

            return _.pluck(items, this.indexField);
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
                this.selected( _.difference(this.getIds(), this.excluded()) );
            }
        }
    });

    return Component({
        constr: MassActions
    });
});