'use strict';
var addDatabase = angular.module('add-database', []);
addDatabase.controller('addDatabaseController', function ($scope) {
    $scope.useExistingDb = 1;
    $scope.testConnection = function () {
        console.log('test');
        console.log($scope.$parent);
        console.log($scope.dbHost);
    };
});
