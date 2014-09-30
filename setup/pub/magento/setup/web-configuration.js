/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

'use strict';
angular.module('web-configuration', ['ngStorage'])
    .controller('webConfigurationController', ['$scope', '$state', '$localStorage', function ($scope, $state, $localStorage) {
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
                key: null,
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
        }

        $scope.$watch('config.encrypt.type', function() {
            if(angular.equals($scope.config.encrypt.type, 'magento')){
                $scope.config.encrypt.key = null;
            }
        });

        $scope.showEncryptKey = function() {
            return angular.equals($scope.config.encrypt.type, 'user');
        }

        // Listens on form validate event, dispatched by parent controller
        $scope.$on('validate-' + $state.current.id, function() {
            $scope.validate();
        });

        // Dispatch 'validation-response' event to parent controller
        $scope.validate = function() {
            if ($scope.webconfig.$valid) {
                $scope.$emit('validation-response', true);
            } else {
                $scope.$emit('validation-response', false);
                $scope.webconfig.submitted = true;
            }
        }

        // Update 'submitted' flag
        $scope.$watch(function() { return $scope.webconfig.$valid }, function(valid) {
            if (valid) {
                $scope.webconfig.submitted = false;
            }
        });
    }]);