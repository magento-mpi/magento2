'use strict';
angular.module('success', ['ngStorage'])
    .controller('successController', ['$scope', '$localStorage', function ($scope, $localStorage) {
        $scope.db     = $localStorage.db;
        $scope.admin  = $localStorage.admin;
        $scope.config = $localStorage.config;
    }]);