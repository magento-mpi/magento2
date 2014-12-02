/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('success', ['ngStorage'])
    .controller('successController', ['$scope', '$localStorage', 'Storage', function ($scope, $localStorage, Storage) {
        $scope.db     = Storage.db;
        $scope.admin  = Storage.admin;
        $scope.config = Storage.config;
        $scope.messages = Storage.messages;
    }]);