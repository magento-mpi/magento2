'use strict';
var app = angular.module('magentoSetup', ['ui.router', 'ui.bootstrap']);
app.controller('navigationController', ['$scope', 'navigationService', function ($scope, navigationService) {
        navigationService.load();
    }])
    .controller('readinessCheckController', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
        $scope.version = {
            visible: false,
            processed: false,
            expanded: false
        };
        $scope.extensions = {
            visible: false,
            processed: false,
            expanded: false
        };
        $scope.permissions = {
            visible: false,
            processed: false,
            expanded: false
        };

        $scope.items = [
            {
                id:'php-version',
                url:'data/php-version',
                show: function() {
                    $scope.version.visible = true;
                },
                process: function(data) {
                    $scope.version.processed = true;
                    angular.extend($scope.version, data);
                }
            },
            {
                id:'php-extensions',
                url:'data/php-extensions',
                show: function() {
                    $scope.extensions.visible = true;
                },
                process: function(data) {
                    $scope.extensions.processed = true;
                    angular.extend($scope.extensions, data);
                }
            },
            {
                id:'file-permissions',
                url:'data/file-permissions',
                show: function() {
                    $scope.permissions.visible = true;
                },
                process: function(data) {
                    $scope.permissions.processed = true;
                    angular.extend($scope.permissions, data);
                }
            }
        ];

        $scope.updateOnError = function(obj) {
            obj.expanded = true;
        }

        $scope.updateOnSuccess = function(obj) {
            obj.expanded = false;
        }

        $scope.updateOnExpand = function(obj) {
            obj.expanded = !obj.expanded;
        }

        $scope.hasItem = function(haystack, needle) {
            if (haystack.indexOf(needle) > -1) {
                return true;
            }
            return false;
        }

        $scope.query = function(item) {
            return $http.get(item.url)
                .success(function(data) { item.process(data) });
        };

        $scope.progress = function() {
            var timeout = 0;
            angular.forEach($scope.items, function(item) {
                timeout += 1000;
                $timeout(function() {
                    item.show();
                }, timeout);
            });
            angular.forEach($scope.items, function(item) {
                timeout += 1000;
                $timeout(function() {
                    $scope.query(item);
                }, timeout);
            })
        };

        $scope.progress();
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
    .controller('landingController', ['$scope', 'languageService', function ($scope, languageService) {
        $scope.selectLanguage = function (data) {
            console.log(data);
        };
        languageService.load(function (response) {
            $scope.languages = response.data.languages;
        });
        $scope.currentLanguage = {code: 'en_US', title: 'United State'};
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
