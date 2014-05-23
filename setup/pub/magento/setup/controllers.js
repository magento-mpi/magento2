'use strict';
angular.module('magentoSetup', ['ngSanitize'])
    .service('License', function ($http) {
        return {
            load: function() {
                return $http.get('license');
            }
        }
    })
    .controller('navigation', function ($scope) {

    })
    .controller('continue', function ($scope) {
        $scope.text = 'Continue';
    })
    .controller('main', function ($scope, License) {
        var request = License.load();
        request.success(function (data) {
            $scope.content = data;
        });
    });