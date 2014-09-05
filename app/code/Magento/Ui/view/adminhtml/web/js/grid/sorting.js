define([
    '_',
    'Magento_Ui/js/lib/ko/scope',
    './core/component'
], function(_, Scope, Component) {
    'use strict';
    
    var Sorting = Scope.extend({
        initialize: function(config) {
            this.dirs = {
                asc: 'sort-arrow-asc',
                desc: 'sort-arrow-desc'
            };

            this.defaultDir = 'asc';

            this.storage = config.storage;

            this.observe({
                field:      config.params.field,
                direction:  config.params.direction
            });

            this.updateParams();
        },

        setClass: function(id) {
            return this.isSorted(id) ?
                this.dirs[this.direction()] :
                'not-sort';
        },

        toggleDirection: function() {
            var dir = this.direction;

            dir(dir() === 'asc' ? 'desc' : 'asc');
        },

        setSort: function(id) {
            this.field(id);
            this.direction(this.defaultDir);
        },

        handleClick: function(id) {
            this.isSorted(id) ?
                this.toggleDirection() :
                this.setSort(id);

            this.reload();
        },

        reload: function() {
            this.updateParams().storage.load();
        },

        updateParams: function() {
            this.storage.setParams({
                sorting: {
                    field: this.field(),
                    direction: this.direction()
                }
            });

            return this;
        },

        isSorted: function(id) {
            return id === this.field();
        },

        onClick: function(field) {
            return function(){
                return this.handleClick(this, field.id)
            }
        }
    });

    return Component({
        name:   'sorting',
        constr: Sorting
    });
});