'use strict';
angular.module('create-admin-account', ['ngStorage'])
    .controller('createAdminAccountController', ['$scope', '$state', '$localStorage', function ($scope, $state, $localStorage) {
        $scope.admin = {
            'passwordStatus': {
                class: 'none',
                label: 'None'
            }
        };

        $scope.passwordStatusChange = function () {
            if (angular.isUndefined($scope.admin.password)) {
                return;
            }
            var p = $scope.admin.password;
            if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/) && p.match(/[!@#$%^*()_\/\\\-\+=]+/)) {
                $scope.admin.passwordStatus.class = 'strong';
                $scope.admin.passwordStatus.label = 'Strong';
            } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-z]+/) && p.match(/[A-Z]+/)) {
                $scope.admin.passwordStatus.class = 'good';
                $scope.admin.passwordStatus.label = 'Good';
            } else if (p.length > 6 && p.match(/[\d]+/) && p.match(/[a-zA-Z]+/)) {
                $scope.admin.passwordStatus.class = 'weak';
                $scope.admin.passwordStatus.label = 'Weak';
            } else if (p.length > 6) {
                $scope.admin.passwordStatus.class = 'to-short';
                $scope.admin.passwordStatus.label = 'To Short';
            } else {
                $scope.admin.passwordStatus.class = 'none';
                $scope.admin.passwordStatus.label = 'None';
            }
        };

        if ($localStorage.admin) {
            $scope.admin = $localStorage.admin;
        }

        $scope.$on('nextState', function () {
            $localStorage.admin = $scope.admin;
        });

        // Listens on form validate event, dispatched by parent controller
        $scope.$on('validate-' + $state.current.id, function() {
            $scope.validate();
        });

        // Dispatch 'validation-response' event to parent controller
        $scope.validate = function() {
            if ($scope.account.$valid) {
                $scope.$emit('validation-response', true);
            } else {
                $scope.$emit('validation-response', false);
                $scope.account.submitted = true;
            }
        }

        // Update 'submitted' flag
        $scope.$watch(function() { return $scope.account.$valid }, function(valid) {
            if (valid) {
                $scope.account.submitted = false;
            }
        });
    }])
    .directive('confirmPassword', function() {
        return {
            require: 'ngModel',
            restrict: 'A',
            link: function (scope, elem, attrs, ctrl) {
                scope.$watch(function () {
                    return scope.$eval(attrs.confirmPassword) === ctrl.$modelValue;
                }, function (value) {
                    ctrl.$setValidity('confirmPassword', value);
                });

                ctrl.$parsers.push(function (value) {
                    if (angular.isUndefined(value) || value === '') {
                        ctrl.$setValidity('confirmPassword', true);
                        return value;
                    }
                    var validated = value === scope.confirmPassword;
                    ctrl.$setValidity('confirmPassword', validated);
                    return value;
                });
            }
        };
    });
