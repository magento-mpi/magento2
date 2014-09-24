/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('install', ['ngStorage'])
    .controller('installController', ['$scope', '$sce', '$timeout', '$localStorage', '$rootScope', 'progress', function ($scope, $sce, $timeout, $localStorage, $rootScope, progress) {
        $scope.finished = false;
        $scope.isStart = false;
        $scope.console = false;
        $scope.disabled = false;
        $scope.toggleConsole = function () {
            $scope.console = $scope.console === false;
        };

        $scope.checkProgress = function () {
            $scope.isStart = true;
            $scope.disabled = true;
            progress.get(function (response) {

                var log = '';
                response.data.console.forEach(function (message) {
                    log = log + message + '<br>';
                });
                $scope.log = $sce.trustAsHtml(log);

                if (response.data.success) {
                    $scope.progress = response.data.progress;
                    $scope.progressText = response.data.progress + '%';

                    $timeout(function() {
                        if (!$scope.finished) {
                            $scope.checkProgress();
                        }
                    }, 1500);
                } else {
                    $scope.progress = 100;
                    $scope.progressText = $scope.errorStatus;
                    $scope.error = true;
                    $scope.disabled = false;
                    $rootScope.isMenuEnabled = true;
                }
            });
        };

        $scope.start = function () {
            var data = {
                'db': $localStorage.db,
                'admin': $localStorage.admin,
                'store': $localStorage.store,
                'config': $localStorage.config
            };
            $rootScope.isMenuEnabled = false;
            progress.post(data, function (response) {
                $localStorage.config.encrypt.key = response.key;
                if (response.success) {
                    $scope.finished = true;
                    $scope.nextState();
                }
            });
            progress.clear(function (response) {
                if (response.data.success) {
                    $scope.checkProgress();
                }
            });
        };
    }])
    .service('progress', ['$http', function ($http) {
        return {
            get: function (callback) {
                $http.get('install/progress').then(callback);
            },
            post: function (data, callback) {
                $http.post('install/start', data).success(callback);
            },
            clear: function (callback) {
                $http.get('install/clear-progress').then(callback);
            }
        };
    }]);
