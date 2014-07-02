'use strict';
var app = angular.module('magentoSetup', ['ui.router', 'ui.bootstrap', 'readiness-check']);
app.controller('navigationController', ['$scope', 'navigationService', function ($scope, navigationService) {
        navigationService.load();
    }])
    .controller('addDatabaseController', ['$scope', function ($scope) {
        console.log('addDatabaseController');
    }])
    .controller('webConfigurationController', ['$scope', function ($scope) {

        $scope.webAddress = {
            value: 'http://www.example.com/',
            hint: false
        };

        $scope.adminAddress = '';

        $scope.httpsOptions = {
            storefront: true,
            admin: true
        };

        $scope.rewrites = {
            allowed: true
        };

        $scope.encryptionKey = {
            value: '',
            type: 'magento',
            hint: false
        };

        $scope.expanded = false;

        $scope.updateOnExpand = function() {
            $scope.expanded = !$scope.expanded;
        }

        $scope.showHint = function(element) {
            element.hint = true;
        }

        $scope.showEncryptionKey = function() {
            return angular.equals($scope.encryptionKey.type, 'customer');
        }
    }])
    .controller('customizeYourStoreController', ['$scope', function ($scope) {
        console.log('customizeYourStoreController');
    }])
    .controller('createAdminAccountController', ['$scope', function ($scope) {
        console.log('createAdminAccountController');
    }])
    .controller('landingController', ['$scope', '$location', 'languageService', function ($scope, $location, languageService) {
        $scope.selectLanguage = function () {
            window.location = '/setup/' + $scope.modelLanguage.code + '/index';
        };
        languageService.load(function (response) {
            $scope.languages = response.data.languages;
            var indexOf = $location.absUrl().search('setup/') + 6;
            var value = $location.absUrl().slice(indexOf, indexOf + 5);
            $scope.languages.forEach(function (lang, index) {
                if (lang.code == value) {
                    $scope.modelLanguage = $scope.languages[index];
                }
            });

        });

    }])
    .controller('installController', ['$scope', function ($scope) {
        console.log('installController');
    }])
    .controller('mainController', [
        '$scope', '$rootScope', '$state', 'navigationService',
        function ($scope, $rootScope, $state, navigationService) {
            $rootScope.$on('$stateChangeSuccess', function (event, state) {
                $scope.class = 'col-lg-9';
                if (state.main) {
                    $scope.class = 'col-lg-offset-3 col-lg-6';
                }
            });

            $scope.nextState = function () {
                $state.go(navigationService.getNextState().id);
            }
            $scope.previousState = function () {
                $state.go(navigationService.getPreviousState().id);
            }
        }
    ])
    .service('languageService', ['$http', function ($http) {
        return {
            languages: [],
            load: function (callback) {
                var self = this;
                $http.get('data/languages').success(function (data) {
                    self.languages = data.languages;
                }).then(callback);
            }
        }
    }])
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
                    if (item.order == $state.$current.order + 1) {
                        nItem = item;
                    }
                });
                return nItem;
            },
            getPreviousState: function () {
                var nItem = {};
                this.states.forEach(function (item) {
                    if (item.order == $state.$current.order - 1) {
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
    })
    .config(function($provide) {
        $provide.decorator('$state', function($delegate, $stateParams) {
            $delegate.forceReload = function() {
                return $delegate.go($delegate.current, $stateParams, {
                    reload: true,
                    inherit: false,
                    notify: true
                });
            };
            return $delegate;
        });
    });
