'use strict';
var webConfiguration = angular.module('web-configuration', []);
webConfiguration.controller('webConfigurationController', ['$scope', function ($scope) {
    $scope.address = {
        web: 'http://www.example.com/',
        admin: ''
    };

    $scope.https = {
        front: true,
        admin: true
    };

    $scope.rewrites = {
        allowed: true
    };

    $scope.encrypt = {
        key: '',
        type: 'magento'
    };

    $scope.advanced = {
        expanded: false
    };

    $scope.updateOnExpand = function(obj) {
        obj.expanded = !obj.expanded;
    }

    $scope.showEncryptKey = function() {
        return angular.equals($scope.encrypt.type, 'user');
    }
}]);