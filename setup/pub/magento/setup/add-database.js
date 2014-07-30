/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('add-database', ['ngStorage'])
    .controller('addDatabaseController', ['$scope', '$state', '$localStorage', '$http', '$timeout', function ($scope, $state, $localStorage, $http, $timeout) {
        $scope.db = {
            useExistingDb: 1,
            useAccess: 1
        };

        if ($localStorage.db) {
            $scope.db = $localStorage.db;
        }

    $scope.testConnection = function () {
        $http.post('data/database', $scope.db)
            .success(function (data) {
                $scope.testConnection.result = data;
            })
            .then(function () {
                $scope.testConnection.pressed = true;
                $timeout(function () {
                    $scope.testConnection.pressed = false;
                }, 2500);
            });
    };

        $scope.$on('nextState', function () {
            $localStorage.db = $scope.db;
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
