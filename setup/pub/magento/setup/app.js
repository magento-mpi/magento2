'use strict';
angular.module('magentoSetup', ['ui.router'])
    .controller('navigationController', [
        '$scope',
        '$state',
        'navigationService',
        function ($scope, $state, navigationService) {
            $scope.nav = $state.go('navigation');
            navigationService.load(function (data) {
                $scope.nav = data.nav;
            });
        }
    ])
    .factory('navigationService', function ($http) {
        return {
            load: function (callback) {
                $http.get('menu').success(callback);
            }
        }
    })
    .config(function ($stateProvider, $urlRouterProvider) {
        $stateProvider.state('navigation', {
            controller: "navigationController"
        })
    });
