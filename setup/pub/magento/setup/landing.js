'use strict';
var landing = angular.module('landing', ['ngStorage']);
landing.controller('landingController', [
        '$scope',
        '$location',
        '$localStorage',
        function ($scope, $location, $localStorage) {
            $scope.selectLanguage = function () {
                $localStorage.lang = $scope.modelLanguage;
                window.location = '/setup/' + $scope.modelLanguage + '/index';
            };
        }
    ]
);
