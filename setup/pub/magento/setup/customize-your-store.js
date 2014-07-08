'use strict';
var customizeYourStore = angular.module('customize-your-store', ['ngStorage']);
customizeYourStore.controller('customizeYourStoreController', ['$scope', '$localStorage', function ($scope, $localStorage) {
    $scope.cys = {};

    $scope.cys.timezone = 'America/Los_Angeles';
    $scope.cys.currency = 'USD';
    $scope.cys.language = 'en_US';

    $scope.cys.useSampledata = false;

    if ($localStorage.cys) {
        $scope.cys = $localStorage.cys;
    }

    $scope.$on('nextState', function () {
        $localStorage.cys = $scope.cys;
    })

}]);
