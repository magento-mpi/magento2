'use strict';
angular.module('success', ['ngStorage'])
    .controller('successController', ['$scope', '$localStorage', '$http', function ($scope, $localStorage, $http) {
        $scope.db     = $localStorage.db;
        $scope.admin  = $localStorage.admin;
        $scope.config = $localStorage.config;
        $http.get('success/encryption').then(function (response) {
            $scope.config.encrypt.key = response.data.key;
        });
    }]);