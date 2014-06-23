'use strict';
var app = angular.module('magentoSetup', ['ui.router']);
app.controller('navigationController', [
        '$scope',
        'navigationService',
        function ($scope, navigationService) {
            navigationService.load(function (data) {
                $scope.nav = data.nav;
                data.nav.forEach(function (item) {
                    app.stateProvider.state(
                        item.url, {
                            'url': item.url,
                            'controller': item.controller + 'Controller'
                        }
                    );
                });
            });
        }
    ])
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
    .controller('mainController', ['$scope', function ($scope) {
        console.log('mainController');
    }])
    .service('navigationService', ['$cacheFactory', '$http', function ($cacheFactory, $http) {
        return {
            load: function (callback) {
                $http.get('menu').success(callback);
            }
        }
    }])
    .config(function ($stateProvider) {
        $stateProvider.state('navigation', {
            controller: 'navigationController'
        });
        app.stateProvider = $stateProvider;
    });
