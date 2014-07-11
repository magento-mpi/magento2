'use strict';
var install = angular.module('install', [])
    .controller('installController', ['$scope', '$sce', '$timeout', 'progress', function ($scope, $sce, $timeout, progress) {
        $scope.isStart = false;
        $scope.console = false;
        $scope.toggleConsole = function () {
            $scope.console = $scope.console === false;
        };

        $scope.start = function () {
            $scope.isStart = true;
            progress.get(function (response) {
                $scope.progress = response.data.progress;
                var log = '';
                response.data.console.forEach(function (message) {
                    log = log + message + '<br>';
                });
                $scope.log = $sce.trustAsHtml(log);

                if ($scope.progress == 100) {
                    $scope.nextState();
                } else {
                    $timeout(function() {
                        $scope.start();
                    }, 2500);
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