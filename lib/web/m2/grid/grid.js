define([
    'angular',
    './directives/index'
], function(ng){
    var ngMageGrid = ng.module('mageGrid', [
            'mageGrid.directives'
        ]); 

    return ngMageGrid;
});