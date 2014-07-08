'use strict';
var webConfiguration = angular.module('web-configuration', ['ngStorage']);
webConfiguration.controller('webConfigurationController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.config = {
        address: {
            web: 'http://www.example.com/',
            admin: 'admin'
        },
        https: {
            front: true,
            admin: true
        },
        rewrites: {
            allowed: true
        },
        encrypt: {
            key: '',
            type: 'magento'
        },
        advanced: {
            expanded: false
        }
    };

    if ($localStorage.config) {
        $scope.config = $localStorage.config;
    }

    $scope.$on('nextState', function () {
        $localStorage.config = $scope.config;
    });

    $scope.updateOnExpand = function(obj) {
        obj.expanded = !obj.expanded;
    };

    $scope.showEncryptKey = function() {
        return angular.equals($scope.config.encrypt.type, 'user');
    }
}]);