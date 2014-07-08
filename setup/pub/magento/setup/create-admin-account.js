'use strict';
var createAdminAccount = angular.module('create-admin-account', ['ngStorage']);
createAdminAccount.controller('createAdminAccountController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.admin = {};
    $scope.admin.passwordStatus = {
        class: 'none',
        label: 'None'
    };

    $scope.passwordStatusChange = function () {
        var p = $scope.admin.password;
        if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/) && p.match(/[!@#$%^*()_\/\\\-\+=]+/)) {
            $scope.admin.passwordStatus.class = 'strong';
            $scope.admin.passwordStatus.label = 'Strong';
        } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/)) {
            $scope.admin.passwordStatus.class = 'good';
            $scope.admin.passwordStatus.label = 'Good';
        } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-zA-Z]+/)) {
            $scope.admin.passwordStatus.class = 'weak';
            $scope.admin.passwordStatus.label = 'Weak';
        } else if (p.length > 6) {
            $scope.admin.passwordStatus.class = 'to-short';
            $scope.admin.passwordStatus.label = 'To Short';
        } else {
            $scope.admin.passwordStatus.class = 'none';
            $scope.admin.passwordStatus.label = 'None';
        }
    };

    if ($localStorage.admin) {
        $scope.admin = $localStorage.admin;
    }

    $scope.$on('nextState', function () {
        $localStorage.admin = $scope.admin;
    })
}]);
