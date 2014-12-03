/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('add-database', ['ngStorage'])
    .controller('addDatabaseController', ['$scope', '$state', '$localStorage', '$http', '$timeout', 'Storage', '$interval', function ($scope, $state, $localStorage, $http, $timeout, Storage, $interval) {
        $scope.db = {
            useExistingDb: 1,
            useAccess: 1
        };

        $scope.testConn = '';

        var intervalPromise = $interval(function () {
            $http.post('data/database', $scope.db)
                .success(function (data) {
                    $scope.testConnection.result = data;
                    if (($scope.testConnection.result !== undefined) && (!$scope.testConnection.result.success)) {
                        $scope.testConn = '';
                    } else {
                        $scope.testConn = 'mock';
                    }
                });
        }, 500);

        $scope.$on('$destroy', function () { $interval.cancel(intervalPromise); });

        if ($localStorage.db) {
            $scope.db = $localStorage.db;
        }

        $scope.testConnection = function () {
            $http.post('data/database', $scope.db)
                .success(function (data) {
                    $scope.testConnection.result = data;
                });
        };

        $scope.$on('nextState', function () {
            $localStorage.db = $scope.db;
            Storage.db = $scope.db;
            $interval.cancel(intervalPromise);
        });

        $scope.$on('previousState()', function () {
            $interval.cancel(intervalPromise);
        });


        // Listens on form validate event, dispatched by parent controller
        $scope.$on('validate-' + $state.current.id, function() {
            $scope.validate();
        });

        // Dispatch 'validation-response' event to parent controller
        $scope.validate = function() {
            if ($scope.database.$valid) {
                $scope.$emit('validation-response', true);
            } else {
                $scope.$emit('validation-response', false);
                $scope.database.submitted = true;
            }
        }

        // Update 'submitted' flag
        $scope.$watch(function() { return $scope.database.$valid }, function(valid) {
            if (valid) {
                $scope.database.submitted = false;
            }
        });
    }]);
