'use strict';
var customizeYourStore = angular.module('customize-your-store', []);
customizeYourStore.controller('customizeYourStoreController', ['$scope', function ($scope) {

    $scope.timezone = 'America/Los_Angeles';
    $scope.currency = 'USD';
    $scope.language = 'en_US';

    $scope.useSampledata = false;

}]);
