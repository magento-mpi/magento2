/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('readiness-check', [])
    .constant('COUNTER', 1)
    .controller('readinessCheckController', ['$rootScope', '$scope', '$http', '$timeout', 'COUNTER', function ($rootScope, $scope, $http, $timeout, COUNTER) {
        $scope.progressCounter = COUNTER;
        $scope.startProgress = function() {
            ++$scope.progressCounter;
        };
        $scope.stopProgress = function() {
            --$scope.progressCounter;
            if ($scope.progressCounter == COUNTER) {
                $scope.resetProgress();
            }
        };
        $scope.resetProgress = function() {
            $scope.progressCounter = 0;
        };
        $rootScope.checkingInProgress = function() {
            return $scope.progressCounter > 0;
        };

        $scope.completed = false;
        $scope.hasErrors = false;

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

        $scope.items = {
            'php-version': {
                url:'data/php-version',
                show: function() {
                    $scope.startProgress();
                    $scope.version.visible = true;
                },
                process: function(data) {
                    $scope.version.processed = true;
                    angular.extend($scope.version, data);
                    $scope.updateOnProcessed($scope.version.responseType);
                    $scope.stopProgress();
                }
            },
            'php-extensions': {
                url:'data/php-extensions',
                show: function() {
                    $scope.startProgress();
                    $scope.extensions.visible = true;
                },
                process: function(data) {
                    $scope.extensions.processed = true;
                    angular.extend($scope.extensions, data);
                    $scope.updateOnProcessed($scope.extensions.responseType);
                    $scope.stopProgress();
                }
            },
            'file-permissions': {
                url:'data/file-permissions',
                show: function() {
                    $scope.startProgress();
                    $scope.permissions.visible = true;
                },
                process: function(data) {
                    $scope.permissions.processed = true;
                    angular.extend($scope.permissions, data);
                    $scope.updateOnProcessed($scope.permissions.responseType);
                    $scope.stopProgress();
                }
            }
        };

        $scope.isCompleted = function() {
            return $scope.version.processed
                && $scope.extensions.processed
                && $scope.permissions.processed;
        };

        $scope.updateOnProcessed = function(value) {
            $rootScope.hasErrors = $scope.hasErrors || (value != 'success');
        };

        $scope.updateOnError = function(obj) {
            obj.expanded = true;
        };

        $scope.updateOnSuccess = function(obj) {
            obj.expanded = false;
        };

        $scope.updateOnExpand = function(obj) {
            obj.expanded = !obj.expanded;
        };

        $scope.hasItem = function(haystack, needle) {
            return haystack.indexOf(needle) > -1;
        };

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
                timeout += 500;
                $timeout(function() {
                    $scope.query(item);
                }, timeout);
            });
        };

        $scope.$on('$stateChangeSuccess', function (event, nextState) {
            if (nextState.id == 'root.readiness-check.progress') {
                $scope.progress();
            }
        });
    }]);
