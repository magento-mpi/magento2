define([
    'angular',
    './module',
    'text!../tmpl/grid.html'
], function(ng, directives, gridTemplate){
    
    directives.directive('mageGrid', function(){
        return {
            restrict: 'AE',
            template: gridTemplate
        }
    });

});