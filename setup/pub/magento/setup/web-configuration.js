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
                web: '',
                admin: 'admin'
            },
            https: {
                front: false,
                admin: false,
                text: ''
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

        $scope.showHttpsField = function() {
            return ($scope.config.https.front || $scope.config.https.admin);
        }

        $scope.addSlash = function() {
            if (angular.isUndefined($scope.config.address.web)) {
                return;
            }

            var p = $scope.config.address.web;
            if (p.length > 1) {
                var lastChar = p.substr(-1);
                if (lastChar != '/') {
                    $scope.config.address.web = p + '/';
                }
            }
        };

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
    }])
    .directive('validateHttpurl', function() {
        return{
            require: "ngModel",
            link: function(scope, elm, attrs, ctrl){
                var validator = function(value){
                    var isValid  = value.match(/^(http):\/\/(([a-zA-Z0-9$\-_.+!*'(),;:&=]|%[0-9a-fA-F]{2})+@)?(((25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])){3})|localhost|([a-zA-Z0-9\-\u00C0-\u017F]+\.)+([a-zA-Z]{2,}))(:[0-9]+)?(\/(([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*(\/([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*)*)?(\?([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?(\#([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?)?$/);
                    ctrl.$setValidity('validateHttpurl', isValid);
                    return value;
                };
                ctrl.$parsers.unshift(validator);
                ctrl.$formatters.unshift(validator);
            }
        };
    })
    .directive('validateHttpsurl', function() {
        return{
            require: "ngModel",
            link: function(scope, elm, attrs, ctrl){
                var validator = function(value){
                    var isValid  = value.match(/^(https):\/\/(([a-zA-Z0-9$\-_.+!*'(),;:&=]|%[0-9a-fA-F]{2})+@)?(((25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])){3})|localhost|([a-zA-Z0-9\-\u00C0-\u017F]+\.)+([a-zA-Z]{2,}))(:[0-9]+)?(\/(([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*(\/([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*)*)?(\?([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?(\#([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?)?$/);
                    ctrl.$setValidity('validateHttpsurl', isValid);
                    return value;
                };
                ctrl.$parsers.unshift(validator);
                ctrl.$formatters.unshift(validator);
            }
        };
    });