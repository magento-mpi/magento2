'use strict';
var app = angular.module(
    'magentoSetup',
    [
        'ui.router',
        'ui.bootstrap',
        'main',
        'landing',
        'readiness-check',
        'add-database',
        'web-configuration',
        'customize-your-store'
    ]);

app.controller('createAdminAccountController', ['$scope', function ($scope) {
        $scope.admin = {};
        $scope.admin.passwordStatus = {
            class: 'none',
            label: 'None'
        };
        $scope.passwordStatusChange = function () {
            var p = $scope.admin.password;
            if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/) && p.match(/[!@#$%^*()_\/\\\-\+=]+/)) {
                $scope.admin.passwordStatus.class = 'strong';
                $scope.admin.passwordStatus.label = 'Strong';
            } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/)) {
                $scope.admin.passwordStatus.class = 'good';
                $scope.admin.passwordStatus.label = 'Good';
            } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/)) {
                $scope.admin.passwordStatus.class = 'weak';
                $scope.admin.passwordStatus.label = 'Weak';
            } else if (p.length > 6) {
                $scope.admin.passwordStatus.class = 'to-short';
                $scope.admin.passwordStatus.label = 'To Short';
            } else {
                $scope.admin.passwordStatus.class = 'none';
                $scope.admin.passwordStatus.label = 'None';
            }
            console.log(p, $scope.admin.passwordStatus);
        }
    }])
//    .directive('checkStrength', function () {
//        return {
//            replace: false,
//            restrict: 'E',
//            link: function (scope, iElement, iAttributes) {
//                console.log(scope, iElement, iAttributes);
//            }
//        };
//    })
    .controller('installController', ['$scope', function ($scope) {
        console.log('installController');
    }])
    .config(function ($stateProvider) {
        app.stateProvider = $stateProvider;
    })
    .config(function($provide) {
        $provide.decorator('$state', function($delegate, $stateParams) {
            $delegate.forceReload = function() {
                return $delegate.go($delegate.current, $stateParams, {
                    reload: true,
                    inherit: false,
                    notify: true
                });
            };
            return $delegate;
        });
    }).run(function ($rootScope, $state) {
        $rootScope.$state = $state;
    });
