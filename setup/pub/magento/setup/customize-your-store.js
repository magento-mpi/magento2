/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('customize-your-store', ['ngStorage'])
    .controller('customizeYourStoreController', ['$scope', '$localStorage', 'Storage', function ($scope, $localStorage, Storage) {
        $scope.store = {
            timezone: 'America/Los_Angeles',
            currency: 'USD',
            language: 'en_US',
            useSampleData: false
        };

        if ($localStorage.store) {
            $scope.store = $localStorage.store;
        }

        $scope.$on('nextState', function () {
            $localStorage.store = $scope.store;
            Storage.store = $scope.store;
        });
    }]);
