'use strict';
angular.module('install', [])
    .controller('installController', ['$scope', '$sce', '$timeout', 'progress', function ($scope, $sce, $timeout, progress) {
        $scope.isStart = false;
        $scope.console = false;
        $scope.disabled = false;
        $scope.toggleConsole = function () {
            $scope.console = $scope.console === false;
        };

        $scope.start = function () {
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
                            $scope.start();
                        }, 2500);
                    }
                } else {
                    $scope.progress = 100;
                    $scope.progressText = $scope.errorStatus;
                    $scope.error = true;
                    $scope.disabled = false;
                }
            });
        };
    }])
    .service('progress', ['$http', function ($http) {
        return {
            get: function (callback) {
                $http.get('install/progress').then(callback);
            }
        };
    }]);