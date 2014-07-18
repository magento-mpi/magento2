'use strict';
angular.module('install', ['ngStorage'])
    .controller('installController', ['$scope', '$sce', '$timeout', '$localStorage', 'progress', function ($scope, $sce, $timeout, $localStorage, progress) {
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

                    if ($scope.progress == 100) {
                        $scope.nextState();
                    } else {
                        $timeout(function() {
                            $scope.checkProgress();
                        }, 1500);
                    }
                } else {
                    $scope.progress = 100;
                    $scope.progressText = $scope.errorStatus;
                    $scope.error = true;
                    $scope.disabled = false;
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
            progress.post(data);
            $scope.checkProgress();
        };
    }])
    .service('progress', ['$http', function ($http) {
        return {
            get: function (callback) {
                $http.get('install/progress').then(callback);
            },
            post: function (data) {
                $http.post('install/start', data);
            }
        };
    }]);