'use strict';
var addDatabase = angular.module('add-database', ['ngStorage']);
addDatabase.controller('addDatabaseController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.db = {};
    $scope.db.useExistingDb = 1;
    if ($localStorage.db) {
        $scope.db = $localStorage.db;
    }
    $scope.testConnection = function () {
        //@todo implemented this action
    };

    $scope.$on('nextState', function (state) {
        if (state.id == 'root.add-database') {
            $localStorage.db = $scope.db;
        }
    });
}]);
