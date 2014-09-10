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
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .updateParams();
        },

        initObservable: function(){
            this.observe({
                field:      this.field,
                direction:  this.direction
            });

            return this;
        },

        setClass: function(id) {
            return this.isSorted(id) ?
                this.dirs[this.direction()] :
                this.noSort;
        },

        toggleDirection: function() {
            var dir = this.direction;

            dir(dir() === 'asc' ? 'desc' : 'asc');
        },

        setSort: function(id) {
            this.field(id);
            this.direction(this.initialDir);
        },

        handleClick: function(id) {
            this.isSorted(id) ?
                this.toggleDirection() :
                this.setSort(id);

            this.reload();
        },

        reload: function() {
            this.updateParams()
                .provider.refresh();
        },

        updateParams: function() {
            var params = this.provider.params;

            params.set('sorting', {
                field:      this.field(),
                direction:  this.direction()
            });

            return this;
        },

        isSorted: function(id) {
            return id === this.field();
        },

        onClick: function(field) {
            return function(){
                this.handleClick(field.index)
            }.bind(this);
        }
    });

    return Component({
        constr: Sorting
    });
});