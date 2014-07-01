'use strict';
var readinessCheck = angular.module('readiness-check', []);
readinessCheck.controller('readinessCheckController', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {

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
                $scope.updateOnProcessed($scope.version.responseType);

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
                $scope.updateOnProcessed($scope.extensions.responseType);
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
                $scope.updateOnProcessed($scope.permissions.responseType);
            }
        }
    ];

    $scope.isCompleted = function() {
        return $scope.version.processed
            && $scope.extensions.processed
            && $scope.permissions.processed;
    }

    $scope.updateOnProcessed = function(value) {
        $scope.hasErrors = $scope.hasErrors || (value != 'success');
    }

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
            timeout += 500;
            $timeout(function() {
                $scope.query(item);
            }, timeout);
        })
    };

    $scope.progress();
}]);
