'use strict';
var app = angular.module('magentoSetup', ['ui.router', 'ui.bootstrap']);
app.controller('navigationController', ['$scope', 'navigationService', function ($scope, navigationService) {
        navigationService.load();
    }])
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
    .controller('landingController', ['$scope', '$state', 'navigationService', function ($scope, $state, navigationService) {
        $scope.next = function () {
            $state.go(navigationService.getNextState().id);
        };
    }])
    .controller('mainController', ['$scope', function ($scope) {
        console.log('mainController');
    }])
    .service('navigationService', ['$location', '$state', '$http', function ($location, $state, $http) {
        return {
            states: [],
            load: function () {
                var self = this;
                $http.get('data/states').success(function (data) {
                    var currentState = $location.path().replace('/', '');
                    var mainState = {};
                    var isCurrentStateFound = false;
                    self.states = data.nav;
                    data.nav.forEach(function (item) {
                        app.stateProvider.state(item.id, item);
                        if (item.main) {
                            mainState = item;
                        }

                        if (currentState == item.url) {
                            $state.go(item.id);
                            isCurrentStateFound = true;
                        }
                    });
                    if (!isCurrentStateFound) {
                        $state.go(mainState.id);
                    }
                });
            },
            getNextState: function () {
                var nItem = {};
                this.states.forEach(function (item) {
                    if (item.step == $state.$current.step + 1) {
                        nItem = item;
                    }
                });
                return nItem;
            }
        }
    }])
    .config(function ($stateProvider) {
        app.stateProvider = $stateProvider;
    }).run(function ($rootScope, $state) {
        $rootScope.$state = $state;
    });
