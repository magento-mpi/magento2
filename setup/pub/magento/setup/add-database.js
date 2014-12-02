/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('add-database', ['ngStorage'])
    .controller('addDatabaseController', ['$scope', '$state', '$localStorage', '$http', '$timeout', 'Storage', function ($scope, $state, $localStorage, $http, $timeout, Storage) {
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
                });
        };

        $scope.$on('nextState', function () {
            $localStorage.db = $scope.db;
            Storage.db = $scope.db;
        });

        // Listens on form validate event, dispatched by parent controller
        $scope.$on('validate-' + $state.current.id, function() {
            $scope.validate();
        });

        // Dispatch 'validation-response' event to parent controller
        $scope.validate = function() {
            $scope.testConnection();
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
    }])
    .directive('testHostname', function() {
        return{
            require: "ngModel",
            link: function(scope, elm, attrs, ctrl){
                var validator = function(value){
                    scope.testConnection();
                    var isValid;
                    if (scope.testConnection.result.success === undefined) {
                        isValid = false;
                    } else {
                        isValid = true;
                    }
                    ctrl.$setValidity('testHostname', isValid);
                    return value;
                };
                ctrl.$parsers.unshift(validator);
                ctrl.$formatters.unshift(validator);
            }
        };
    });
