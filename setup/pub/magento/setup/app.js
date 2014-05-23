'use strict';

angular.module('magentoSetup', [
        'ngRoute',
//    'magentoSetup.filters',
        'magentoSetup.services',
//    'magentoSetup.directives',
        'magentoSetup.controllers'
    ]).config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/license', {templateUrl: 'license', controller: 'LicenseCtrl'});
        $routeProvider.when('/checkEnvironment', {templateUrl: 'check-environment', controller: 'CheckEnvironmentCtrl'});
        $routeProvider.otherwise({redirectTo: '/license'});
    }]);