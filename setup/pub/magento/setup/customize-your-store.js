'use strict';
var customizeYourStore = angular.module('customize-your-store', []);
customizeYourStore.controller('customizeYourStoreController', ['$scope', function ($scope) {

    $scope.options = {
        timezone: ['Central Standard Time'],
        currency: ['US Dollars'],
        language: ['English (United States)']
    };

    $scope.advanced = {
        expanded: false
    };

    $scope.updateOnExpand = function(obj) {
        obj.expanded = !obj.expanded;
    }
}]);
