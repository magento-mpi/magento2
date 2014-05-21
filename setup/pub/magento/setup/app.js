'use strict';

angular.module('magentoSetup', [
    'ngRoute',
//    'magentoSetup.filters',
    'magentoSetup.services',
//    'magentoSetup.directives',
    'magentoSetup.controllers'
]).config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/license', {templateUrl: 'setup/license', controller: 'LicenseCtrl'});
    $routeProvider.when('/checkEnvironment', {templateUrl: 'setup/check-environment', controller: 'CheckEnvironmentCtrl'});
    $routeProvider.otherwise({redirectTo: '/license'});
}]);