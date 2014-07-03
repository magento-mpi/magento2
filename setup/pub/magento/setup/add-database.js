'use strict';
var addDatabase = angular.module('add-database', ['ngStorage']);
addDatabase.controller('addDatabaseController', ['$scope', '$localStorage', function ($scope, $localStorage, $http) {
    $scope.db = {};
    $scope.db.useExistingDb = 1;
    $scope.db.useAccess = 1;
    if ($localStorage.db) {
        $scope.db = $localStorage.db;
    }

    $scope.testConnection = function () {
        $http.get('')
    };

    $scope.$on('nextState', function (state) {
        if (state.id == 'root.add-database') {
            $localStorage.db = $scope.db;
        }
    });
}]);
