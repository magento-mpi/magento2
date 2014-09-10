define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component'
], function(_, Scope, Component) {
    'use strict';
    
    var defaults = {
        dirs: {
            asc: 'sort-arrow-asc',
            desc: 'sort-arrow-desc'
        },
        initialDir: 'asc',
        noSort: 'not-sort'
    };

    var Sorting = Scope.extend({

        /**
         * Extends instance with defaults and config, initializes observable properties.
         * Updates storage with current state of instance. 
         * @param  {Object} config
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .updateParams();
        },

        /**
         * Initializes observable properties of instance.
         * @return {Object} - reference to instance
         */
        initObservable: function(){
            this.observe({
                field:      this.params.field,
                direction:  this.params.direction
            });

            return this;
        },

        /**
         * Generates css class for indicating sorting state for field. 
         * @param {String} id - identifier of field to be sorted
         * @returns {String} - css class.
         */
        setClass: function(id) {
            return this.isSorted(id) ?
                this.dirs[this.direction()] :
                this.noSort;
        },

        /**
         * Toggles observable dir property betweeen 'asc' and 'desc' values.
         */
        toggleDirection: function() {
            var dir = this.direction;

            dir(dir() === 'asc' ? 'desc' : 'asc');
        },

        /**
         * Sets currently sorted field and initial sorting type for it.
         * @param {String} id - identifier of field to be sorted
         */
        setSort: function(id) {
            this.field(id);
            this.direction(this.initialDir);
        },

        /**
         * Sorts by field and reloads storage.
         * @param  {String]} id - identifier of field to be sorted
         */
        sortBy: function(id) {
            this.isSorted(id) ?
                this.toggleDirection() :
                this.setSort(id);

            this.reload();
        },

        /**
         * Updates storage's params and reloads it.
         */
        reload: function() {
            this.updateParams().storage.load();
        },

        /**
         * Updates storage's params by the current state of instance
         * @return {Object} - reference to instance
         */
        updateParams: function() {
            this.storage.setParams({
                sorting: {
                    field: this.field(),
                    direction: this.direction()
                }
            });

            return this;
        },

        /**
         * Checks if the field is currently sorted.
         * @param  {String} id - identifier of field to be sorted
         * @return {Boolean} true, if target field is sorted already, false otherwise
         */
        isSorted: function(id) {
            return id === this.field();
        },

        /**
         * Returns function to handle user's click (workaround for knockout.js).
         * @param  {Object} field
         * @return {Function} - click handler
         */
        onClick: function(field) {
            return function(){
                this.sortBy(field.index)
            }.bind(this);
        }
    });

    return Component({
        name:   'sorting',
        constr: Sorting
    });
});