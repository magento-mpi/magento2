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
    .controller('landingController', ['$scope', function ($scope) {
        console.log('landingController');
    }])
    .controller('mainController', [
        '$scope', '$rootScope', '$state', 'navigationService',
        function ($scope, $rootScope, $state, navigationService) {
            $rootScope.$on('$stateChangeSuccess', function (event, state) {
                $scope.class = 'col-lg-9';
                if (state.main) {
                    $scope.class = 'col-lg-12';
                }
            });

            $scope.nextState = function () {
                $state.go(navigationService.getNextState().id);
            }
        }
    ])
    .service('navigationService', ['$location', '$state', '$http', function ($location, $state, $http) {
        return {
            mainState: {},
            states: [],
            load: function () {
                var self = this;
                $http.get('data/states').success(function (data) {
                    var currentState = $location.path().replace('/', '');
                    var isCurrentStateFound = false;
                    self.states = data.nav;
                    data.nav.forEach(function (item) {
                        app.stateProvider.state(item.id, item);
                        if (item.main) {
                            self.mainState = item;
                        }

                        if (currentState == item.url) {
                            $state.go(item.id);
                            isCurrentStateFound = true;
                        }
                    });
                    if (!isCurrentStateFound) {
                        $state.go(self.mainState.id);
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
            },
            getMainState: function () {
                return this.mainState;
            }
        }
    }])
    .config(function ($stateProvider) {
        app.stateProvider = $stateProvider;
    }).run(function ($rootScope, $state) {
        $rootScope.$state = $state;
    });
