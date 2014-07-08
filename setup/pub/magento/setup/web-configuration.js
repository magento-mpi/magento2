'use strict';
var webConfiguration = angular.module('web-configuration', ['ngStorage']);
webConfiguration.controller('webConfigurationController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.wc = {};

    $scope.wc.address = {
        web: 'http://www.example.com/',
        admin: 'admin'
    };

    $scope.wc.https = {
        front: true,
        admin: true
    };

    $scope.wc.rewrites = {
        allowed: true
    };

    $scope.wc.encrypt = {
        key: '',
        type: 'magento'
    };

    $scope.wc.advanced = {
        expanded: false
    };

    if ($localStorage.wc) {
        $scope.wc = $localStorage.wc;
    }

    $scope.$on('nextState', function () {
        $localStorage.wc = $scope.wc;
    });

    $scope.updateOnExpand = function(obj) {
        obj.expanded = !obj.expanded;
    };

    $scope.showEncryptKey = function() {
        return angular.equals($scope.wc.encrypt.type, 'user');
    }
}]);