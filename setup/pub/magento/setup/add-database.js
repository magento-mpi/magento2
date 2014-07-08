'use strict';
var addDatabase = angular.module('add-database', ['ngStorage']);
addDatabase.controller('addDatabaseController', ['$scope', '$localStorage', function ($scope, $localStorage, $http) {
    $scope.db = {
        useExistingDb: 1,
        useAccess: 1
    };

    if ($localStorage.db) {
        $scope.db = $localStorage.db;
    }

    $scope.testConnection = function () {
        $http.get(''); //@todo
    };

    $scope.$on('nextState', function () {
        $localStorage.db = $scope.db;
    });
}]);
