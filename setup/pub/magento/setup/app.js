'use strict';
var app = angular.module('magentoSetup', ['ui.router']);
app.controller('navigationController',
        ['$scope', '$location', '$state', 'navigationService',
            function ($scope, $location, $state, navigationService) {
                navigationService.load(function (data) {
                    data.nav.forEach(function (item) {
                        app.stateProvider.state(item.id, {
                            url: item.url,
                            templateUrl: item.templateUrl,
                            controller: item.controller + 'Controller',
                            title: item.title,
                            step: item.step
                        });
                    });
                    $state.go($location.path().replace('/', ''));
                });
            }
        ]
    )
    .controller('readinessCheckController', ['$scope', function ($scope) {
        console.log('readinessCheckController');
    }])
    .controller('addDatabaseController', ['$scope', function ($scope) {
        console.log('addDatabaseController');
    }])
    .controller('webConfigurationController', ['$scope', function ($scope) {
        console.log('webConfigurationController');
    }])
    .controller('customizeYourStoreController', ['$scope', function ($scope) {
        console.log('customizeYourStoreController');
    }])
    .controller('createAdminAccountController', ['$scope', function ($scope) {
        console.log('createAdminAccountController');
    }])
    .controller('installController', ['$scope', function ($scope) {
        console.log('installController');
    }])
    .controller('landingController', ['$scope', function ($scope) {
        console.log('landingController');
    }])
    .controller('mainController', ['$scope', function ($scope) {
        console.log('mainController');
    }])
    .service('navigationService', ['$http', function ($http) {
        return {
            load: function (callback) {
                $http.get('data/states').success(callback);
            }
        }
    }])
    .config(function ($stateProvider, $urlRouterProvider) {
        app.stateProvider = $stateProvider;
        app.urlRouterProvider = $urlRouterProvider;
    }).run(function ($rootScope, $state) {
        $rootScope.$state = $state;
    });
