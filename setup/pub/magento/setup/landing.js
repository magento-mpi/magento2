/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('landing', ['ngStorage'])
    .controller('landingController', [
        '$scope',
        '$location',
        '$localStorage',
        function ($scope, $location, $localStorage) {
            $scope.selectLanguage = function () {
                $localStorage.lang = $scope.modelLanguage;
                window.location = '/setup/' + $scope.modelLanguage + '/index';
            };
        }
    ]);
