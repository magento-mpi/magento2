define([
    'angular',
    './module',
    'text!../tmpl/header-cell.html'
], function(ng, directives, cellTemplate){
    
    directives.directive('mageGridHeaderCell', function(){
        return {
            restrict: 'AE',
            template: cellTemplate
        }
    });

});