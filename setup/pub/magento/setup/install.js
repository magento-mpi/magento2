/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('install', ['ngStorage'])
    .controller('installController', ['$scope', '$sce', '$timeout', '$localStorage', '$rootScope', 'progress', 'Storage', function ($scope, $sce, $timeout, $localStorage, $rootScope, progress, Storage) {
        $scope.isStarted = false;
        $scope.isInProgress = false;
        $scope.isConsole = false;
        $scope.isDisabled = false;
        $scope.toggleConsole = function () {
            $scope.isConsole = $scope.isConsole === false;
        };

        $scope.barStyle = function (value) {
            return { width: value + '%' };
        };

        $scope.checkProgress = function () {
            if ($scope.isInProgress) {
                $scope.displayProgress();
            }
            progress.get(function (response) {
                var log = '';
                response.data.console.forEach(function (message) {
                    log = log + message + '<br>';
                });
                $scope.log = $sce.trustAsHtml(log);

                if (response.data.success) {
                    $scope.progress = response.data.progress;
                    $scope.progressText = response.data.progress + '%';
                } else {
                    $scope.displayFailure();
                }
                if ($scope.isInProgress) {
                    $timeout(function() {
                        $scope.checkProgress();
                    }, 1500);
                }
            });
        };

        $scope.start = function () {
            var data = {
                'db': Storage.db,
                'admin': Storage.admin,
                'store': Storage.store,
                'config': Storage.config
            };
            $scope.isStarted = true;
            $scope.isInProgress = true;
            progress.post(data, function (response) {
                $scope.isInProgress = false;
                if (response.success) {
                    Storage.config.encrypt.key = response.key;
                    Storage.messages = response.messages;
                    $scope.nextState();
                } else {
                    $scope.displayFailure();
                }
            });
            progress.get(function () {
                $scope.checkProgress();
            });
        };
        $scope.displayProgress = function() {
            $scope.isFailed = false;
            $scope.isDisabled = true;
            $rootScope.isMenuEnabled = false;
        };
        $scope.displayFailure = function () {
            $scope.isFailed = true;
            $scope.isDisabled = false;
            $rootScope.isMenuEnabled = true;
        };
    }])
    .service('progress', ['$http', function ($http) {
        return {
            get: function (callback) {
                $http.post('install/progress').then(callback);
            },
            post: function (data, callback) {
                $http.post('install/start', data).success(callback);
            }
        };
    }]);
